<?php

namespace App\Models;

use App\Traits\HasGoogleTranslationTrait;
use Illuminate\Notifications\DatabaseNotification as BaseDatabaseNotification;


class Notification extends BaseDatabaseNotification
{
  protected $table = 'notifications';

  protected $fillable = [
    'id',
    'type',
    'notifiable_type',
    'notifiable_id',
    'data',
    'read_at',
  ];

  protected $casts = [
    'data' => 'array',
    'read_at' => 'datetime',
  ];
}
