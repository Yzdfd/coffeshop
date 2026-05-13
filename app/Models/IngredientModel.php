<?php

namespace App\Models;

use CodeIgniter\Model;

class IngredientModel extends Model
{
    protected $table      = 'ingredients';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'supplier_id',
        'name',
        'stock_qty',
        'min_stock',
        'unit',
    ];

    // DB ingredients sudah pakai DEFAULT current_timestamp() — biarkan DB yang handle
    protected $useTimestamps = false;
}

