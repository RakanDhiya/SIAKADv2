<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AnnouncementController extends CrudController
{
    protected string $model = Announcement::class;
    protected string $routeName = 'announcements';
    protected string $title = 'Kelola Pengumuman';
    protected string $singular = 'Pengumuman';
    protected array $searchable = ['title'];

    protected function columns(): array
    {
        return [
            'Judul'   => 'title',
            'Untuk'   => fn ($r) => Announcement::AUDIENCES[$r->audience] ?? $r->audience,
            'Terbit'  => fn ($r) => $r->published_at?->format('d/m/Y H:i') ?? '—',
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'title', 'label' => 'Judul', 'required' => true, 'rules' => 'required|string|max:150'],
            ['name' => 'audience', 'label' => 'Ditujukan untuk', 'type' => 'select', 'required' => true, 'default' => 'semua',
                'options' => Announcement::AUDIENCES, 'rules' => ['required', Rule::in(array_keys(Announcement::AUDIENCES))]],
            ['name' => 'published_at', 'label' => 'Waktu terbit', 'type' => 'datetime', 'hint' => 'Kosongkan untuk terbit sekarang.', 'rules' => 'nullable|date'],
            ['name' => 'body', 'label' => 'Isi pengumuman', 'type' => 'textarea', 'required' => true, 'rules' => 'required|string|max:5000'],
        ];
    }

    protected function beforeSave(array $data, Model $model, Request $request): array
    {
        $data['published_at'] = $data['published_at'] ?? now();

        if (! $model->exists) {
            $data['created_by'] = auth()->id();
        }

        return $data;
    }
}
