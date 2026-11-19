<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\User;
use Illuminate\Validation\Rule;

class StudentController extends CrudController
{
    protected string $model = Student::class;
    protected string $routeName = 'students';
    protected string $title = 'Data Siswa';
    protected string $singular = 'Siswa';
    protected array $with = ['activeEnrollment.classroom'];
    protected array $searchable = ['name', 'nis', 'nisn'];
    protected bool $showable = true;
    protected string $orderBy = 'name';
    protected string $orderDir = 'asc';

    protected function filters(): array
    {
        return [
            'status' => ['label' => 'Status', 'options' => Student::STATUSES],
            'classroom_id' => [
                'label'   => 'Kelas',
                'options' => fn () => Classroom::options(),
                'apply'   => fn ($q, $v) => $q->whereHas('activeEnrollment', fn ($e) => $e->where('classroom_id', $v)),
            ],
        ];
    }

    protected function columns(): array
    {
        return [
            'NIS'    => 'nis',
            'Nama'   => 'name',
            'JK'     => 'gender',
            'Kelas'  => 'activeEnrollment.classroom.name',
            'Status' => fn ($r) => $this->badge(Student::STATUSES[$r->status] ?? $r->status, $r->status === 'aktif' ? 'green' : 'gray'),
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'nis', 'label' => 'NIS', 'required' => true,
                'rules' => fn ($m) => ['required', 'string', 'max:30', Rule::unique('students', 'nis')->ignore($m?->id)]],
            ['name' => 'nisn', 'label' => 'NISN',
                'rules' => fn ($m) => ['nullable', 'string', 'max:30', Rule::unique('students', 'nisn')->ignore($m?->id)]],
            ['name' => 'name', 'label' => 'Nama lengkap', 'required' => true, 'rules' => 'required|string|max:100'],
            ['name' => 'gender', 'label' => 'Jenis kelamin', 'type' => 'select', 'options' => Student::GENDERS, 'required' => true,
                'rules' => ['required', Rule::in(array_keys(Student::GENDERS))]],
            ['name' => 'birth_place', 'label' => 'Tempat lahir', 'rules' => 'nullable|string|max:100'],
            ['name' => 'birth_date', 'label' => 'Tanggal lahir', 'type' => 'date', 'rules' => 'nullable|date|before:today'],
            ['name' => 'phone', 'label' => 'No. telepon', 'rules' => 'nullable|string|max:30'],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => Student::STATUSES, 'required' => true, 'default' => 'aktif',
                'rules' => ['required', Rule::in(array_keys(Student::STATUSES))]],
            ['name' => 'user_id', 'label' => 'Akun login (peran Siswa)', 'type' => 'select',
                'hint' => 'Buat akun dulu di menu Akun Pengguna, lalu pilih di sini.',
                'options' => fn ($m) => User::optionsFor('siswa', 'student', $m?->user_id),
                'rules' => fn ($m) => ['nullable', Rule::exists('users', 'id')->where('role', 'siswa'),
                    Rule::unique('students', 'user_id')->ignore($m?->id)]],
            ['name' => 'address', 'label' => 'Alamat', 'type' => 'textarea', 'rules' => 'nullable|string|max:500'],
        ];
    }
}
