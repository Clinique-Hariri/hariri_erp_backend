<?php

namespace Modules\Patients\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckupTicket extends Model
{
  use HasFactory;

  /**
   * The attributes that are mass assignable.
   */
  protected $fillable = [
    'ticket_number',
    'date',
    'status',
    'checkup_id',
  ];

  protected $casts = [
    'date' => 'date',
  ];

  public function checkup(): BelongsTo
  {
    return $this->belongsTo(Checkup::class);
  }

  //creating boot method to auto generate ticket number
  protected static function boot()
  {
    parent::boot();

    static::creating(function ($model) {
      $doctorId = $model->checkup->doctor_id ?? null;

      if (!$doctorId) {
        throw new \Exception("Doctor ID is required to generate ticket number.");
      }

      $maxTicketNumber = self::whereDate('date', $model->date)
        ->whereHas('checkup', fn($q) => $q->where('doctor_id', $doctorId))
        ->max('ticket_number');

      $model->ticket_number = $maxTicketNumber ? $maxTicketNumber + 1 : 1;
    });
  }

  //tickets bofore you ticktes scope
  public function scopeActiveBeforeCurrent($query, CheckupTicket $ticket)
  {
    return $query->whereHas('checkup', function ($q) use ($ticket) {
      $q->where('doctor_id', $ticket->checkup->doctor_id);
    })
      ->where('ticket_number', '<', $ticket->ticket_number)
      ->where('status', 'pending')
      ->where('date', $ticket->date)
      ->orderBy('id', 'desc');
  }

  public function scopeDoctorActiveTickets($query, $doctorId)
  {
    return $query->whereHas('checkup', function ($q) use ($doctorId) {
      $q->where('doctor_id', $doctorId);
    })
      ->where('status', 'pending')
      ->where('date', now()->format('Y-m-d'))
      ->orderBy('ticket_number', 'asc');
  }
}
