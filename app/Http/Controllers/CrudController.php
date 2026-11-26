<?php

namespace App\Http\Controllers;

use Closure;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\HtmlString;

/**
 * Controller CRUD generik berbasis konfigurasi (fields + columns).
 * Setiap modul cukup mendefinisikan model, route, kolom tabel, dan field form.
 *
 * Definisi field:
 *  name, label, type (text|email|number|date|time|datetime|textarea|select|multiselect|checkbox|password|file),
 *  required (tampilan *), hint, default, rules (array|string|Closure($model)), options (array|Closure($model)),
 *  relation (untuk multiselect), min/max/step (untuk input number).
 */
abstract class CrudController extends Controller
{
    protected string $model;
    protected string $routeName;
    protected string $title;
    protected string $singular;
    protected array $with = [];
    protected array $searchable = [];
    protected bool $showable = false;
    protected string $orderBy = 'id';
    protected string $orderDir = 'desc';

    abstract protected function fields(): array;

    /** @return array<string, string|Closure> label => path relasi/atribut atau closure(model) */
    abstract protected function columns(): array;

    // ---------- Hook yang bisa di-override ----------

    protected function filters(): array { return []; }
    protected function rowActions(Model $row): array { return []; }
    protected function canWrite(): bool { return true; }
    protected function cannotDelete(Model $model): ?string { return null; }
    protected function beforeSave(array $data, Model $model, Request $request): array { return $data; }
    protected function afterSave(Model $model, Request $request): void {}

    protected function query(): Builder
    {
        return $this->model::query()->with($this->with);
    }

    protected function applyOrder(Builder $query): Builder
    {
        return $query->orderBy($this->orderBy, $this->orderDir);
    }

    protected function badge(string $text, string $color = 'gray'): HtmlString
    {
        return new HtmlString(Blade::render(
            '<x-ui.badge :color="$c">{{ $t }}</x-ui.badge>',
            ['c' => $color, 't' => $text]
        ));
    }

    // ---------- Aksi resource ----------

    public function index(Request $request)
    {
        $query = $this->query();

        $q = trim((string) $request->query('q', ''));
        if ($q !== '' && $this->searchable) {
            $query->where(function ($w) use ($q) {
                foreach ($this->searchable as $column) {
                    $w->orWhere($column, 'ilike', "%{$q}%");
                }
            });
        }

        $filters = [];
        foreach ($this->filters() as $name => $def) {
            $options = $def['options'];
            $filters[$name] = [
                'label'   => $def['label'],
                'options' => $options instanceof Closure ? $options() : $options,
            ];

            $value = $request->query($name);
            if ($value === null || $value === '') {
                continue;
            }

            isset($def['apply']) ? $def['apply']($query, $value) : $query->where($name, $value);
        }

        $rows = $this->applyOrder($query)->paginate(15)->withQueryString();

        return view('modules.crud.index', $this->viewData([
            'rows'    => $rows,
            'q'       => $q,
            'filters' => $filters,
        ]));
    }

    public function create()
    {
        abort_unless($this->canWrite(), 403);

        return view('modules.crud.form', $this->formData(new $this->model));
    }

    public function store(Request $request)
    {
        abort_unless($this->canWrite(), 403);

        $model = new $this->model;
        $data = $this->validated($request, null);
        $this->persist($model, $request, $data);

        return redirect()->route($this->routeName.'.index')
            ->with('success', $this->singular.' berhasil ditambahkan.');
    }

    public function show($id)
    {
        $model = $this->find($id);
        $rows = [];

        foreach ($this->resolvedFields($model) as $f) {
            if (($f['type'] ?? 'text') === 'password') {
                continue;
            }
            $rows[$f['label']] = $this->displayValue($f, $model);
        }

        return view('modules.crud.show', $this->viewData(['model' => $model, 'rows' => $rows]));
    }

    public function edit($id)
    {
        abort_unless($this->canWrite(), 403);

        return view('modules.crud.form', $this->formData($this->find($id)));
    }

    public function update(Request $request, $id)
    {
        abort_unless($this->canWrite(), 403);

        $model = $this->find($id);
        $data = $this->validated($request, $model);
        $this->persist($model, $request, $data);

        return redirect()->route($this->routeName.'.index')
            ->with('success', $this->singular.' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        abort_unless($this->canWrite(), 403);

        $model = $this->find($id);

        if ($message = $this->cannotDelete($model)) {
            return back()->with('error', $message);
        }

        try {
            $model->delete();
        } catch (QueryException $e) {
            return back()->with('error', $this->singular.' tidak dapat dihapus karena masih dipakai oleh data lain.');
        }

        foreach ($this->fields() as $f) {
            if (($f['type'] ?? '') === 'file' && $model->{$f['name']}) {
                Storage::disk('public')->delete($model->{$f['name']});
            }
        }

        return redirect()->route($this->routeName.'.index')
            ->with('success', $this->singular.' berhasil dihapus.');
    }

    // ---------- Internal ----------

    protected function find($id): Model
    {
        return $this->query()->findOrFail($id);
    }

    protected function viewData(array $extra = []): array
    {
        return array_merge([
            'title'      => $this->title,
            'singular'   => $this->singular,
            'routeName'  => $this->routeName,
            'columns'    => $this->columns(),
            'canWrite'   => $this->canWrite(),
            'showable'   => $this->showable,
            'searchable' => $this->searchable,
            'rowActions' => fn (Model $row) => $this->rowActions($row),
        ], $extra);
    }

    protected function formData(Model $model): array
    {
        $fields = $this->resolvedFields($model);
        $values = [];

        foreach ($fields as $f) {
            $values[$f['name']] = $this->valueFor($f, $model);
        }

        return $this->viewData(['model' => $model, 'fields' => $fields, 'values' => $values]);
    }

    protected function resolvedFields(?Model $model): array
    {
        return array_map(function (array $f) use ($model) {
            if (isset($f['options']) && $f['options'] instanceof Closure) {
                $f['options'] = ($f['options'])($model);
            }

            return $f;
        }, $this->fields());
    }

    protected function valueFor(array $f, Model $model)
    {
        $type = $f['type'] ?? 'text';

        if ($type === 'password') {
            return '';
        }

        if ($type === 'multiselect') {
            return $model->exists ? $model->{$f['relation']}->pluck('id')->all() : [];
        }

        $value = $model->{$f['name']};

        if ($value instanceof \DateTimeInterface) {
            return $value->format($type === 'datetime' ? 'Y-m-d\TH:i' : 'Y-m-d');
        }

        if ($type === 'time' && is_string($value)) {
            return substr($value, 0, 5);
        }

        return $value ?? ($f['default'] ?? null);
    }

    protected function displayValue(array $f, Model $model)
    {
        $type = $f['type'] ?? 'text';
        $options = $f['options'] ?? [];

        if ($type === 'multiselect') {
            return $model->{$f['relation']}->pluck('id')
                ->map(fn ($id) => $options[$id] ?? $id)->join(', ') ?: '—';
        }

        $value = $model->{$f['name']};

        return match (true) {
            $type === 'select'   => $options[$value] ?? '—',
            $type === 'checkbox' => $value ? 'Ya' : 'Tidak',
            $type === 'file'     => $value ? 'Ada berkas' : '—',
            $value instanceof \DateTimeInterface => $value->format($type === 'datetime' ? 'd/m/Y H:i' : 'd/m/Y'),
            default              => $value === null || $value === '' ? '—' : $value,
        };
    }

    protected function validated(Request $request, ?Model $model): array
    {
        $rules = [];
        $attributes = [];

        foreach ($this->fields() as $f) {
            $r = $f['rules'] ?? 'nullable';
            $rules[$f['name']] = $r instanceof Closure ? $r($model) : $r;
            $attributes[$f['name']] = mb_strtolower($f['label']);

            if (($f['type'] ?? '') === 'multiselect') {
                $rules[$f['name'].'.*'] = 'integer';
            }
        }

        return $request->validate($rules, [], $attributes);
    }

    protected function persist(Model $model, Request $request, array $data): void
    {
        DB::transaction(function () use ($model, $request, $data) {
            foreach ($this->fields() as $f) {
                $name = $f['name'];
                $type = $f['type'] ?? 'text';

                if ($type === 'file') {
                    if ($request->hasFile($name)) {
                        if ($model->{$name}) {
                            Storage::disk('public')->delete($model->{$name});
                        }

                        $data[$name] = $request->file($name)
                            ->store($this->routeName, 'public');
                    } else {
                        unset($data[$name]);
                    }
                } elseif ($type === 'password') {
                    if (empty($data[$name])) {
                        unset($data[$name]);
                    }
                } elseif ($type === 'checkbox') {
                    $data[$name] = $request->boolean($name);
                } elseif ($type === 'multiselect') {
                    unset($data[$name]);
                }
            }

            $data = $this->beforeSave($data, $model, $request);

            $model->forceFill($data)->save();

            foreach ($this->fields() as $f) {
                if (($f['type'] ?? '') === 'multiselect') {
                    $model->{$f['relation']}()->sync(
                        $request->input($f['name'], [])
                    );
                }
            }

            $this->afterSave($model, $request);
        });
    }
}