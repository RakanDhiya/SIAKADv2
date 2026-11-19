<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Classroom;
use App\Models\Major;
use App\Models\Teacher;
use Illuminate\Validation\Rule;

class ClassroomController extends CrudController
{
    protected string $model = Classroom::class;
    protected string $routeName = 'classrooms';
    protected string $title = 'Data Kelas';
    protected string $singular = 'Kelas';
    protected array $with = ['academicYear', 'major', 'homeroomTeacher'];
    protected array $searchable = ['name'];

    protected function query(): \Illuminate\Database\Eloquent\Builder
    {
        return parent::query()->withCount(['enrollments as students_count' => fn ($q) => $q->where('status', 'aktif')]);
    }

    protected function filters(): array
    {
        return ['academic_year_id' => ['label' => 'Tahun ajaran', 'options' => fn () => AcademicYear::options()]];
    }

    protected function columns(): array
    {
        return [
            'Kelas'        => 'name',
            'Tingkat'      => 'grade_level',
            'Jurusan'      => 'major.name',
            'Tahun ajaran' => 'academicYear.name',
            'Wali kelas'   => 'homeroomTeacher.name',
            'Siswa aktif'  => 'students_count',
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Nama kelas', 'required' => true, 'hint' => 'Contoh: X RPL 1',
                'rules' => fn ($m) => ['required', 'string', 'max:50',
                    Rule::unique('classrooms', 'name')->where('academic_year_id', request('academic_year_id'))->ignore($m?->id)]],
            ['name' => 'grade_level', 'label' => 'Tingkat', 'type' => 'number', 'required' => true, 'min' => 1, 'max' => 13,
                'rules' => 'required|integer|between:1,13'],
            ['name' => 'academic_year_id', 'label' => 'Tahun ajaran', 'type' => 'select', 'required' => true,
                'options' => fn () => AcademicYear::options(), 'rules' => 'required|exists:academic_years,id'],
            ['name' => 'major_id', 'label' => 'Jurusan', 'type' => 'select',
                'options' => fn () => Major::options(), 'rules' => 'nullable|exists:majors,id'],
            ['name' => 'homeroom_teacher_id', 'label' => 'Wali kelas', 'type' => 'select',
                'options' => fn () => Teacher::options(), 'rules' => 'nullable|exists:teachers,id'],
        ];
    }
}
