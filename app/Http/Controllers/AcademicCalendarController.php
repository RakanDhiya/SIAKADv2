<?php

namespace App\Http\Controllers;

use App\Models\AcademicEvent;
use App\Models\AcademicYear;
use App\Models\Semester;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AcademicCalendarController extends Controller
{
    /**
     * Menampilkan kalender akademik berdasarkan bulan dan tahun.
     */
    public function index(Request $request)
    {
        $validated = $request->validate([
            'month' => ['nullable', 'integer', 'between:1,12'],
            'year' => ['nullable', 'integer', 'between:2000,2100'],
        ]);

        $month = $validated['month'] ?? now()->month;
        $year = $validated['year'] ?? now()->year;

        $startDate = Carbon::create($year, $month, 1)->startOfMonth();
        $endDate = $startDate->copy()->endOfMonth();

        $calendarDays = collect(CarbonPeriod::create(
            $startDate->copy()->startOfWeek(Carbon::MONDAY),
            $endDate->copy()->endOfWeek(Carbon::SUNDAY)
        ));

        $events = AcademicEvent::with(['academicYear', 'semester'])
            ->whereDate('start_date', '<=', $endDate)
            ->whereDate('end_date', '>=', $startDate)
            ->orderBy('start_date')
            ->orderBy('title')
            ->get();

        $editEvent = null;

        if ($request->filled('edit') && $request->user()->hasRole('admin')) {
            $editEvent = AcademicEvent::findOrFail($request->integer('edit'));
        }

        return view('modules.academic-calendar.index', [
            'events' => $events,
            'month' => $month,
            'year' => $year,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'calendarDays' => $calendarDays,
            'academicYears' => AcademicYear::orderByDesc('start_date')->get(),
            'semesters' => Semester::with('academicYear')
                ->orderByDesc('id')
                ->get(),
            'activeAcademicYear' => AcademicYear::active(),
            'editEvent' => $editEvent,
        ]);
    }

    /**
     * Menyimpan agenda kalender akademik.
     */
    public function store(Request $request)
    {
        $validated = $this->validateEvent($request);

        AcademicEvent::create($validated);

        return redirect()
            ->route('academic-calendar.index')
            ->with('success', 'Agenda akademik berhasil ditambahkan.');
    }

    /**
     * Memperbarui agenda kalender akademik.
     */
    public function update(Request $request, AcademicEvent $academicEvent)
    {
        $validated = $this->validateEvent($request);

        $academicEvent->update($validated);

        return redirect()
            ->route('academic-calendar.index')
            ->with('success', 'Agenda akademik berhasil diperbarui.');
    }

    /**
     * Menghapus agenda kalender akademik.
     */
    public function destroy(AcademicEvent $academicEvent)
    {
        $academicEvent->delete();

        return redirect()
            ->route('academic-calendar.index')
            ->with('success', 'Agenda akademik berhasil dihapus.');
    }

    /**
     * Memvalidasi data agenda kalender akademik.
     */
    private function validateEvent(Request $request): array
    {
        return $request->validate([
            'academic_year_id' => [
                'required',
                'exists:academic_years,id',
            ],
            'semester_id' => [
                'nullable',
                Rule::exists('semesters', 'id')->where(
                    fn ($query) => $query->where(
                        'academic_year_id',
                        $request->input('academic_year_id')
                    )
                ),
            ],
            'title' => ['required', 'string', 'max:150'],
            'category' => [
                'required',
                Rule::in([
                    'Akademik',
                    'Ujian',
                    'Libur',
                    'Kegiatan Sekolah',
                    'Pembagian Rapor',
                    'Lainnya',
                ]),
            ],
            'start_date' => ['required', 'date'],
            'end_date' => [
                'required',
                'date',
                'after_or_equal:start_date',
            ],
            'description' => ['nullable', 'string'],
            'location' => ['nullable', 'string', 'max:150'],
        ]);
    }
}