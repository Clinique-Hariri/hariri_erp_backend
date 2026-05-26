<?php

namespace Modules\Actions\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Modules\Actions\Constants\ActionType;

class Action extends Model
{
    protected $fillable = [
        'action_type',
        'from_status',
        'to_status',
        'user_id',
    ];

    protected $casts = [
        'action_type' => 'string',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function actionable(): MorphTo
    {
        return $this->morphTo();
    }
}
