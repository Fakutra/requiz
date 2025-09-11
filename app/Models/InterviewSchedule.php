<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class InterviewSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'position_id',
        'schedule_start',
        'schedule_end',
        'zoom_link',
        'zoom_id',
        'zoom_passcode',
        'keterangan',
    ];

    protected $casts = [
        'schedule_start' => 'datetime',
        'schedule_end'   => 'datetime',
    ];

    /* ===========================
     |          RELASI
     |===========================*/
    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    /* ===========================
     |          SCOPES
     |===========================*/
    /**
     * Filter berdasarkan posisi (terima model Position atau ID).
     */
    public function scopeForPosition(Builder $query, $position): Builder
    {
        $positionId = $position instanceof Position ? $position->id : $position;
        return $query->where('position_id', $positionId);
    }

    /**
     * Jadwal yang akan datang / sedang berlangsung (end >= now).
     */
    public function scopeUpcoming(Builder $query): Builder
    {
        return $query->where('schedule_end', '>=', now())
                     ->orderBy('schedule_start');
    }

    /**
     * Jadwal yang sudah lewat (end < now).
     */
    public function scopePast(Builder $query): Builder
    {
        return $query->where('schedule_end', '<', now())
                     ->orderByDesc('schedule_start');
    }

    /* ===========================
     |          HELPERS
     |===========================*/
    /**
     * Apakah saat ini berada dalam window interview.
     */
    public function isActive(): bool
    {
        return now()->between($this->schedule_start, $this->schedule_end, true);
    }

    /**
     * Durasi menit dari schedule.
     */
    public function durationMinutes(): ?int
    {
        if (!$this->schedule_start || !$this->schedule_end) return null;
        return $this->schedule_start->diffInMinutes($this->schedule_end);
    }
}
