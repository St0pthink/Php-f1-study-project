<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class DriverController extends Controller
{
    private function rules(): array
    {
        return [
            'title'             => 'required|string|max:255',
            'track_name'        => 'required|string|max:255',
            'short_description' => 'required|string',
            'details_html'      => 'required|string',
            'image_path'        => 'nullable|string|max:255',
        ];
    }

    public function index(): View
    {
        $drivers = Driver::all();
        return view('drivers.index', compact('drivers'));
    }

    public function create():View
    {
        return view('drivers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $driver = Driver::create($validated);

        return redirect()
            ->route('drivers.manage', ['driver_id' => $driver->id])
            ->with('success', 'Карточка добавлена.');
    }

    public function show(Request $request,Driver $driver):View
    {
        if ($request->ajax()) {
        return view('drivers.part.details', compact('driver'));
        }

        return view('drivers.show', compact('driver'));
    }

    public function edit($id):View
    {
        return view('drivers.edit', compact('driver'));
    }

        public function update(Request $request, Driver $driver): RedirectResponse
    {
        $validated = $request->validate($this->rules());

        $driver->update($validated);

        return redirect()
            ->route('drivers.manage', ['driver_id' => $driver->id])
            ->with('success', 'Карточка обновлена.');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        $driver->delete();

        return redirect()
            ->route('drivers.manage')
            ->with('success', 'Карточка удалена (soft delete).');
    }

    public function manage(Request $request): View
    {
        $drivers = Driver::orderBy('title')->get();

        $selectedId = $request->query('driver_id');
        $selectedDriver = $selectedId
            ? Driver::find($selectedId)
            : null;

        return view('drivers.manage', [
            'drivers'        => $drivers,
            'selectedDriver' => $selectedDriver,
        ]);
    }
}
