<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A visitor's report that a facility's information is wrong or missing.
 * Deliberately stores no IP address and no account: only the message and an optional contact.
 */
class Report extends Model
{
    use HasFactory;

    public const STATUS_NEW = 'new';

    public const STATUS_RESOLVED = 'resolved';

    protected $fillable = [
        'message',
        'contact',
        'status',
    ];

    public function facility(): BelongsTo
    {
        return $this->belongsTo(Facility::class);
    }
}
