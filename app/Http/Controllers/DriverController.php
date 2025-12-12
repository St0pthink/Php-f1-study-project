<?php

namespace App\Http\Controllers;

use App\Models\Driver;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

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

        public function index()
    {
        $query = Driver::query()->with('user');
        $query->whereNull('deleted_at');
        $drivers = $query->orderBy('id')->get();

        return view('drivers.index', [
            'drivers' => $drivers,
        ]);
    }

   public function create()
    {
    Gate::authorize('driver-create');

    return view('drivers.create');
    }

    public function store(Request $request): RedirectResponse
    {
        Gate::authorize('driver-create');

        $data = $request->validate($this->rules());

        $data['user_id'] = Auth::id();

        Driver::create($data);

        return redirect()->route('drivers.manage')->with('success', 'Карточка создана');

    }

    public function show(Request $request,Driver $driver):View
    {
        if ($request->ajax()) {
        return view('drivers.part.details', compact('driver'));
        }

        return view('drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        Gate::authorize('driver-update', $driver);

        return view('drivers.edit', compact('driver'));
    }

        public function update(Request $request, Driver $driver)
    {
        Gate::authorize('driver-update', $driver);

        $data = $request->validate($this->rules());

        $driver->update($data);

        return redirect()->route('drivers.manage')->with('success', 'Карточка обновлена');
    }

    public function destroy(Driver $driver): RedirectResponse
    {
        Gate::authorize('driver-delete', $driver);
        
        $driver->delete();

        return redirect()
            ->route('drivers.manage')
            ->with('success', 'Карточка удалена (soft delete).');
    }

    public function manage(Request $request): View
    {
        $user = Auth::user();

        if ($user->is_admin) {
            // админ видит все карточки, включая удалённые, и владельцев
            $drivers = Driver::withTrashed()
                ->with('user')
                ->orderBy('id')
                ->get();
        } else {
            // обычный пользователь — только свои НЕудалённые
            $drivers = Driver::where('user_id', $user->id)
                ->orderBy('id')
                ->get();
        }

        $selectedDriver = null;
        if ($request->filled('driver_id')) {
            $selectedDriver = Driver::withTrashed()->findOrFail($request->driver_id);

            // подстраховка: обычный пользователь не может выбрать чужую карточку
            if (!$user->is_admin && $selectedDriver->user_id !== $user->id) {
                abort(403);
            }
    }

    return view('drivers.manage', compact('drivers', 'selectedDriver'));
    }

    public function restore($id)
    {
        $driver = Driver::withTrashed()->findOrFail($id);

        Gate::authorize('driver-restore', $driver);

        $driver->restore();

        return back()->with('success', 'Карточка восстановлена');
    }

    public function forceDelete($id)
    {
        $driver = Driver::withTrashed()->findOrFail($id);

        Gate::authorize('driver-force-delete', $driver);

        $driver->forceDelete();

        return redirect()
            ->route('drivers.manage')
            ->with('success', 'Карточка удалена окончательно.');
    }

    
    public function userDrivers(User $user)
    {
        $query = $user->drivers();

        // админ видит удалённые
        if (Auth::user()->is_admin) {
            $query->withTrashed();
        }

        $drivers = $query->get();

        return view('drivers.index', [
            'drivers' => $drivers,
            'owner'   => $user,
        ]);
    }

}
