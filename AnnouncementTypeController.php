<?php

namespace App\Http\Controllers\Admin;

use App;
use App\Http\Controllers\Admin\Controller;
use App\Models\Announcement;
use App\Models\AnnouncementType;
use App\Models\Country;
use DB;
use Illuminate\Http\Request;

class AnnouncementTypeController extends Controller
{
    private $rules;
    private $locales;

    public function __construct()
    {
        $this->locales = config('ysite.locales');
    }

    public function index(Request $request)
    {
        $validated = $request->validate([
            'filter'        => 'sometimes|required|array',
            'filter.status' => 'nullable|boolean',
        ]);

        $vacancy_types = AnnouncementType::orderBy('ordering', 'desc')
            ->orderBy('id', 'desc')
            ->where(function ($q) use ($validated) {
                if (isset($validated['filter']['status'])) {
                    $q->where('status', $validated['filter']['status']);
                }
            })
            ->select([
                'id',
                'status',
            ])
            ->paginate(20);

        return view('admin.vacancy_types.index')->with(compact('vacancy_types'));
    }

    public function json(Request $request)
    {
        $validated = $request->validate([
            'filter.search' => 'nullable|string|min:1|max:255',
            'filter.status' => 'nullable|boolean',
        ]);

        return AnnouncementType::withTranslation()
            ->orderBy('ordering', 'desc')
            ->orderBy('id', 'desc')
            ->where(function ($q) use ($validated) {
                if (isset($validated['filter']['status'])) {
                    $q->where('status', $validated['filter']['status']);
                }

                if (isset($validated['filter']['search'])) {
                    $q->whereHas('translations', function ($q) use ($validated) {
                        $q->where('name', 'LIKE', '%' . $validated['filter']['search'] . '%');
                    });
                }
            })
            ->select('vacancy_types.id')
            ->get()
            ->each(function ($vacancy_type) {
                $vacancy_type->makeHidden('translations');
            });
    }

    public function create()
    {
        $locales        = $this->locales;
        $default_locale = config('ysite.default_locale');

        $countries = Country::where('id', 1)->withTranslation()->select('id')->get();

        return view('admin.vacancy_types.create')->with(compact(
            'locales',
            'default_locale',
            'countries'
        ));
    }

    public function store(Request $request)
    {
        $request->merge([
            'changeable' => $request->filled('changeable'),
            'status'     => $request->filled('status'),
        ]);

        $rules = [
            'changeable' => ['required', 'boolean'],
            'status'     => ['required', 'boolean'],
            'ordering'   => ['nullable', 'sometimes', 'integer'],
            'amount'     => ['required', 'array'],
            'amount.*'   => ['numeric'],
        ];

        foreach ($this->locales as $code) {
            $rules += [
                $code . '.title' => 'required|string',
            ];
        }

        $validated = $request->validate($rules);
        if (!isset($validated['ordering']) || !$validated['ordering']) {
            $validated['ordering'] = 0;
        }

        $vacancy_type = AnnouncementType::create($validated);

        $inserts = [];
        $now     = now();
        foreach ($validated['amount'] as $country_id => $amount) {
            $inserts[] = "(NULL, {$vacancy_type->id}, {$country_id}, {$amount}, '{$now}', '{$now}', NULL)";
        }

        DB::statement("INSERT INTO vacancy_type_values VALUES " . implode(', ', $inserts));

        return redirect()->route('admin.vacancies.index')->with('success', __('Created'));
    }

    public function edit(AnnouncementType $vacancy_type)
    {
        $locales        = $this->locales;
        $default_locale = config('ysite.default_locale');

        $vacancy_type->loadMissing([
            'translations',
            'values',
        ]);

        $values = $vacancy_type->values->pluck('amount', 'country_id');

        $countries = Country::where('id', 1)->withTranslation()->select('id')->get();

        return view('admin.vacancy_types.edit')->with(compact(
            'locales',
            'default_locale',
            'values',
            'countries',
            'vacancy_type'
        ));
    }

    public function update(Request $request, AnnouncementType $vacancy_type)
    {
        $request->merge([
            'changeable' => $request->filled('changeable'),
            'status'     => $request->filled('status'),
        ]);

        $rules = [
            'changeable' => ['required', 'boolean'],
            'status'     => ['required', 'boolean'],
            'ordering'   => ['nullable', 'sometimes', 'integer'],
            'amount'     => ['required', 'array'],
            'amount.*'   => ['numeric'],
        ];

        foreach ($this->locales as $code) {
            $rules += [
                $code . '.title' => 'required|string',
            ];
        }

        $validated = $request->validate($rules);
        if (!isset($validated['ordering']) || !$validated['ordering']) {
            $validated['ordering'] = 0;
        }

        $vacancy_type->update($validated);

        $inserts = [];
        $now     = now();
        foreach ($validated['amount'] as $country_id => $amount) {
            $inserts[] = "(NULL, {$vacancy_type->id}, {$country_id}, {$amount}, '{$now}', '{$now}', NULL)";
        }

        DB::statement("INSERT INTO vacancy_type_values VALUES " . implode(', ', $inserts) . " ON DUPLICATE KEY UPDATE amount=VALUES(amount)");

        return back()->with('success', __('Updated'));
    }

    public function destroy(AnnouncementType $vacancy_type)
    {

        $used = Announcement::where('type_id', $vacancy_type->id)->first() ? true : false;

        if ($used) {
            return back()->with('error', __("You can't delete vacancy type with vacancies"));
        }

        $vacancy_type->deleteTranslations();
        $vacancy_type->delete();

        return back()->with('success', __('Deleted'));
    }
}
