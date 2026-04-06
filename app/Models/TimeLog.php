<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TimeLog extends Model
{
    protected $table = 'time_logs';
    protected $fillable = [
        'ticket_id',
        'user_id',
        'started_at',
        'time_spent',
        'comment',
    ];

    public function ticket(): BelongsTo
    {
        return $this->belongsTo(Ticket::class, 'ticket_id');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
            'time_spent' => 'integer',
        ];
    }

    public static function formatDuration(int $time): string
    {
        $minutes = $time % 60;
        $hours = floor($time / 60) % 24;
        $days = floor($time / 60 / 24);
        return ($days > 0 ? $days . "d " : "") . ($hours > 0 ? $hours . "h " : "") . ($minutes . "m");
    }
}
