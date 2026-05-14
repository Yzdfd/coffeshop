<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class MenuSeeder extends Seeder
{
    public function run()
    {
        // ambil kategori
        $categories = $this->db
            ->table('categories')
            ->get()
            ->getResultArray();

        $catMap = [];

        foreach ($categories as $c) {
            $catMap[$c['name']] = $c['id'];
        }

        $menus = [

            // COFFEE
            [
                'category_id' => $catMap['Coffee'],
                'name'        => 'Latte',
                'description' => 'Classic latte',
                'price'       => 28000,
                'hpp'         => 12000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Coffee'],
                'name'        => 'Hazelnut Latte',
                'description' => 'Latte with hazelnut syrup',
                'price'       => 32000,
                'hpp'         => 14000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Coffee'],
                'name'        => 'Vanilla Latte',
                'description' => 'Latte with vanilla syrup',
                'price'       => 32000,
                'hpp'         => 14000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Coffee'],
                'name'        => 'Creamy Aren Latte',
                'description' => 'Latte gula aren creamy',
                'price'       => 33000,
                'hpp'         => 15000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Coffee'],
                'name'        => 'Caramel Latte',
                'description' => 'Latte caramel',
                'price'       => 34000,
                'hpp'         => 15000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Coffee'],
                'name'        => 'Cappuccino',
                'description' => 'Classic cappuccino',
                'price'       => 30000,
                'hpp'         => 13000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Coffee'],
                'name'        => 'Caramel Macchiato',
                'description' => 'Macchiato caramel',
                'price'       => 35000,
                'hpp'         => 16000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Coffee'],
                'name'        => 'Americano',
                'description' => 'Espresso with water',
                'price'       => 25000,
                'hpp'         => 10000,
                'status'      => 'available',
            ],

            // NON COFFEE
            [
                'category_id' => $catMap['Non Coffee'],
                'name'        => 'Matcha Latte',
                'description' => 'Premium matcha latte',
                'price'       => 33000,
                'hpp'         => 14000,
                'status'      => 'available',
            ],

            // TOAST
            [
                'category_id' => $catMap['Toast'],
                'name'        => 'Cheese Toast',
                'description' => 'Toast with cheese',
                'price'       => 22000,
                'hpp'         => 10000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Toast'],
                'name'        => 'Chocolate Toast',
                'description' => 'Toast chocolate',
                'price'       => 22000,
                'hpp'         => 10000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Toast'],
                'name'        => 'Butter Toast',
                'description' => 'Classic butter toast',
                'price'       => 18000,
                'hpp'         => 8000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Toast'],
                'name'        => 'Egg Salad Toast',
                'description' => 'Toast with egg salad',
                'price'       => 25000,
                'hpp'         => 12000,
                'status'      => 'available',
            ],

            // DESSERT
            [
                'category_id' => $catMap['Dessert'],
                'name'        => 'Matcha Bun',
                'description' => 'Sweet matcha bun',
                'price'       => 20000,
                'hpp'         => 9000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Dessert'],
                'name'        => 'Cookies',
                'description' => 'Fresh baked cookies',
                'price'       => 15000,
                'hpp'         => 6000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Dessert'],
                'name'        => 'Croissant',
                'description' => 'Butter croissant',
                'price'       => 18000,
                'hpp'         => 7000,
                'status'      => 'available',
            ],

            // SNACK
            [
                'category_id' => $catMap['Snack'],
                'name'        => 'Fries',
                'description' => 'French fries',
                'price'       => 20000,
                'hpp'         => 9000,
                'status'      => 'available',
            ],

            [
                'category_id' => $catMap['Snack'],
                'name'        => 'Crispy Chicken Nuggets',
                'description' => 'Chicken nuggets crispy',
                'price'       => 25000,
                'hpp'         => 12000,
                'status'      => 'available',
            ],

        ];

        $this->db->table('menus')->insertBatch($menus);
    }
}