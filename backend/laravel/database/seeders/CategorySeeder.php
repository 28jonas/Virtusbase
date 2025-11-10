<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        /*$categories = ['Dairy','Meat','Vegetables','Fruits','Cereals',];
        foreach ($categories as $categoryName) {
            Category::firstOrCreate(['name' => $categoryName]);
        }*/
        $habitCategories = [
            ['name' => 'Sport', 'color' => '#10b981'],      // green-500
            ['name' => 'School', 'color' => '#eab308'],     // yellow-500
            ['name' => 'Work', 'color' => '#93c5fd'],      // blue-300
            ['name' => 'Health', 'color' => '#dc2626'],     // red-600
        ];

        $shopDepartments = [
            ['name' => 'Vegetables', 'color' => '#4285f4'], // Behoud origineel (blauw)
            ['name' => 'Fruit', 'color' => '#5842f4'],      // Behoud origineel (paars-blauw)
            ['name' => 'Dairy', 'color' => '#b142f4'],      // Behoud origineel (lichtpaars)
            ['name' => 'Meat', 'color' => '#ef4444'],       // Rood (ipv #f442de - past beter bij vlees)
            ['name' => 'Fish', 'color' => '#3b82f6'],       // blue-500
            ['name' => 'Bakery', 'color' => '#f59e0b'],     // amber-500
            ['name' => 'Dry goods', 'color' => '#fb923c'],  // orange-400
            ['name' => 'Canned goods', 'color' => '#a855f7'], // purple-500
            ['name' => 'Frozen foods', 'color' => '#06b6d4'], // cyan-500
            ['name' => 'Beverages', 'color' => '#6366f1'],   // indigo-500
        ];

        $eventCategories = [
            ['name' => 'Work', 'color' => '#2563eb'],          // Blauw – professioneel, zakelijk
            ['name' => 'Birthday', 'color' => '#facc15'],      // Geel – feestelijk, vrolijk
            ['name' => 'Holiday', 'color' => '#10b981'],       // Groen – ontspanning, natuur
            ['name' => 'Medical', 'color' => '#ef4444'],       // Rood – urgentie, gezondheid
            ['name' => 'Sport', 'color' => '#f97316'],         // Oranje – energie, actie
            ['name' => 'Family', 'color' => '#8b5cf6'],        // Paars – warmte, verbondenheid
            ['name' => 'Travel', 'color' => '#0ea5e9'],        // Lichtblauw – avontuur, lucht
            ['name' => 'Study', 'color' => '#64748b'],         // Grijs-blauw – focus, rust
            ['name' => 'Appointment', 'color' => '#d946ef'],   // Roze-paars – opvallend, belangrijk
            ['name' => 'Other', 'color' => '#6b7280'],         // Neutraal grijs – voor alles wat anders is
        ];

        $financialgoalsCategories = [
            ['name' => 'Vacation', 'color' => '#3b82f6'],        // helder blauw (ontspanning, lucht)
            ['name' => 'Renovation', 'color' => '#f59e0b'],      // amber (bouw, hout, gereedschap)
            ['name' => 'Education', 'color' => '#6366f1'],       // indigo (kennis, stabiliteit)
            ['name' => 'Car', 'color' => '#6b7280'],             // grijs (metaal, technologie)
            ['name' => 'Emergency Fund', 'color' => '#ef4444'],  // rood (dringend, opvallend)
            ['name' => 'Other', 'color' => '#10b981'],           // groen (neutraal, algemeen positief)
        ];


        $expenseCategories = [
            ['name' => 'Groceries', 'color' => '#10b981'],  // green-500
            ['name' => 'Restaurants', 'color' => '#eab308'], // yellow-500
            ['name' => 'Clothing', 'color' => '#93c5fd'],   // blue-300
            ['name' => 'Entertainment', 'color' => '#dc2626'], // red-600
            ['name' => 'Healthcare', 'color' => '#3b82f6'],  // blue-500
        ];

        $incomeCategories = [
            ['name' => 'Salary', 'color' => '#10b981'],     // green-500
            ['name' => 'Gifts', 'color' => '#eab308'],      // yellow-500
            ['name' => 'Investments', 'color' => '#93c5fd'], // blue-300
            ['name' => 'Rents', 'color' => '#dc2626'],      // red-600
            ['name' => 'Flexi job', 'color' => '#3b82f6'],  // blue-500
        ];

        foreach ($habitCategories as $category) {
            Category::create([
                'name' => $category['name'],
                'type' => 'habit_category',
                'color' => $category['color'],
            ]);
        }

        foreach ($shopDepartments as $department) {
            Category::create([
                'name' => $department['name'],
                'type' => 'shop_department',
                'color' => $department['color'],
            ]);
        }

        foreach ($eventCategories as $category) {
            Category::create([
                'name' => $category['name'],
                'type' => 'event_category',
                'color' => $category['color'],
            ]);
        }

        foreach ($financialgoalsCategories as $category) {
            Category::create([
                'name' => $category['name'],
                'type' => 'financialgoal_category',
                'color' => $category['color'],
            ]);
        }

        foreach ($expenseCategories as $category) {
            Category::create([
                'name' => $category['name'],
                'type' => 'expense_category',
                'color' => $category['color'],
            ]);
        }

        foreach ($incomeCategories as $category) {
            Category::create([
                'name' => $category['name'],
                'type' => 'income_category',
                'color' => $category['color'],
            ]);
        }
    }
}