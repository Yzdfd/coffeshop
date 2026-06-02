<?php

namespace App\Models;

use CodeIgniter\Model;

class PromoModel extends Model
{
    protected $table = 'promos';
    protected $primaryKey = 'id';

    protected $allowedFields = [
        'code',
        'type',
        'value',
        'valid_from',
        'valid_until',
        'status',
    ];

    protected $useTimestamps = false;

    protected function hasField(string $table, string $field): bool
    {
        return $this->db->tableExists($table)
            && method_exists($this->db, 'fieldExists')
            && $this->db->fieldExists($field, $table);
    }

    public function isUsed(int $promoId, ?string $promoCode = null): bool
    {
        if ($this->db->tableExists('transactions')) {
            if ($this->hasField('transactions', 'promo_id')) {
                $count = $this->db->table('transactions')
                    ->where('promo_id', $promoId)
                    ->countAllResults();

                if ($count > 0) {
                    return true;
                }
            }

            if ($promoCode && $this->hasField('transactions', 'promo_code')) {
                $count = $this->db->table('transactions')
                    ->where('promo_code', $promoCode)
                    ->countAllResults();

                if ($count > 0) {
                    return true;
                }
            }
        }

        if ($this->db->tableExists('payments')) {
            if ($this->hasField('payments', 'promo_id')) {
                $count = $this->db->table('payments')
                    ->where('promo_id', $promoId)
                    ->countAllResults();

                if ($count > 0) {
                    return true;
                }
            }

            if ($promoCode && $this->hasField('payments', 'promo_code')) {
                $count = $this->db->table('payments')
                    ->where('promo_code', $promoCode)
                    ->countAllResults();

                if ($count > 0) {
                    return true;
                }
            }
        }

        return false;
    }
}
