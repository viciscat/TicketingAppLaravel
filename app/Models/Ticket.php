<?php

namespace App\Models;

use App\Enums\TicketKind;
use App\Enums\TicketPriority;
use App\Enums\TicketStatus;
use App\Enums\TicketType;
use Database\Factories\TicketFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\DB;

class Ticket extends Model
{
    /** @use HasFactory<TicketFactory> */
    use HasFactory, Notifiable;

    protected $table = 'tickets';
    protected $fillable = [
        'project_id',
        'local_id',
        'created_by',
        'title',
        'status',
        'type',
        'kind',
        'priority',
        'description',
        'previous_status',
        'refuse_reason',
    ];

    protected function casts(): array
    {
        return [
            'priority' => TicketPriority::class,
            'status' => TicketStatus::class,
            'type' => TicketType::class,
            'kind' => TicketKind::class,
        ];
    }

    public function project(): BelongsTo
    {
        return $this->belongsTo(Project::class, 'project_id');
    }

    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedTo(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'ticket_members', 'ticket_id', 'user_id');
    }

    public function isAssignedTo(User $user): bool
    {
        return $this->assignedTo()->where('user_id', $user->id)->exists();
    }

    public function logs(): HasMany
    {
        return $this->hasMany(TimeLog::class, 'ticket_id');
    }

    protected static function booted(): void
    {
        static::creating(function ($ticket) {

            DB::transaction(function () use ($ticket) {

                $project = Project::lockForUpdate()->find($ticket->project_id);

                $ticket->local_id = $project->next_ticket_id;

                $project->next_ticket_id += 1;
                $project->save();
            });

        });
    }
}
