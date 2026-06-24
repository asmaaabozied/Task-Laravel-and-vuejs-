<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Ticket extends Model
{
    use HasFactory;

    public const STATUS_OPEN = 'Open';
    public const STATUS_ESCALATED = 'Escalated';
    public const STATUS_CLOSED = 'Closed';

    protected $fillable = [
        'subject',
        'description',
        'priority',
        'status',
        'customer_name',
        'customer_email',
        'escalated_at',
    ];

    protected $casts = [
        'escalated_at' => 'datetime',
    ];

    public function notifications(): HasMany
    {
        return $this->hasMany(TicketNotificationLog::class);
    }
}
