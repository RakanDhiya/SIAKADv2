<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Concerns\TeacherScope;
use App\Models\AssessmentComponent;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Validation\Rule;

class AssessmentComponentController extends CrudController
{
    use TeacherScope;

    protected string $model = AssessmentComponent::class;
    protected string $routeName = 'components';
    protected string $title = 'Komponen Penilaian';
    protected string $singular = 'Komponen';
    protected array $with = ['teachingAssignment.subject', 'teachingAssignment.classroom', 'teachingAssignment.semester.academicYear'];

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
            'Penugasan' => 'teachingAssignment.label',
            'Komponen'  => 'name',
            'Bobot (%)' => fn ($r) => rtrim(rtrim($r->weight, '0'), '.'),
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'teaching_assignment_id', 'label' => 'Penugasan', 'type' => 'select', 'required' => true,
                'options' => fn () => $this->assignmentOptions(),
                'rules' => fn () => ['required', Rule::in(array_keys($this->assignmentOptions()))]],
            ['name' => 'name', 'label' => 'Nama komponen', 'required' => true, 'hint' => 'Contoh: Tugas, Ulangan Harian, UTS, UAS',
                'rules' => fn ($m) => ['required', 'string', 'max:50',
                    Rule::unique('assessment_components', 'name')->where('teaching_assignment_id', request('teaching_assignment_id'))->ignore($m?->id)]],
            ['name' => 'weight', 'label' => 'Bobot (%)', 'type' => 'number', 'required' => true, 'min' => 0, 'max' => 100, 'step' => '0.01',
                'hint' => 'Total bobot semua komponen sebaiknya 100.', 'rules' => 'required|numeric|min:0|max:100'],
        ];
    }
}
