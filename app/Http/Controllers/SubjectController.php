<?php

namespace App\Http\Controllers;

use App\Models\Subject;
use Illuminate\Validation\Rule;

class SubjectController extends CrudController
{
    protected string $model = Subject::class;
    protected string $routeName = 'subjects';
    protected string $title = 'Mata Pelajaran';
    protected string $singular = 'Mata pelajaran';
    protected array $searchable = ['code', 'name'];
    protected string $orderBy = 'name';
    protected string $orderDir = 'asc';

    protected function columns(): array
    {
        return ['Kode' => 'code', 'Mata pelajaran' => 'name', 'KKM' => 'kkm'];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'code', 'label' => 'Kode', 'required' => true,
                'rules' => fn ($m) => ['required', 'string', 'max:20', Rule::unique('subjects', 'code')->ignore($m?->id)]],
            ['name' => 'name', 'label' => 'Nama mata pelajaran', 'required' => true, 'rules' => 'required|string|max:100'],
            ['name' => 'kkm', 'label' => 'KKM', 'type' => 'number', 'required' => true, 'default' => 70,
                'min' => 0, 'max' => 100, 'hint' => 'Kriteria ketuntasan minimal (0–100).',
                'rules' => 'required|integer|between:0,100'],
        ];
    }
}
