<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\TeacherScope;
use App\Models\Classroom;
use App\Models\Schedule;
use App\Models\TeachingAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

/**
 * Admin: CRUD penuh. Guru: hanya melihat jadwal mengajarnya sendiri.
 */
class ScheduleController extends CrudController
{
    use TeacherScope;

    protected string $model = Schedule::class;
    protected string $routeName = 'schedules';
    protected string $title = 'Jadwal Pelajaran';
    protected string $singular = 'Jadwal';
    protected array $with = ['teachingAssignment.subject', 'teachingAssignment.classroom', 'teachingAssignment.teacher', 'teachingAssignment.semester.academicYear'];

    protected function canWrite(): bool
    {
        return auth()->user()->isAdmin();
    }

    protected function query(): Builder
    {
        return $this->scopeToTeacher(parent::query());
    }

    protected function applyOrder(Builder $query): Builder
    {
        return $query->orderBy('day')->orderBy('start_time');
    }

    protected function filters(): array
    {
        return [
            'day' => ['label' => 'Hari', 'options' => Schedule::DAYS],
            'classroom_id' => [
                'label'   => 'Kelas',
                'options' => fn () => Classroom::options(),
                'apply'   => fn ($q, $v) => $q->whereHas('teachingAssignment', fn ($t) => $t->where('classroom_id', $v)),
            ],
        ];
    }

    protected function columns(): array
    {
        return [
            'Hari'     => fn ($r) => $r->day_name,
            'Jam'      => fn ($r) => $r->time_range,
            'Mata pelajaran' => 'teachingAssignment.subject.name',
            'Kelas'    => 'teachingAssignment.classroom.name',
            'Guru'     => 'teachingAssignment.teacher.name',
            'Ruang'    => 'room',
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'teaching_assignment_id', 'label' => 'Penugasan (mapel – kelas – guru)', 'type' => 'select', 'required' => true,
                'options' => fn () => $this->assignmentOptions(),
                'rules' => fn () => ['required', Rule::in(array_keys($this->assignmentOptions()))]],
            ['name' => 'day', 'label' => 'Hari', 'type' => 'select', 'required' => true, 'options' => Schedule::DAYS,
                'rules' => ['required', Rule::in(array_keys(Schedule::DAYS))]],
            ['name' => 'start_time', 'label' => 'Jam mulai', 'type' => 'time', 'required' => true, 'rules' => 'required|date_format:H:i'],
            ['name' => 'end_time', 'label' => 'Jam selesai', 'type' => 'time', 'required' => true,
                'rules' => fn ($m) => ['required', 'date_format:H:i', 'after:start_time', function ($attr, $value, $fail) use ($m) {
                    $ta = TeachingAssignment::find(request('teaching_assignment_id'));
                    if (! $ta) {
                        return;
                    }
                    $clash = Schedule::where('day', request('day'))
                        ->where('start_time', '<', $value)
                        ->where('end_time', '>', request('start_time'))
                        ->when($m, fn ($q) => $q->where('id', '!=', $m->id))
                        ->whereHas('teachingAssignment', fn ($q) => $q
                            ->where('semester_id', $ta->semester_id)
                            ->where(fn ($w) => $w->where('classroom_id', $ta->classroom_id)->orWhere('teacher_id', $ta->teacher_id)))
                        ->exists();
                    if ($clash) {
                        $fail('Jadwal bentrok dengan jadwal lain untuk kelas atau guru yang sama.');
                    }
                }]],
            ['name' => 'room', 'label' => 'Ruang', 'rules' => 'nullable|string|max:50'],
        ];
    }
}
