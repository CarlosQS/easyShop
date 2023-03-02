<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TiendaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiendas = \App\Models\Tienda::factory()->count(10)->create();
        $productos = \App\Models\Producto::factory()->count(10)->create();

        foreach ($tiendas as $tienda) {
            $tienda->productos()->attach($productos->random(5));
        }
    }
}
