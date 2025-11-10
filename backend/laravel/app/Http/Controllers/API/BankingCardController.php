<?php

namespace App\Http\Controllers\API;

use App\Models\BankingCard;
use App\Models\Bank;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\API\BaseApiController;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BankingCardController extends BaseApiController
{
    public function index()
    {
        $userId = request()->user['id'];

        $cards = BankingCard::where('profile_id', $userId)
                    ->with('bank')
                    ->get();

        $totalBalance = $cards->sum('balance');
        
        // Bereken totaal van overboekingen uit goals (indien aanwezig)
        $totalTransfers = $cards->map(function($card) {
            return $card->goals ? $card->goals->sum('transfer') : 0;
        })->sum();

        $totalBalanceWithTransfers = $totalBalance + $totalTransfers;

        $response = [
            'cards' => $cards,
            'stats' => [
                'total_balance' => $totalBalance,
                'total_transfers' => $totalTransfers,
                'total_balance_with_transfers' => $totalBalanceWithTransfers,
                'card_count' => $cards->count()
            ]
        ];

        return $this->sendResponse($response, 'Cards retrieved successfully.');
    }

    public function getBanks()
    {
        $banks = Bank::all();
        return $this->sendResponse($banks, 'Banks retrieved successfully.');
    }

    public function store(Request $request)
    {
        Log::info('Store card request data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'card_number' => 'required|string',
            'expiry_date' => 'required|date_format:m/y|after_or_equal:' . date('m/y'),
            'balance' => 'required|numeric|min:0',
            'bank_id' => 'required|exists:banks,id',
        ]);

        if ($validator->fails()) {
            Log::error('Validation Error.', $validator->errors()->toArray());
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $userId = request()->user['id'];

        // Handmatige kaartnummer validatie
        $validationError = $this->validateCardNumberManual($request->card_number, $request->bank_id);
        if ($validationError) {
            return $this->sendError('Validation Error.', ['card_number' => [$validationError]], 422);
        }

        try {
            // Normaliseer kaartnummer (verwijder spaties)
            $cleanCardNumber = preg_replace('/\s+/', '', $request->card_number);

            // Maak nieuwe kaart aan
            $card = BankingCard::create([
                'last_four' => substr($cleanCardNumber, -4),
                'card_number' => Crypt::encryptString($cleanCardNumber),
                'expiry_date' => $request->expiry_date,
                'balance' => $request->balance,
                'bank_id' => $request->bank_id,
                'profile_id' => $userId,
            ]);

            Log::info('Card created successfully', ['card_id' => $card->id]);

            return $this->sendResponse($card, 'Card created successfully.', 201);

        } catch (\Exception $e) {
            Log::error('Error creating card: ' . $e->getMessage());
            return $this->sendError('Failed to create card. Please try again.', [], 500);
        }
    }

    public function show(BankingCard $card)
    {
        // Authorisatie: controleer of card van de gebruiker is
        $userId = request()->user['id'];
        if ($card->profile_id != $userId) {
            return $this->sendError('Unauthorized access to card.', [], 403);
        }

        $card->load('bank');

        return $this->sendResponse($card, 'Card retrieved successfully.');
    }

    public function update(Request $request, BankingCard $card)
    {
        Log::info('Update card request data:', $request->all());
        
        // Authorisatie: controleer of card van de gebruiker is
        $userId = request()->user['id'];
        if ($card->profile_id != $userId) {
            return $this->sendError('Unauthorized access to card.', [], 403);
        }

        $validator = Validator::make($request->all(), [
            'balance' => 'required|numeric|min:0',
            'expiry_date' => 'required|date_format:m/y|after_or_equal:' . date('m/y'),
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $card->update([
            'balance' => $request->balance,
            'expiry_date' => $request->expiry_date,
        ]);

        return $this->sendResponse($card, 'Card updated successfully.');
    }

    public function destroy(BankingCard $card)
    {
        // Authorisatie: controleer of card van de gebruiker is
        $userId = request()->user['id'];
        if ($card->profile_id != $userId) {
            return $this->sendError('Unauthorized access to card.', [], 403);
        }

        $card->delete();

        return $this->sendResponse(null, 'Card deleted successfully.');
    }

    public function transfer(Request $request)
    {
        Log::info('Transfer request data:', $request->all());
        
        $validator = Validator::make($request->all(), [
            'source_card_id' => 'required|exists:bankingcards,id',
            'destination_card_id' => 'required|exists:bankingcards,id|different:source_card_id',
            'amount' => 'required|numeric|min:0.01',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error.', $validator->errors(), 422);
        }

        $userId = request()->user['id'];

        // Controleer of beide kaarten van de gebruiker zijn
        $sourceCard = BankingCard::where('id', $request->source_card_id)
                         ->where('profile_id', $userId)
                         ->first();

        $destinationCard = BankingCard::where('id', $request->destination_card_id)
                              ->where('profile_id', $userId)
                              ->first();

        if (!$sourceCard || !$destinationCard) {
            return $this->sendError('Unauthorized access to one or both cards.', [], 403);
        }

        // Saldo check
        if ($sourceCard->balance < $request->amount) {
            return $this->sendError('Insufficient balance on source card.', [], 422);
        }

        // Gebruik database transactie voor data-integriteit
        DB::beginTransaction();

        try {
            // Pas saldo's aan
            $sourceCard->balance -= $request->amount;
            $destinationCard->balance += $request->amount;

            $sourceCard->save();
            $destinationCard->save();

            DB::commit();

            // Laad bank relaties voor response
            $sourceCard->load('bank');
            $destinationCard->load('bank');

            $response = [
                'source_card' => $sourceCard,
                'destination_card' => $destinationCard,
                'amount' => $request->amount,
                'transfer_date' => now()->toDateTimeString()
            ];

            return $this->sendResponse($response, 'Transfer completed successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Transfer error: ' . $e->getMessage());
            return $this->sendError('An error occurred during the transfer. Please try again.', [], 500);
        }
    }

    public function getStats()
    {
        $userId = request()->user['id'];

        $cards = BankingCard::where('profile_id', $userId)->get();
        
        $totalBalance = $cards->sum('balance');
        $totalTransfers = $cards->map(function($card) {
            return $card->goals ? $card->goals->sum('transfer') : 0;
        })->sum();

        $stats = [
            'total_balance' => $totalBalance,
            'total_transfers' => $totalTransfers,
            'total_balance_with_transfers' => $totalBalance + $totalTransfers,
            'card_count' => $cards->count(),
            'banks_count' => $cards->groupBy('bank_id')->count()
        ];

        return $this->sendResponse($stats, 'Card stats retrieved successfully.');
    }

    /**
     * Handmatige validatie van kaartnummer op basis van bankregels
     */
    private function validateCardNumberManual($cardNumber, $bankId)
    {
        $bank = Bank::find($bankId);

        if (!$bank) {
            return 'Please select a bank first';
        }

        // Verwijder spaties voor validatie
        $cleanNumber = preg_replace('/\s+/', '', $cardNumber);

        // Valideer op basis van banktype 17 cijfers (KBC)
        if (in_array($bank->name, ['KBC'])) {
            if (!preg_match('/^\d{17}$/', $cleanNumber)) {
                return 'Please enter a valid 17-digit card number';
            }
        }

        // Valideer op basis van banktype 16 cijfers
        if (in_array($bank->name, ['Mastercard', 'Visa', 'ING', 'Bunq', 'Axa', 'BeoBank', 'BNP Paribas Fortis'])) {
            if (!preg_match('/^\d{16}$/', $cleanNumber)) {
                return 'Please enter a valid 16-digit card number';
            }

            // Bank-specifieke prefix checks
            if ($bank->name === 'Mastercard' && !preg_match('/^5[1-5]/', $cleanNumber)) {
                return 'Mastercard numbers start with 51-55';
            }

            if ($bank->name === 'Visa' && !preg_match('/^4/', $cleanNumber)) {
                return 'Visa numbers start with 4';
            }

            // Specifiek voor BNP Paribas Fortis
            if ($bank->name === 'BNP Paribas Fortis' && !preg_match('/^(4|5[1-5])/', $cleanNumber)) {
                return 'BNP Paribas Fortis card numbers must start with 4 (Visa) or 51-55 (Mastercard)';
            }
        }

        // Luhn algoritme voor creditcard validatie
        if (in_array($bank->name, ['Mastercard', 'Visa', 'Belfius', 'KBC', 'BNP Paribas Fortis'])) {
            if (!$this->passesLuhnCheck($cleanNumber)) {
                return 'Invalid card number (Luhn check failed)';
            }
        }

        return null; // Geen fouten
    }

    /**
     * Voer Luhn check uit voor kaartnummer validatie
     */
    private function passesLuhnCheck($number)
    {
        $number = preg_replace('/\s+/', '', $number);
        $sum = 0;
        $length = strlen($number);

        for ($i = 0; $i < $length; $i++) {
            $digit = (int)$number[$length - $i - 1];
            if ($i % 2 === 1) {
                $digit *= 2;
                if ($digit > 9) {
                    $digit -= 9;
                }
            }
            $sum += $digit;
        }

        return $sum % 10 === 0;
    }
}