<?php

namespace Database\Seeders\Shop;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductCategoriesForWomenStoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Calçados' => [
                'Anabela',
                'Botas',
                'Chinelos',
                'Mocassins',
                'Rasteiras',
                'Sandálias',
                'Sapatilhas',
                'Scarpins',
                'Tênis'
            ],
            'Roupas' => [
                'Blusas e Camisetas',
                'Calças',
                'Calças Jeans',
                'Camisas',
                'Jaquetas e Casacos',
                'Macacões',
                'Moda Praia',
                'Moletons',
                'Suéteres e Cardigans',
                'Vestidos'
            ],
            'Moda Íntima' => [
                'Bodys Sensuais',
                'Baby Dolls',
                'Calcinhas',
                'Caleçons',
                'Camisetes',
                'Camisolas',
                'Cintas Ligas',
                'Croppeds',
                'Conjuntos',
                'Corpetes',
                'Corselets',
                'Corsets',
                'Espartilhos',
                'Lingeries',
                'Luvas',
                'Meias',
                'Meias Calças',
                'Modeladores',
                'Perneiras',
                'Pijamas e Camisolas',
                'Roupões',
                'Sutiãs'
            ],
            'Beleza e Higiene'  => [
                'Cabelos',
                'Cuidados da Pele',
                'Dermocosméticos',
                'Maquiagens',
                'Perfumes',
            ],
            'Acessórios'  => [
                'Bolsas',
                'Carteiras',
                'Cintos',
                'Mochilas',
                'Óculos',
                'Relógios',
                'Brincos',
                'Anéis',
                'Colares',
                'Pulseiras'
            ],
        ];

        foreach ($categories as $parentName => $subcategories) {
            $categoryId = DB::table('shop_product_categories')->insertGetId([
                'category_id' => null,
                'name'        => $parentName,
                'slug'        => Str::slug($parentName),
                'created_at'  => now(),
                'updated_at'  => now()
            ]);

            foreach ($subcategories as $subcategoryName) {
                DB::table('shop_product_categories')->insert([
                    'category_id' => $categoryId,
                    'name'        => $subcategoryName,
                    'slug'        => Str::slug($subcategoryName),
                    'created_at'  => now(),
                    'updated_at'  => now()
                ]);
            }
        }
    }
}
