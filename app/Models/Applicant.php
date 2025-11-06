<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Applicant extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'position_id',
        'batch_id',
        'pendidikan',
        'universitas',
        'jurusan',
        'thn_lulus',
        'skills',
        'ekspektasi_gaji',
        'cv_document',
        'doc_tambahan',
        'status',
    ];

    // optional: hemat N+1 saat listing/export
    protected $with = [
        'user.profile',
        'position',
        'batch',
    ];

    protected $casts = [
        // 'tgl_lahir' DIHAPUS karena tanggal lahir ada di profiles.birthdate
        'ekspektasi_gaji' => 'integer',
    ];

    // biar properti virtual kebaca di Blade/JSON & intelephense gak rewel
    protected $appends = ['age', 'ekspektasi_gaji_formatted', 'current_stage', 'final_position'];

    /* ================== Relationships ================== */

    public function position()
    {
        return $this->belongsTo(Position::class);
    }

    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }

    public function latestTestResult()
    {
        return $this->hasOne(TestResult::class)->latestOfMany();
    }

    public function technicalTestAnswers()
    {
        return $this->hasMany(TechnicalTestAnswer::class);
    }

    public function latestTechnicalTestAnswer()
    {
        return $this->hasOne(TechnicalTestAnswer::class)->latestOfMany();
    }

    public function emailLogs()
    {
        return $this->hasMany(\App\Models\EmailLog::class);
    }

    public function latestEmailLog()
    {
        return $this->hasOne(\App\Models\EmailLog::class)->latestOfMany();
    }

    public function selectionLogs()
    {
        return $this->hasMany(SelectionLog::class);
    }

    public function myInterviewResult()
    {
        return $this->hasOne(InterviewResult::class)->where('user_id', auth()->id());
    }

    public function offering()
    {
        return $this->hasOne(Offering::class);
    }

    public function interviewResults()
    {
        return $this->hasMany(InterviewResult::class, 'applicant_id');
    }

    /* ================== Accessors ================== */

    // Rp 1.000.000 style
    public function getEkspektasiGajiFormattedAttribute(): ?string
    {
        return is_null($this->ekspektasi_gaji)
            ? null
            : 'Rp ' . number_format($this->ekspektasi_gaji, 0, ',', '.');
    }

    // umur dari profile.birthdate
    public function getAgeAttribute(): ?int
    {
        $birth = $this->user?->profile?->birthdate;
        if (!$birth) return null;

        try {
            return ($birth instanceof Carbon ? $birth : Carbon::parse($birth))->age;
        } catch (\Throwable $e) {
            return null;
        }
    }

    public function getCurrentStageAttribute(): string
    {
        $status = (string) ($this->status ?? '');
        $s = mb_strtolower($status);

        if (str_contains($s, 'administrasi'))   return 'Seleksi Administrasi';
        if (str_contains($s, 'tes tulis'))      return 'Seleksi Tes Tulis';
        if (str_contains($s, 'technical test')) return 'Technical Test';
        if (str_contains($s, 'interview'))      return 'Interview';
        if (str_contains($s, 'offering'))       return 'Offering';

        return $status !== '' ? $status : 'Tahap Tidak Dikenal';
    }

    public function getFinalPositionAttribute(): string
    {
        if ($this->offering && $this->offering->position) {
            return (string) ($this->offering->position->name ?? '-');
        }
        return (string) ($this->position->name ?? '-');
    }

    /* ================== Query Scopes ================== */

    // cari di: users.name, users.email, applicants.jurusan, positions.name
    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') return $query;

        $needle = '%'.mb_strtolower($term).'%';

        return $query->where(function ($w) use ($needle) {
            $w->whereRaw('LOWER(applicants.jurusan) LIKE ?', [$needle])
              ->orWhereHas('position', fn($p) =>
                  $p->whereRaw('LOWER(name) LIKE ?', [$needle])
               )
              ->orWhereHas('user', function ($u) use ($needle) {
                  $u->whereRaw('LOWER(name) LIKE ?', [$needle])
                    ->orWhereRaw('LOWER(email) LIKE ?', [$needle]);
               });
        });
    }

    public function scopeOfPosition($query, $positionId)
    {
        if (empty($positionId)) return $query;
        return $query->where('position_id', $positionId);
    }

    public function scopeOfBatch($query, $batchId)
    {
        if (empty($batchId)) return $query;
        return $query->where('batch_id', $batchId);
    }

    // app/Models/Applicant.php
    public function getNameAttribute()
    {
        return $this->attributes['name'] ?? $this->user?->name;
    }

    public function getEmailAttribute()
    {
        return $this->attributes['email'] ?? $this->user?->email;
    }

}
