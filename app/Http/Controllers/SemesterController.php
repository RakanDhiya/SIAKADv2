<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use App\Models\Semester;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class SemesterController extends CrudController
{
    protected string $model = Semester::class;
    protected string $routeName = 'semesters';
    protected string $title = 'Semester';
    protected string $singular = 'Semester';
    protected array $with = ['academicYear'];

    protected function filters(): array
    {
        return ['academic_year_id' => ['label' => 'Tahun ajaran', 'options' => fn () => AcademicYear::options()]];
    }

    protected function columns(): array
    {
        return [
            'Tahun ajaran' => 'academicYear.name',
            'Semester'     => 'name',
            'Status'       => fn ($r) => $this->badge($r->is_active ? 'Aktif' : 'Tidak aktif', $r->is_active ? 'green' : 'gray'),
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'academic_year_id', 'label' => 'Tahun ajaran', 'type' => 'select', 'required' => true,
                'options' => fn () => AcademicYear::options(), 'rules' => 'required|exists:academic_years,id'],
            ['name' => 'name', 'label' => 'Semester', 'type' => 'select', 'required' => true, 'options' => Semester::NAMES,
                'rules' => fn ($m) => ['required', Rule::in(array_keys(Semester::NAMES)),
                    Rule::unique('semesters', 'name')->where('academic_year_id', request('academic_year_id'))->ignore($m?->id)]],
            ['name' => 'is_active', 'label' => 'Jadikan semester aktif', 'type' => 'checkbox', 'rules' => 'nullable|boolean'],
        ];
    }

    protected function afterSave(Model $model, Request $request): void
    {
        if ($model->is_active) {
            Semester::where('id', '!=', $model->id)->update(['is_active' => false]);
        }
    }
}
