<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\TeacherScope;
use App\Models\LearningMaterial;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class LearningMaterialController extends CrudController
{
    use TeacherScope;

    protected string $model = LearningMaterial::class;
    protected string $routeName = 'materials';
    protected string $title = 'Materi Pembelajaran';
    protected string $singular = 'Materi';
    protected array $with = ['teachingAssignment.subject', 'teachingAssignment.classroom', 'teachingAssignment.semester.academicYear'];
    protected array $searchable = ['title'];

    protected function query(): Builder
    {
        return $this->scopeToTeacher(parent::query());
    }

    protected function filters(): array
    {
        return ['teaching_assignment_id' => ['label' => 'Penugasan', 'options' => fn () => $this->assignmentOptions()]];
    }

    protected function columns(): array
    {
        return [
            'Judul'     => 'title',
            'Penugasan' => 'teachingAssignment.label',
            'Berkas'    => fn ($r) => $r->file_path ? 'Ada' : '—',
            'Tautan'    => fn ($r) => $r->link_url ? 'Ada' : '—',
            'Dibuat'    => fn ($r) => $r->created_at?->format('d/m/Y'),
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'teaching_assignment_id', 'label' => 'Penugasan', 'type' => 'select', 'required' => true,
                'options' => fn () => $this->assignmentOptions(),
                'rules' => fn () => ['required', Rule::in(array_keys($this->assignmentOptions()))]],
            ['name' => 'title', 'label' => 'Judul materi', 'required' => true, 'rules' => 'required|string|max:150'],
            ['name' => 'description', 'label' => 'Deskripsi', 'type' => 'textarea', 'rules' => 'nullable|string|max:2000'],
            ['name' => 'file_path', 'label' => 'Berkas (maks. 5 MB)', 'type' => 'file',
                'rules' => 'nullable|file|max:5120|mimes:pdf,doc,docx,ppt,pptx,xls,xlsx,jpg,jpeg,png,zip'],
            ['name' => 'link_url', 'label' => 'Tautan (opsional)', 'type' => 'text', 'rules' => 'nullable|url|max:255'],
        ];
    }
}
