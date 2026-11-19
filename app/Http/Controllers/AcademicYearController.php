<?php

namespace App\Http\Controllers;

use App\Models\AcademicYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicYearController extends CrudController
{
    protected string $model = AcademicYear::class;
    protected string $routeName = 'academic-years';
    protected string $title = 'Tahun Ajaran';
    protected string $singular = 'Tahun ajaran';
    protected array $searchable = ['name'];
    protected string $orderBy = 'start_date';

    protected function columns(): array
    {
        return [
            'Tahun ajaran' => 'name',
            'Mulai'        => fn ($r) => $r->start_date?->format('d/m/Y'),
            'Selesai'      => fn ($r) => $r->end_date?->format('d/m/Y'),
            'Status'       => fn ($r) => $this->badge($r->is_active ? 'Aktif' : 'Tidak aktif', $r->is_active ? 'green' : 'gray'),
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Tahun ajaran', 'required' => true, 'hint' => 'Contoh: 2026/2027',
                'rules' => fn ($m) => ['required', 'string', 'max:20', Rule::unique('academic_years', 'name')->ignore($m?->id)]],
            ['name' => 'start_date', 'label' => 'Tanggal mulai', 'type' => 'date', 'required' => true, 'rules' => 'required|date'],
            ['name' => 'end_date', 'label' => 'Tanggal selesai', 'type' => 'date', 'required' => true, 'rules' => 'required|date|after:start_date'],
            ['name' => 'is_active', 'label' => 'Jadikan tahun ajaran aktif', 'type' => 'checkbox', 'rules' => 'nullable|boolean'],
        ];
    }

    protected function afterSave(Model $model, Request $request): void
    {
        // Hanya satu tahun ajaran yang aktif.
        if ($model->is_active) {
            AcademicYear::where('id', '!=', $model->id)->update(['is_active' => false]);
        }
    }
}
