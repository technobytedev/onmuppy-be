<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ServiceController extends Controller
{
    /**
     * List all services of the authenticated vendor.
     */
    public function index(Request $request)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor profile not found.'], 404);
        }

        $services = $vendor->services()
            ->latest()
            ->get();

        return response()->json($services);
    }

    /**
     * Create a new service.
     */
    public function store(Request $request)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'Vendor profile not found.'], 404);
        }

        if (!$vendor->isServiceProvider()) {
            return response()->json([
                'message' => 'Only freelance or home-based vendors can post services.'
            ], 403);
        }

        $validated = $request->validate([
            'name'             => ['required', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'price'            => ['required', 'numeric', 'min:0'],
            'price_type'       => ['required', Rule::in(['fixed', 'hourly', 'starting_at', 'free_quote'])],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'category'         => ['required', 'string', 'max:100'],
            'image'            => ['nullable', 'image', 'max:2048'],
            'is_available'     => ['boolean'],
            'is_home_service'  => ['boolean'],
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('services', 'public');
        }

        $service = $vendor->services()->create([
            ...$validated,
            'slug'  => Str::slug($validated['name']) . '-' . Str::random(6),
            'image' => $imagePath,
        ]);

        return response()->json($service, 201);
    }

    /**
     * Show a single service.
     */
    public function show(Request $request, Service $service)
    {
        // Public — anyone can view a service
        return response()->json($service->load('vendor'));
    }

    /**
     * Update a service.
     */
    public function update(Request $request, Service $service)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor || $service->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'name'             => ['sometimes', 'string', 'max:255'],
            'description'      => ['nullable', 'string'],
            'price'            => ['sometimes', 'numeric', 'min:0'],
            'price_type'       => ['sometimes', Rule::in(['fixed', 'hourly', 'starting_at', 'free_quote'])],
            'duration_minutes' => ['nullable', 'integer', 'min:1'],
            'category'         => ['sometimes', 'string', 'max:100'],
            'image'            => ['nullable', 'image', 'max:2048'],
            'is_available'     => ['boolean'],
            'is_home_service'  => ['boolean'],
        ]);

        // Handle image upload
        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('services', 'public');
        }

        // Update slug if name changed
        if (isset($validated['name'])) {
            $validated['slug'] = Str::slug($validated['name']) . '-' . Str::random(6);
        }

        $service->update($validated);

        return response()->json($service->fresh());
    }

    /**
     * Toggle availability on/off quickly.
     */
    public function toggleAvailability(Request $request, Service $service)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor || $service->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $service->update(['is_available' => !$service->is_available]);

        return response()->json([
            'message'      => 'Availability updated.',
            'is_available' => $service->is_available,
        ]);
    }

    /**
     * Delete a service (soft delete).
     */
    public function destroy(Request $request, Service $service)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor || $service->vendor_id !== $vendor->id) {
            return response()->json(['message' => 'Unauthorized.'], 403);
        }

        $service->delete();

        return response()->json(['message' => 'Service deleted.']);
    }

    /**
     * Browse all available services publicly (map/list discovery).
     */
    public function browse(Request $request)
    {
        $query = Service::query()
            ->where('is_available', true)
            ->with(['vendor:id,business_name,slug,latitude,longitude,type,is_verified,service_radius_km'])
            ->latest();

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        // Filter by price type
        if ($request->filled('price_type')) {
            $query->where('price_type', $request->price_type);
        }

        // Filter home service only
        if ($request->boolean('home_service')) {
            $query->where('is_home_service', true);
        }

        // Search by name or description
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
            });
        }

        return response()->json($query->paginate(20));
    }
}