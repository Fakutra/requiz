<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class TechnicalTestSchedule extends Model
{
    use HasFactory;

    /**
     * Kolom yang dapat diisi mass-assignment.
     */
    protected $fillable = [
        'position_id',
        'schedule_date',
        'zoom_link',
        'zoom_id',
        'zoom_passcode',
        'keterangan',
        'upload_deadline',
    ];

    /**
     * Casting atribut tanggal.
     */
    protected $casts = [
        'schedule_date'   => 'datetime',
        'upload_deadline' => 'datetime',
    ];

    /* ===========================
     |          RELASI
     |===========================*/

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function answers()
    {
        return $this->hasMany(TechnicalTestAnswer::class);
    }

    /**
     * Ambil jawaban (answer) terbaru milik applicant tertentu untuk schedule ini.
     */
    public function latestAnswerFor(Applicant $applicant)
    {
        return $this->answers()
            ->where('applicant_id', $applicant->id)
            ->orderByDesc('submitted_at')
            ->first();
    }

    /* ===========================
     |          SCOPES
     |===========================*/

    /**
     * Filter berdasarkan posisi.
     */
    public function scopeForPosition(Builder $query, $position)
    {
        $positionId = $position instanceof Position ? $position->id : $position;
        return $query->where('position_id', $positionId);
    }

    /**
     * Jadwal yang akan datang (>= sekarang).
     */
    public function scopeUpcoming(Builder $query)
    {
        return $query->where('schedule_date', '>=', now())->orderBy('schedule_date');
    }

    /**
     * Jadwal yang sudah lewat (< sekarang).
     */
    public function scopePast(Builder $query)
    {
        return $query->where('schedule_date', '<', now())->orderByDesc('schedule_date');
    }

    /* ===========================
     |        HELPERS
     |===========================*/

    /**
     * Apakah saat ini masih dalam batas upload (tombol upload aktif)?
     */
    public function withinUploadDeadline(): bool
    {
        return is_null($this->upload_deadline) || now()->lte($this->upload_deadline);
    }
}
