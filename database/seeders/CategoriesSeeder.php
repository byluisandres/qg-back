<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Category::updateOrCreate([
            'name' => 'Gasto de compras',
            'color' => '#ffcdd2'
        ]);
        Category::updateOrCreate([
            'name' => 'Gasto de alquiler',
            'color' => '#f8bbd0 '
        ]);
        Category::updateOrCreate([
            'name' => 'Gasto de suministros',
            'color' => '#e1bee7'
        ]);
        Category::updateOrCreate([
            'name' => 'Gasto de servicios externos',
            'color' => '#d1c4e9'
        ]);
        Category::updateOrCreate([
            'name' => 'Gasto de otros tipo',
            'color' => '#c5cae9'
        ]);
        Category::updateOrCreate([
            'name' => 'Gasto de impuestos y tasas',
            'color' => '#bbdefb'
        ]);
        Category::updateOrCreate([
            'name' => 'Gasto de personal',
            'color' => '#b3e5fc'
        ]);
        Category::updateOrCreate([
            'name' => 'Gastos bancarios y similares',
            'color' => '#b2ebf2'
        ]);
        Category::updateOrCreate([
            'name' => 'Gasto de amortizaciones',
            'color' => '#b2dfdb'
        ]);
        Category::updateOrCreate([
            'name' => 'Gastos extraordinarios',
            'color' => '#c8e6c9'
        ]);
    }
}
