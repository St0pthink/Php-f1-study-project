<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\DriverResource;
use App\Models\Driver;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class DriverController extends Controller
{
    private function rules(bool $forUpdate = false): array
    {
        $prefix = $forUpdate ? 'sometimes|' : '';
        return [
            'title' => $prefix . 'required|string|max:255',
            'track_name' => $prefix . 'required|string|max:255',
            'short_description' => $prefix . 'required|string',
            'details_html' => $prefix . 'required|string',
            'image_path' => 'nullable|string|max:255',
        ];
    }

    public function index(): AnonymousResourceCollection
    {
        $drivers = Driver::with('user')
            ->whereNull('deleted_at')
            ->orderBy('id')
            ->paginate(15);

        return DriverResource::collection($drivers);
    }

    public function show(Driver $driver): DriverResource
    {
        $driver->load('user');
        return new DriverResource($driver);
    }

    public function store(Request $request): JsonResponse
    {
        Gate::authorize('driver-create');
        $validated = $request->validate($this->rules());
        $validated['user_id'] = Auth::id();
        $driver = Driver::create($validated);
        $driver->load('user');

        return response()->json([
            'message' => 'Driver created successfully',
            'data' => new DriverResource($driver),
        ], 201);
    }

    public function update(Request $request, Driver $driver): JsonResponse
    {
        Gate::authorize('driver-update', $driver);
        $validated = $request->validate($this->rules(true));
        $driver->update($validated);
        $driver->load('user');

        return response()->json([
            'message' => 'Driver updated successfully',
            'data' => new DriverResource($driver),
        ], 200);
    }
}
