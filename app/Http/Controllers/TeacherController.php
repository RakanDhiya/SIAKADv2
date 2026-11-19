<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Validation\Rule;

class TeacherController extends CrudController
{
    protected string $model = Teacher::class;
    protected string $routeName = 'teachers';
    protected string $title = 'Data Guru';
    protected string $singular = 'Guru';
    protected array $searchable = ['name', 'nip'];
    protected bool $showable = true;
    protected string $orderBy = 'name';
    protected string $orderDir = 'asc';

    protected function columns(): array
    {
        return ['NIP' => 'nip', 'Nama' => 'name', 'JK' => 'gender', 'Telepon' => 'phone'];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'nip', 'label' => 'NIP',
                'rules' => fn ($m) => ['nullable', 'string', 'max:30', Rule::unique('teachers', 'nip')->ignore($m?->id)]],
            ['name' => 'name', 'label' => 'Nama lengkap', 'required' => true, 'rules' => 'required|string|max:100'],
            ['name' => 'gender', 'label' => 'Jenis kelamin', 'type' => 'select', 'options' => Teacher::GENDERS, 'required' => true,
                'rules' => ['required', Rule::in(array_keys(Teacher::GENDERS))]],
            ['name' => 'phone', 'label' => 'No. telepon', 'rules' => 'nullable|string|max:30'],
            ['name' => 'user_id', 'label' => 'Akun login (peran Guru)', 'type' => 'select',
                'hint' => 'Buat akun dulu di menu Akun Pengguna, lalu pilih di sini.',
                'options' => fn ($m) => User::optionsFor('guru', 'teacher', $m?->user_id),
                'rules' => fn ($m) => ['nullable', Rule::exists('users', 'id')->where('role', 'guru'),
                    Rule::unique('teachers', 'user_id')->ignore($m?->id)]],
            ['name' => 'address', 'label' => 'Alamat', 'type' => 'textarea', 'rules' => 'nullable|string|max:500'],
        ];
    }
}
