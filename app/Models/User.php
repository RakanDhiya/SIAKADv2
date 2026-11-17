<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

/**
 * Mengelola akun pengguna dalam sistem informasi akademik.
 *
 * Menangani autentikasi, identifikasi peran pengguna,
 * serta relasi akun dengan profil guru, siswa, dan orang tua.
 *
 * Peran pengguna yang didukung meliputi admin, guru, siswa,
 * dan orang tua.
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * Daftar peran pengguna beserta label tampilannya.
     *
     * @var array<string, string>
     */
    public const ROLES = [
        'admin' => 'Admin',
        'guru'  => 'Guru',
        'siswa' => 'Siswa',
        'ortu'  => 'Orang Tua',
    ];

    /**
     * Mengatur atribut yang dapat diisi melalui mass assignment.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Atribut yang disembunyikan saat model diserialisasi.
     *
     * @var array<int, string>
     */
    protected $hidden = ['password', 'remember_token'];

    /**
     * Menentukan konversi tipe data atribut model.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password'          => 'hashed',
            'is_active'         => 'boolean',
        ];
    }

    /**
     * Mendefinisikan relasi satu akun pengguna dengan satu profil guru.
     */
    public function teacher()  { return $this->hasOne(Teacher::class); }

    /**
     * Mendefinisikan relasi satu akun pengguna dengan satu profil siswa.
     */
    public function student()  { return $this->hasOne(Student::class); }

    /**
     * Mendefinisikan relasi satu akun pengguna dengan satu profil orang tua.
     */
    public function guardian() { return $this->hasOne(Guardian::class); }

    /**
     * Memeriksa apakah pengguna memiliki salah satu peran yang ditentukan.
     *
     * @param string ...$roles Daftar peran yang akan diperiksa.
     * @return bool True jika peran pengguna cocok dengan salah satu peran yang diberikan.
     */
    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles, true);
    }

    /**
     * Memeriksa apakah pengguna memiliki peran administrator.
     *
     * @return bool True jika peran pengguna adalah admin.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Mengambil label tampilan berdasarkan peran pengguna.
     *
     * Jika peran tidak terdaftar, nilai peran asli akan dikembalikan.
     *
     * @return string Label peran pengguna.
     */
    public function roleLabel(): string
    {
        return self::ROLES[$this->role] ?? $this->role;
    }

    /**
     * Mengambil daftar akun berdasarkan peran yang belum terhubung
     * dengan profil tertentu.
     *
     * Akun yang sedang digunakan tetap disertakan dalam hasil
     * meskipun telah memiliki relasi dengan profil.
     *
     * @param string $role Peran akun yang akan dicari.
     * @param string $relation Nama relasi profil pada model User.
     * @param mixed $current ID akun yang sedang digunakan.
     * @return array Daftar ID akun beserta nama dan alamat email.
     */
    public static function optionsFor(string $role, string $relation, $current = null): array
    {
        return static::where('role', $role)
            ->where(fn ($q) => $q->doesntHave($relation)->when($current, fn ($w) => $w->orWhere('id', $current)))
            ->orderBy('name')
            ->get()
            ->mapWithKeys(fn ($u) => [$u->id => $u->name.' ('.$u->email.')'])
            ->all();
    }
}