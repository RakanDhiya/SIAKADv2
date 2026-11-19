<?php

namespace App\Http\Controllers;

use App\Models\Guardian;
use App\Models\Student;
use App\Models\User;
use Illuminate\Validation\Rule;

class GuardianController extends CrudController
{
    protected string $model = Guardian::class;
    protected string $routeName = 'guardians';
    protected string $title = 'Data Orang Tua / Wali';
    protected string $singular = 'Orang tua/wali';
    protected array $with = ['students'];
    protected array $searchable = ['name', 'phone'];
    protected bool $showable = true;
    protected string $orderBy = 'name';
    protected string $orderDir = 'asc';

    protected function columns(): array
    {
        return [
            'Nama'    => 'name',
            'Hubungan' => fn ($r) => Guardian::RELATIONSHIPS[$r->relationship] ?? $r->relationship,
            'Telepon' => 'phone',
            'Anak'    => fn ($r) => $r->students->pluck('name')->join(', ') ?: '—',
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Nama lengkap', 'required' => true, 'rules' => 'required|string|max:100'],
            ['name' => 'relationship', 'label' => 'Hubungan', 'type' => 'select', 'options' => Guardian::RELATIONSHIPS, 'required' => true,
                'rules' => ['required', Rule::in(array_keys(Guardian::RELATIONSHIPS))]],
            ['name' => 'phone', 'label' => 'No. telepon', 'rules' => 'nullable|string|max:30'],
            ['name' => 'occupation', 'label' => 'Pekerjaan', 'rules' => 'nullable|string|max:100'],
            ['name' => 'user_id', 'label' => 'Akun login (peran Orang Tua)', 'type' => 'select',
                'hint' => 'Buat akun dulu di menu Akun Pengguna, lalu pilih di sini.',
                'options' => fn ($m) => User::optionsFor('ortu', 'guardian', $m?->user_id),
                'rules' => fn ($m) => ['nullable', Rule::exists('users', 'id')->where('role', 'ortu'),
                    Rule::unique('guardians', 'user_id')->ignore($m?->id)]],
            ['name' => 'students', 'label' => 'Anak (siswa)', 'type' => 'multiselect', 'relation' => 'students',
                'hint' => 'Tahan Ctrl/Cmd untuk memilih lebih dari satu.',
                'options' => fn () => Student::options(), 'rules' => 'nullable|array'],
            ['name' => 'address', 'label' => 'Alamat', 'type' => 'textarea', 'rules' => 'nullable|string|max:500'],
        ];
    }
}
