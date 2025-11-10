<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\API\BaseApiController;
use App\Http\Controllers\Controller;
use App\Models\Card;
use App\Models\Bank;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class CardController extends BaseApiController
{
    public function index()
    {
        $userId = request()->user['id'];

        $cards = Card::with(['bank', 'goals', 'expenses', 'incomes'])
            ->where('profile_id', $userId)
            ->get();

        $totalCardBalance = $cards->sum('balance');
        $totalTransfers = $cards->sum(function ($card) {
            return $card->goals->sum('transfer');
        });

        $totalBalance = $totalCardBalance + $totalTransfers;

        return response()->json([
            'cards' => $cards,
            'totalBalance' => $totalBalance,
            'totalTransfers' => $totalTransfers
        ]);
    }

    public function banks()
    {
        $banks = Bank::all();
        return response()->json($banks);
    }

    public function store(Request $request)
    {
        Log::info('Store card request data:', $request->all());
        $request->validate([
            'card_number' => 'required',
            'expiry_date' => 'required|date_format:m/y|after_or_equal:' . date('m/y'),
            'balance' => 'required|numeric|min:0',
            'bank_id' => 'required|exists:banks,id',
        ]);

        // Manual card number validation
        $this->validateCardNumber($request->card_number, $request->bank_id);
        //Log::info('Validated card number: ' . $request->card_number);
        DB::beginTransaction();
        try {
            $cleanNumber = preg_replace('/\s+/', '', $request->card_number);
            //Log::info('Cleaned card number: ' . $cleanNumber);
            $card = Card::create([
                'profile_id' => (int) $request->user['id'],
                'last_four' => substr($cleanNumber, -4),
                'card_number' => Crypt::encryptString($cleanNumber),
                'expiry_date' => $request->expiry_date,
                'balance' => $request->balance,
                'bank_id' => $request->bank_id,
            ]);

            Log::info('Created card: ' . json_encode($card));

            DB::commit();

            return response()->json($card, 201);
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Fout bij aanmaken kaart: ' . $e->getMessage());
            return response()->json(['error' => 'Fout bij aanmaken kaart. Probeer opnieuw.'], 500);
        }
    }

    public function transfer(Request $request)
    {
        $request->validate([
            'sourceCardId' => 'required|different:destinationCardId',
            'destinationCardId' => 'required|different:sourceCardId',
            'transferAmount' => 'required|numeric|min:0.01',
        ]);

        $sourceCard = Card::find($request->sourceCardId);
        $destinationCard = Card::find($request->destinationCardId);

        // Check if cards belong to user
        if ($sourceCard->user_id !== auth()->id() || $destinationCard->user_id !== auth()->id()) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        // Saldo check
        if ($sourceCard->balance < $request->transferAmount) {
            return response()->json(['error' => 'Onvoldoende saldo op de bronkaart.'], 422);
        }

        DB::beginTransaction();
        try {
            $sourceCard->balance -= $request->transferAmount;
            $destinationCard->balance += $request->transferAmount;

            $sourceCard->save();
            $destinationCard->save();

            DB::commit();

            return response()->json(['message' => 'Transfer successful']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Er is een fout opgetreden bij de overdracht. Probeer opnieuw.'], 500);
        }
    }

    private function validateCardNumber($cardNumber, $bankId)
    {
        $bank = Bank::find($bankId);

        if (!$bank) {
            throw ValidationException::withMessages([
                'bank_id' => 'Selecteer eerst een bank'
            ]);
        }

        $cleanNumber = preg_replace('/\s+/', '', $cardNumber);

        // KBC validation
        if (in_array($bank->name, ['KBC'])) {
            if (!preg_match('/^\d{17}$/', $cleanNumber)) {
                throw ValidationException::withMessages([
                    'card_number' => 'Voer een geldig 17-cijferig kaartnummer in'
                ]);
            }
        }

        // Other banks validation
        if (in_array($bank->name, ['Mastercard', 'Visa', 'ING', 'Bunq', 'Axa', 'BeoBank', 'BNP Paribas Fortis'])) {
            if (!preg_match('/^\d{16}$/', $cleanNumber)) {
                throw ValidationException::withMessages([
                    'card_number' => 'Voer een geldig 16-cijferig kaartnummer in'
                ]);
            }

            // Bank-specific prefix checks
            if ($bank->name === 'Mastercard' && !preg_match('/^5[1-5]/', $cleanNumber)) {
                throw ValidationException::withMessages([
                    'card_number' => 'Mastercard nummers beginnen met 51-55'
                ]);
            }

            if ($bank->name === 'Visa' && !preg_match('/^4/', $cleanNumber)) {
                throw ValidationException::withMessages([
                    'card_number' => 'Visa nummers beginnen met 4'
                ]);
            }
        }

        // Luhn check
        if (in_array($bank->name, ['Mastercard', 'Visa', 'Belfius', 'KBC', 'BNP Paribas Fortis'])) {
            if (!$this->passesLuhnCheck($cleanNumber)) {
                throw ValidationException::withMessages([
                    'card_number' => 'Ongeldig kaartnummer (Luhn check gefaald)'
                ]);
            }
        }
    }

    private function passesLuhnCheck($number)
    {
        $number = preg_replace('/\s+/', '', $number);
        $sum = 0;
        $length = strlen($number);

        for ($i = 0; $i < $length; $i++) {
            $digit = (int) $number[$length - $i - 1];
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