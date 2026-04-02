<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Location;
use App\Models\ProductionLine;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LocationController extends Controller
{
    public function index(Request $request): View
    {
        $query = Location::query();

        if ($request->filled('q')) {
            $q = (string) $request->input('q');
            $query->where(function ($sub) use ($q) {
                $sub->where('name', 'like', "%{$q}%")
                    ->orWhere('zone', 'like', "%{$q}%")
                    ->orWhere('rack', 'like', "%{$q}%")
                    ->orWhere('row', 'like', "%{$q}%");
            });
        }
        if ($request->filled('zone')) {
            $query->where('zone', 'like', '%'.$request->input('zone').'%');
        }
        if ($request->filled('rack')) {
            $query->where('rack', 'like', '%'.$request->input('rack').'%');
        }
        if ($request->filled('row')) {
            $query->where('row', 'like', '%'.$request->input('row').'%');
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->input('status') === 'active');
        }

        $locations = $query->orderBy('name')->paginate(15)->withQueryString();
        $productionLines = ProductionLine::query()->orderBy('name')->get();

        return view('admin.locations.index', compact('locations', 'productionLines'));
    }

    public function create(): View
    {
        return view('admin.locations.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $locationType = (string) $request->validate([
            'location_type' => 'required|in:item,production_line',
        ])['location_type'];

        if ($locationType === 'item') {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'zone' => 'nullable|string|max:100',
                'rack' => 'nullable|string|max:100',
                'row'  => 'nullable|string|max:100',
                'is_active' => 'nullable|boolean',
            ]);

            $validated['is_active'] = (bool)($validated['is_active'] ?? true);

            $duplicate = Location::where('name', $validated['name'])
                ->where('zone', $validated['zone'] ?? null)
                ->where('rack', $validated['rack'] ?? null)
                ->where('row', $validated['row'] ?? null)
                ->exists();

            if ($duplicate) {
                return back()->withErrors(['name' => 'Lokasi sudah ada (kombinasi nama/zone/rack/row).'])->withInput();
            }

            Location::create($validated);

            return redirect()->route('admin.locations.index')->with('success', __('messages.location.created'));
        }

        $validated = $request->validate([
            'production_line_name' => 'required|string|max:255|unique:production_lines,name',
            'production_line_is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = (bool)($validated['production_line_is_active'] ?? true);
        unset($validated['production_line_is_active']);

        ProductionLine::create([
            'name' => $validated['production_line_name'],
            'is_active' => $validated['is_active'],
        ]);

        return redirect()->route('admin.locations.index')->with('success', 'Line produksi berhasil dibuat.');
    }

    public function edit(Location $location): View
    {
        return view('admin.locations.edit', compact('location'));
    }

    public function update(Request $request, Location $location): RedirectResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'zone' => 'nullable|string|max:100',
            'rack' => 'nullable|string|max:100',
            'row'  => 'nullable|string|max:100',
            'is_active' => 'nullable|boolean',
        ]);

        $validated['is_active'] = (bool)($validated['is_active'] ?? false);

        $duplicate = Location::where('name', $validated['name'])
            ->where('zone', $validated['zone'] ?? null)
            ->where('rack', $validated['rack'] ?? null)
            ->where('row', $validated['row'] ?? null)
            ->where('id', '!=', $location->id)
            ->exists();

        if ($duplicate) {
            return back()->withErrors(['name' => 'Lokasi sudah ada (kombinasi nama/zone/rack/row).'])->withInput();
        }

        $location->update($validated);

        return redirect()->route('admin.locations.index')->with('success', __('messages.location.updated'));
    }

    public function destroy(Location $location): RedirectResponse
    {
        $location->delete();
        return redirect()->route('admin.locations.index')->with('success', __('messages.location.deleted'));
    }
}


