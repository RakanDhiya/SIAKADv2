<?php

namespace App\Http\Controllers;

use App\Models\Major;
use Illuminate\Validation\Rule;

class MajorController extends CrudController
{
    protected string $model = Major::class;
    protected string $routeName = 'majors';
    protected string $title = 'Jurusan / Program Keahlian';
    protected string $singular = 'Jurusan';
    protected array $searchable = ['code', 'name'];
    protected string $orderBy = 'name';
    protected string $orderDir = 'asc';

    protected function columns(): array
    {
        return ['Kode' => 'code', 'Nama jurusan' => 'name'];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'code', 'label' => 'Kode', 'required' => true,
                'rules' => fn ($m) => ['required', 'string', 'max:20', Rule::unique('majors', 'code')->ignore($m?->id)]],
            ['name' => 'name', 'label' => 'Nama jurusan', 'required' => true, 'rules' => 'required|string|max:100'],
        ];
    }
}
