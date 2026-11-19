<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends CrudController
{
    protected string $model = User::class;
    protected string $routeName = 'users';
    protected string $title = 'Akun Pengguna';
    protected string $singular = 'Akun';
    protected array $searchable = ['name', 'email'];
    protected string $orderBy = 'name';
    protected string $orderDir = 'asc';

    protected function filters(): array
    {
        return ['role' => ['label' => 'Peran', 'options' => User::ROLES]];
    }

    protected function columns(): array
    {
        return [
            'Nama'   => 'name',
            'Email'  => 'email',
            'Peran'  => fn ($r) => $this->badge($r->roleLabel(), $r->role === 'admin' ? 'blue' : 'gray'),
            'Status' => fn ($r) => $this->badge($r->is_active ? 'Aktif' : 'Nonaktif', $r->is_active ? 'green' : 'red'),
        ];
    }

    protected function fields(): array
    {
        return [
            ['name' => 'name', 'label' => 'Nama lengkap', 'required' => true, 'rules' => 'required|string|max:100'],
            ['name' => 'email', 'label' => 'Email', 'type' => 'email', 'required' => true,
                'rules' => fn ($m) => ['required', 'email', 'max:150', Rule::unique('users', 'email')->ignore($m?->id)]],
            ['name' => 'role', 'label' => 'Peran', 'type' => 'select', 'options' => User::ROLES, 'required' => true,
                'rules' => ['required', Rule::in(array_keys(User::ROLES))]],
            ['name' => 'password', 'label' => 'Password', 'type' => 'password',
                'hint' => 'Minimal 8 karakter. Saat edit, kosongkan jika tidak ingin mengubah.',
                'rules' => fn ($m) => [$m ? 'nullable' : 'required', 'string', 'min:8']],
            ['name' => 'is_active', 'label' => 'Akun aktif', 'type' => 'checkbox', 'default' => true, 'rules' => 'nullable|boolean'],
        ];
    }

    protected function beforeSave(array $data, Model $model, Request $request): array
    {
        // Admin tidak boleh menurunkan atau menonaktifkan akunnya sendiri.
        if ($model->exists && $model->id === auth()->id()) {
            $data['role'] = $model->role;
            $data['is_active'] = true;
        }

        return $data;
    }

    protected function cannotDelete(Model $model): ?string
    {
        return $model->id === auth()->id() ? 'Anda tidak dapat menghapus akun yang sedang dipakai.' : null;
    }
}
