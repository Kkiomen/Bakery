<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'nazwa' => 'Chleby',
                'opis' => 'Różne rodzaje chlebów - żytnie, pszenne, mieszane',
                'aktywny' => true,
            ],
            [
                'nazwa' => 'Bułki',
                'opis' => 'Bułki śniadaniowe, kajzerki, bagietki',
                'aktywny' => true,
            ],
            [
                'nazwa' => 'Ciasta i desery',
                'opis' => 'Ciasta, torty, serniki, desery',
                'aktywny' => true,
            ],
            [
                'nazwa' => 'Pieczywo słodkie',
                'opis' => 'Drożdżówki, pączki, rogaliki',
                'aktywny' => true,
            ],
            [
                'nazwa' => 'Pieczywo specjalne',
                'opis' => 'Pieczywo bezglutenowe, dietetyczne, wegańskie',
                'aktywny' => true,
            ],
            [
                'nazwa' => 'Przekąski',
                'opis' => 'Precelki, krakersy, paluszki',
                'aktywny' => true,
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
