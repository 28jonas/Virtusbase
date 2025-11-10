<?php

namespace Database\Seeders;

use App\Models\FoodItem;
use Illuminate\Database\Seeder;

class FoodSeeder extends Seeder
{
    public function run()
    {
        $foodItems = [
            // Ontbijtproducten
            [
                'name' => 'Havermout',
                'serving_size' => 40,
                'serving_unit' => 'g',
                'calories' => 150,
                'protein' => 5,
                'fat' => 3,
                'carbs' => 27,
                'fiber' => 4,
                'sugar' => 1,
                'is_public' => true
            ],
            [
                'name' => 'Griekse yoghurt (naturel)',
                'serving_size' => 150,
                'serving_unit' => 'g',
                'calories' => 130,
                'protein' => 10,
                'fat' => 5,
                'carbs' => 6,
                'sugar' => 4,
                'is_public' => true
            ],
            [
                'name' => 'Volkoren brood',
                'serving_size' => 35,
                'serving_unit' => 'g',
                'calories' => 85,
                'protein' => 4,
                'fat' => 1,
                'carbs' => 15,
                'fiber' => 2,
                'is_public' => true
            ],
            [
                'name' => 'Pindakaas',
                'serving_size' => 15,
                'serving_unit' => 'g',
                'calories' => 90,
                'protein' => 4,
                'fat' => 8,
                'carbs' => 3,
                'is_public' => true
            ],
            [
                'name' => 'Eieren (gekookt)',
                'serving_size' => 1,
                'serving_unit' => 'stuk',
                'calories' => 70,
                'protein' => 6,
                'fat' => 5,
                'carbs' => 0.6,
                'is_public' => true
            ],

            // Lunchproducten
            [
                'name' => 'Kipfilet',
                'serving_size' => 100,
                'serving_unit' => 'g',
                'calories' => 165,
                'protein' => 31,
                'fat' => 3.6,
                'carbs' => 0,
                'is_public' => true
            ],
            [
                'name' => 'Zalm (gerookt)',
                'serving_size' => 100,
                'serving_unit' => 'g',
                'calories' => 206,
                'protein' => 22,
                'fat' => 13,
                'carbs' => 0,
                'is_public' => true
            ],
            [
                'name' => 'Avocado',
                'serving_size' => 50,
                'serving_unit' => 'g',
                'calories' => 80,
                'protein' => 1,
                'fat' => 7,
                'carbs' => 4,
                'fiber' => 3,
                'is_public' => true
            ],
            [
                'name' => 'Bruine rijst (gekookt)',
                'serving_size' => 100,
                'serving_unit' => 'g',
                'calories' => 111,
                'protein' => 2.6,
                'fat' => 0.9,
                'carbs' => 23,
                'fiber' => 1.8,
                'is_public' => true
            ],
            [
                'name' => 'Zoete aardappel (gekookt)',
                'serving_size' => 100,
                'serving_unit' => 'g',
                'calories' => 86,
                'protein' => 1.6,
                'fat' => 0.1,
                'carbs' => 20,
                'fiber' => 3,
                'is_public' => true
            ],

            // Dranken
            [
                'name' => 'Water',
                'serving_size' => 250,
                'serving_unit' => 'ml',
                'calories' => 0,
                'protein' => 0,
                'fat' => 0,
                'carbs' => 0,
                'is_public' => true
            ],
            [
                'name' => 'Koffie (zwart)',
                'serving_size' => 250,
                'serving_unit' => 'ml',
                'calories' => 2,
                'protein' => 0.3,
                'fat' => 0,
                'carbs' => 0,
                'is_public' => true
            ],
            [
                'name' => 'Groene thee',
                'serving_size' => 250,
                'serving_unit' => 'ml',
                'calories' => 2,
                'protein' => 0,
                'fat' => 0,
                'carbs' => 0.5,
                'is_public' => true
            ],
            [
                'name' => 'Halfvolle melk',
                'serving_size' => 250,
                'serving_unit' => 'ml',
                'calories' => 125,
                'protein' => 8,
                'fat' => 5,
                'carbs' => 12,
                'sugar' => 12,
                'is_public' => true
            ],
            [
                'name' => 'Amandelmelk (ongezoet)',
                'serving_size' => 250,
                'serving_unit' => 'ml',
                'calories' => 30,
                'protein' => 1,
                'fat' => 2.5,
                'carbs' => 1,
                'is_public' => true
            ],

            // Snacks
            [
                'name' => 'Amandelen (ongezouten)',
                'serving_size' => 28,
                'serving_unit' => 'g',
                'calories' => 160,
                'protein' => 6,
                'fat' => 14,
                'carbs' => 6,
                'fiber' => 3,
                'is_public' => true
            ],
            [
                'name' => 'Appel',
                'serving_size' => 1,
                'serving_unit' => 'stuk',
                'calories' => 95,
                'protein' => 0.5,
                'fat' => 0.3,
                'carbs' => 25,
                'fiber' => 4,
                'is_public' => true
            ],
            [
                'name' => 'Banaan',
                'serving_size' => 1,
                'serving_unit' => 'stuk',
                'calories' => 105,
                'protein' => 1.3,
                'fat' => 0.4,
                'carbs' => 27,
                'fiber' => 3,
                'is_public' => true
            ],
            [
                'name' => 'Pure chocolade (85%)',
                'serving_size' => 10,
                'serving_unit' => 'g',
                'calories' => 60,
                'protein' => 1,
                'fat' => 5,
                'carbs' => 3,
                'fiber' => 1,
                'is_public' => true
            ],
            [
                'name' => 'Hummus',
                'serving_size' => 30,
                'serving_unit' => 'g',
                'calories' => 50,
                'protein' => 2,
                'fat' => 3,
                'carbs' => 4,
                'fiber' => 1,
                'is_public' => true
            ],

            // Diner
            [
                'name' => 'Kip curry',
                'serving_size' => 1,
                'serving_unit' => 'portie',
                'calories' => 350,
                'protein' => 25,
                'fat' => 12,
                'carbs' => 30,
                'fiber' => 5,
                'is_public' => true
            ],
            [
                'name' => 'Zalmfilet (gebakken)',
                'serving_size' => 150,
                'serving_unit' => 'g',
                'calories' => 280,
                'protein' => 34,
                'fat' => 15,
                'carbs' => 0,
                'is_public' => true
            ],
            [
                'name' => 'Quinoa (gekookt)',
                'serving_size' => 100,
                'serving_unit' => 'g',
                'calories' => 120,
                'protein' => 4,
                'fat' => 2,
                'carbs' => 21,
                'fiber' => 2.8,
                'is_public' => true
            ],
            [
                'name' => 'Broccoli (gestoomd)',
                'serving_size' => 100,
                'serving_unit' => 'g',
                'calories' => 35,
                'protein' => 2.8,
                'fat' => 0.4,
                'carbs' => 7,
                'fiber' => 2.6,
                'is_public' => true
            ],
            [
                'name' => 'Rundvlees (mager)',
                'serving_size' => 100,
                'serving_unit' => 'g',
                'calories' => 250,
                'protein' => 26,
                'fat' => 15,
                'carbs' => 0,
                'is_public' => true
            ]
        ];

        foreach ($foodItems as $item) {
            FoodItem::create($item);
        }
    }
}