<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Semester;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeachingAssignment;
use Illuminate\Validation\Rule;

class TeachingAssignmentController extends CrudController
{
    protected string $model = TeachingAssignment::class;
    protected string $routeName = 'teaching-assignments';
    protected string $title = 'Penugasan Guru Mengajar';
    protected string $singular = 'Penugasan';
    protected array $with = ['teacher', 'subject', 'classroom', 'semester.academicYear'];

    protected function filters(): array
    {
        return [
            'semester_id'  => ['label' => 'Semester', 'options' => fn () => Semester::options()],
            'classroom_id' => ['label' => 'Kelas', 'options' => fn () => Classroom::options()],
            'teacher_id'   => ['label' => 'Guru', 'options' => fn () => Teacher::options()],
        ];
    }

    protected function columns(): array
    {
        return [
            'Guru'     => 'teacher.name',
            'Mata pelajaran' => 'subject.name',
            'Kelas'    => 'classroom.name',
            'Semester' => 'semester.label',
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'teacher_id', 'label' => 'Guru', 'type' => 'select', 'required' => true,
                'options' => fn () => Teacher::options(), 'rules' => 'required|exists:teachers,id'],
            ['name' => 'subject_id', 'label' => 'Mata pelajaran', 'type' => 'select', 'required' => true,
                'options' => fn () => Subject::options(),
                'rules' => fn ($m) => ['required', 'exists:subjects,id',
                    Rule::unique('teaching_assignments', 'subject_id')
                        ->where('classroom_id', request('classroom_id'))
                        ->where('semester_id', request('semester_id'))
                        ->ignore($m?->id)]],
            ['name' => 'classroom_id', 'label' => 'Kelas', 'type' => 'select', 'required' => true,
                'options' => fn () => Classroom::options(), 'rules' => 'required|exists:classrooms,id'],
            ['name' => 'semester_id', 'label' => 'Semester', 'type' => 'select', 'required' => true,
                'options' => fn () => Semester::options(), 'rules' => 'required|exists:semesters,id'],
        ];
    }
}
