<?php

namespace Modules\Transactions\Models;

use Illuminate\Database\Eloquent\Model;

class ExternalContact extends Model
{
  protected $fillable = [
    'fullname',
    'phone',
  ];
}
