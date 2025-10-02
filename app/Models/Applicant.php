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
        'batch_id',
        'position_id',
        'name',
        'email',
        'nik',
        'no_telp',
        'tpt_lahir',
        'tgl_lahir',
        'alamat',
        'pendidikan',
        'universitas',
        'jurusan',
        'thn_lulus',
        'skills',
        'cv_document',
        'status',
    ];

    // Biar tgl_lahir otomatis jadi Carbon instance
    protected $casts = [
        'tgl_lahir' => 'date',
    ];

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
        return $this->belongsTo(User::class); // jika pelamar login
    }

    public function testResults()
    {
        return $this->hasMany(TestResult::class);
    }

    public function latestTestResult()
    {
        return $this->hasOne(TestResult::class)->latestOfMany();
    }


    public function emailLogs()
    {
        return $this->hasMany(\App\Models\EmailLog::class);
    }
    
    public function latestEmailLog()
    {
        return $this->hasOne(\App\Models\EmailLog::class)->latestOfMany();
    }

    /* ================== Accessors ================== */

    // {{ $applicant->age }}
    public function getAgeAttribute(): ?int
    {
        if (empty($this->tgl_lahir)) return null;
        try {
            return ($this->tgl_lahir instanceof Carbon ? $this->tgl_lahir : Carbon::parse($this->tgl_lahir))->age;
        } catch (\Throwable $e) {
            return null;
        }
    }

    // {{ $applicant->current_stage }}
    public function getCurrentStageAttribute(): string
    {
        $status = (string) ($this->status ?? '');
        $statusLower = mb_strtolower($status);

        if (str_contains($statusLower, 'administrasi')) return 'Seleksi Administrasi';
        if (str_contains($statusLower, 'tes tulis'))     return 'Seleksi Tes Tulis';
        if (str_contains($statusLower, 'technical test'))return 'Technical Test';
        if (str_contains($statusLower, 'interview'))     return 'Interview';
        if (str_contains($statusLower, 'offering'))      return 'Offering';

        return $status !== '' ? $status : 'Tahap Tidak Dikenal';
    }

    /* ================== Query Scopes ================== */

    // scopeSearch: aman untuk MySQL & Postgres
    public function scopeSearch($query, ?string $term)
    {
        $term = trim((string) $term);
        if ($term === '') return $query;

        $needle = '%'.mb_strtolower($term).'%';

        return $query->where(function ($w) use ($needle) {
            $w->whereRaw('LOWER(name) LIKE ?', [$needle])
              ->orWhereRaw('LOWER(email) LIKE ?', [$needle])
              ->orWhereRaw('LOWER(jurusan) LIKE ?', [$needle])
              ->orWhereHas('position', fn($p) =>
                  $p->whereRaw('LOWER(name) LIKE ?', [$needle])
              );
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
    public function selectionLogs() { return $this->hasMany(SelectionLog::class); }

    public function technicalTestAnswers()
    {
        return $this->hasMany(TechnicalTestAnswer::class);
    }

    public function myInterviewResult()
    {
        return $this->hasOne(InterviewResult::class)->where('user_id', auth()->id());
    }

}
