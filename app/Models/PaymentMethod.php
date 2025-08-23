<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'account_number',
        'account_name',
        'bank_code',
        'phone_number',
        'instructions',
        'logo',
        'is_active',
        'sort_order',
        'additional_info'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'additional_info' => 'array',
        'sort_order' => 'integer'
    ];

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('type', $type);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order')->orderBy('name');
    }

    // Accessors
    public function getLogoUrlAttribute()
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    // Helper methods
    public function isBankTransfer()
    {
        return $this->type === 'bank_transfer';
    }

    public function isEWallet()
    {
        return $this->type === 'e_wallet';
    }

    public function getDisplayDetailsAttribute()
    {
        $details = [];
        
        if ($this->isBankTransfer()) {
            if ($this->account_number) {
                $details['Account Number'] = $this->account_number;
            }
            if ($this->account_name) {
                $details['Account Name'] = $this->account_name;
            }
            if ($this->bank_code) {
                $details['Bank Code'] = $this->bank_code;
            }
        } elseif ($this->isEWallet()) {
            if ($this->phone_number) {
                $details['Phone Number'] = $this->phone_number;
            }
            if ($this->account_name) {
                $details['Account Name'] = $this->account_name;
            }
        } else {
            // For other payment types (credit_card, other), always show account name if available
            if ($this->account_name) {
                $details['Account Name'] = $this->account_name;
            }
            if ($this->account_number) {
                $details['Account Number'] = $this->account_number;
            }
        }
        
        return $details;
    }

    // Constants
    const TYPES = [
        'bank_transfer' => 'Bank Transfer',
        'e_wallet' => 'E-Wallet',
        'credit_card' => 'Credit Card',
        'other' => 'Other'
    ];
}
