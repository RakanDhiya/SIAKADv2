<?php

namespace App\Http\Controllers;

use App\Models\ClassEnrollment;
use App\Models\Classroom;
use App\Models\Student;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EnrollmentController extends CrudController
{
    protected string $model = ClassEnrollment::class;
    protected string $routeName = 'enrollments';
    protected string $title = 'Penempatan Siswa ke Kelas';
    protected string $singular = 'Penempatan';
    protected array $with = ['student', 'classroom.academicYear'];

    protected function filters(): array
    {
        return [
            'classroom_id' => ['label' => 'Kelas', 'options' => fn () => Classroom::options()],
            'status'       => ['label' => 'Status', 'options' => ClassEnrollment::STATUSES],
        ];
    }

    protected function columns(): array
    {
        return [
            'NIS'          => 'student.nis',
            'Siswa'        => 'student.name',
            'Kelas'        => 'classroom.name',
            'Tahun ajaran' => 'classroom.academicYear.name',
            'Status'       => fn ($r) => $this->badge(ClassEnrollment::STATUSES[$r->status] ?? $r->status, $r->status === 'aktif' ? 'green' : 'gray'),
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'student_id', 'label' => 'Siswa', 'type' => 'select', 'required' => true,
                'options' => fn () => Student::options(), 'rules' => 'required|exists:students,id'],
            ['name' => 'classroom_id', 'label' => 'Kelas', 'type' => 'select', 'required' => true,
                'options' => fn () => Classroom::options(),
                'rules' => fn ($m) => ['required', 'exists:classrooms,id', function ($attr, $value, $fail) use ($m) {
                    $classroom = Classroom::find($value);
                    if (! $classroom) {
                        return;
                    }
                    $exists = ClassEnrollment::where('student_id', request('student_id'))
                        ->where('academic_year_id', $classroom->academic_year_id)
                        ->when($m, fn ($q) => $q->where('id', '!=', $m->id))
                        ->exists();
                    if ($exists) {
                        $fail('Siswa ini sudah ditempatkan di kelas lain pada tahun ajaran yang sama.');
                    }
                }]],
            ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'required' => true, 'default' => 'aktif',
                'options' => ClassEnrollment::STATUSES, 'rules' => ['required', Rule::in(array_keys(ClassEnrollment::STATUSES))]],
            ['name' => 'note', 'label' => 'Catatan', 'type' => 'textarea', 'rules' => 'nullable|string|max:300'],
        ];
    }

    protected function beforeSave(array $data, Model $model, Request $request): array
    {
        $data['academic_year_id'] = Classroom::findOrFail($data['classroom_id'])->academic_year_id;

        return $data;
    }
}
