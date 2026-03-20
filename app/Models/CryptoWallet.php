<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CryptoWallet extends Model
{
    protected $fillable = ['currency', 'network', 'address', 'qr_code_path', 'label', 'is_active', 'sort_order'];

    protected function casts(): array
    {
        return ['is_active' => 'boolean'];
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('sort_order');
    }

    public function getDisplayNameAttribute(): string
    {
        return $this->network ? "{$this->currency} ({$this->network})" : $this->currency;
    }
}
