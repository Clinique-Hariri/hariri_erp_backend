<?php

namespace Modules\Transactions\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Transaction extends Model
{
  //use SoftDeletes;

  protected $fillable = [
    'transaction_number',
    'amount',
    'details',
    'type',
    'status',
    'user_id',
    'accountable_type',
    'accountable_id',
  ];

  protected $casts = [
    'amount' => 'float',
  ];

  // Auto-generate transaction_number
  protected static function booted(): void
  {
    static::creating(function (self $transaction) {
      if (empty($transaction->transaction_number)) {
        $transaction->transaction_number = 'TRX-' . strtoupper(Str::random(10));
      }
    });
  }

  // Relationships
  public function user(): BelongsTo
  {
    return $this->belongsTo(User::class);
  }

  public function transactionable(): MorphTo
  {
    return $this->morphTo();
  }

  public function accountable(): MorphTo
  {
    return $this->morphTo();
  }
}
