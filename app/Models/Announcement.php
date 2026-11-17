<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola pengumuman akademik dan pengaturan visibilitasnya.
 */
class Announcement extends Model
{
    public const AUDIENCES = ['semua' => 'Semua pengguna', 'guru' => 'Guru', 'siswa' => 'Siswa', 'ortu' => 'Orang tua'];

    protected $guarded = [];

    protected $casts = ['published_at' => 'datetime'];

    public function author() { return $this->belongsTo(User::class, 'created_by'); }

    /**
     * Membatasi pengumuman berdasarkan hak akses pengguna.
     * 
     * Admin dapat melihat seluruh pengumuman, sedangkan user
     * lainnya hanya melihat pengumuman sesuai audiens dan waktu publikasi.
     */
    public function scopeVisibleTo($query, User $user)
    {
        if ($user->role === 'admin') {
            return $query;
        }

        return $query->whereIn('audience', ['semua', $user->role])
            ->where(fn ($q) => $q->whereNull('published_at')->orWhere('published_at', '<=', now()));
    }
}