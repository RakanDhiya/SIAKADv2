<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\TeacherScope;
use App\Models\Assignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

/** Tugas (dibuat guru). Pengumpulan dikelola di SubmissionController. */
class AssignmentController extends CrudController
{
    use TeacherScope;

    protected string $model = Assignment::class;
    protected string $routeName = 'assignments';
    protected string $title = 'Tugas';
    protected string $singular = 'Tugas';
    protected array $with = ['teachingAssignment.subject', 'teachingAssignment.classroom', 'teachingAssignment.semester.academicYear'];
    protected array $searchable = ['title'];

    protected function query(): Builder
    {
        return $this->scopeToTeacher(parent::query())->withCount('submissions');
    }

    protected function filters(): array
    {
        return ['teaching_assignment_id' => ['label' => 'Penugasan', 'options' => fn () => $this->assignmentOptions()]];
    }

    protected function columns(): array
    {
        return [
            'Judul'      => 'title',
            'Penugasan'  => 'teachingAssignment.label',
            'Tenggat'    => fn ($r) => $r->due_at?->format('d/m/Y H:i') ?? '—',
            'Terkumpul'  => 'submissions_count',
        ];
    }

    protected function rowActions(Model $row): array
    {
        return [['label' => 'Pengumpulan', 'url' => route('assignments.submissions', $row->id)]];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'teaching_assignment_id', 'label' => 'Penugasan', 'type' => 'select', 'required' => true,
                'options' => fn () => $this->assignmentOptions(),
                'rules' => fn () => ['required', Rule::in(array_keys($this->assignmentOptions()))]],
            ['name' => 'title', 'label' => 'Judul tugas', 'required' => true, 'rules' => 'required|string|max:150'],
            ['name' => 'description', 'label' => 'Instruksi', 'type' => 'textarea', 'rules' => 'nullable|string|max:3000'],
            ['name' => 'due_at', 'label' => 'Tenggat', 'type' => 'datetime', 'rules' => 'nullable|date'],
            ['name' => 'max_score', 'label' => 'Nilai maksimum', 'type' => 'number', 'required' => true, 'default' => 100, 'min' => 1, 'max' => 1000,
                'rules' => 'required|integer|between:1,1000'],
        ];
    }
}
