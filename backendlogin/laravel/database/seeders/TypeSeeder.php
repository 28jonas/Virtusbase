<?php

namespace Database\Seeders;

use Illuminate\Container\Attributes\DB;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Type;


class TypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Type::create(['name' => 'Admin']);
        Type::create(['name' => 'Wholesale']);
        Type::create(['name' => 'Reseller']);
        Type::create(['name' => 'Customer']);
    }
}
