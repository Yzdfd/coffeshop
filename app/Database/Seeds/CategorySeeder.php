<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [

            [
                'name'        => 'Coffee',
                'description' => 'Menu kopi',
                'sort_order'  => 1
            ],

            [
                'name'        => 'Non Coffee',
                'description' => 'Menu non kopi',
                'sort_order'  => 2
            ],

            [
                'name'        => 'Snack',
                'description' => 'Snack & side dish',
                'sort_order'  => 3
            ],

            [
                'name'        => 'Toast',
                'description' => 'Toast menu',
                'sort_order'  => 4
            ],

            [
                'name'        => 'Dessert',
                'description' => 'Dessert & pastry',
                'sort_order'  => 5
            ],

        ];

        $this->db->table('categories')->insertBatch($data);
    }
}