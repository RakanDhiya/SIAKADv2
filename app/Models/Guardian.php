<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * Mengelola data orang tua atau wali siswa dalam sistem akademik.
 */
class Guardian extends Model
{
    public const RELATIONSHIPS = ['ayah' => 'Ayah', 'ibu' => 'Ibu', 'wali' => 'Wali'];

    protected $guarded = [];

    public function user()     { return $this->belongsTo(User::class); }
    public function students() { return $this->belongsToMany(Student::class, 'guardian_student'); }
}