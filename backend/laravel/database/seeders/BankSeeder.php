<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $banks = [
                ['name' => 'Mastercard', 'logo' => 'mastercard-logo.svg', 'example' => '5123 4567 8901 2346', 'format' => '16 cijfers (5XXX XXXX XXXX XXXX)'],
                ['name' => 'Visa', 'logo' => 'visa-logo.svg', 'example' => '4123 4567 8901 2345', 'format' => '16 cijfers (4XXX XXXX XXXX XXXX)'],
                ['name' => 'ABN AMRO', 'logo' => 'abn-amro-logo.svg', 'example' => '1234 5678 9012', 'format' => '12 cijfers (vaak Maestro)'],
                ['name' => 'ING', 'logo' => 'ing-logo.svg', 'example' => '1234 5678 9012 3456', 'format' => '16 cijfers'],
                ['name' => 'Rabobank', 'logo' => 'rabobank-logo.svg', 'example' => '1234 5678 9012', 'format' => '12 cijfers (vaak Maestro)'],
                ['name' => 'Bunq', 'logo' => 'bunq-logo.svg', 'example' => '1234 5678 9012 3456', 'format' => '16 cijfers (Mastercard/Visa)'],
                ['name' => 'BNP Paribas Fortis', 'logo' => 'BNP-logo.svg', 'example' => '1234 5678 9012 3456', 'format' => '16 cijfers (Mastercard/Visa)'],
                ['name' => 'KBC', 'logo' => 'KBC-logo.svg', 'example' => '1234 5678 9012 3456 7', 'format' => '17 cijfers'],
                ['name' => 'Belfius', 'logo' => 'Belfius-logo.svg', 'example' => '1234 5678 9012 3', 'format' => '15 cijfers (American Express)'],
                ['name' => 'Argenta', 'logo' => 'Argenta-logo.svg', 'example' => '1234 567890 12', 'format' => '4-6-2 formaat'],
                ['name' => 'Axa', 'logo' => 'Axa-logo.svg', 'example' => '1234 5678 9012 3456', 'format' => '16 cijfers'],
                ['name' => 'Crelan', 'logo' => 'Crelan-logo.svg', 'example' => '1234 5678 9012', 'format' => '12 cijfers'],
                ['name' => 'BeoBank', 'logo' => 'BeoBank-logo.svg', 'example' => '1234 5678 9012 3456', 'format' => '16 cijfers'],
            ];
        /*];*/

        foreach ($banks as $bank) {
            Bank::create($bank);
        }
    }
}