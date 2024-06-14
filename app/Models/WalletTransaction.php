<?php

namespace App\Models;

use App\Enums\TransactionStatus;
use App\Enums\TransactionType;
use App\Models\Scopes\OrderByCreatedDateDescending;
use Illuminate\Database\Eloquent\Attributes\ScopedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

#[ScopedBy([OrderByCreatedDateDescending::class])]
class WalletTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['wallet_id','amount','new_balance','payment_method','type','status','type','notes'];

    protected $casts = [
        'type'=>TransactionType::class,
        'status'=>TransactionStatus::class,
    ];

    public function wallet(){
        return $this->belongsTo(Wallet::class);
    }
}
