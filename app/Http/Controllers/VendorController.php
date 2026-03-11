<?php

namespace App\Http\Controllers;

use App\Models\Vendor;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class VendorController extends Controller
{
    /**
     * Public list of all vendors with coordinates for map display.
     */
    public function index(Request $request)
    {
        $vendors = Vendor::query()
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->where('is_open', true)
            ->withCount('activeFlashAlerts')
            ->with('activeFlashAlerts')
            ->when($request->filled('category'), fn($q) =>
                $q->where('category', $request->category)
            )
            ->when($request->filled('type'), fn($q) =>
                $q->where('type', $request->type)
            )
            ->when($request->filled('search'), fn($q) =>
                $q->where('business_name', 'like', '%' . $request->search . '%')
            )
            ->get();

        return response()->json($vendors);
    }
    /**
     * Get my vendor profile.
     */
    public function show(Request $request)
    {
        $vendor = $request->user()->vendor;

        if (!$vendor) {
            return response()->json(['message' => 'No vendor profile found.'], 404);
        }

        return response()->json($vendor);
    }

    /**
     * Create my vendor profile.
     */
    public function store(Request $request)
    {
        if ($request->user()->vendor) {
            return response()->json(['message' => 'Vendor profile already exists.'], 422);
        }

        $validated = $request->validate([
            'business_name'     => ['required', 'string', 'max:255'],
            'description'       => ['nullable', 'string'],
            'category'          => ['required', 'string'],
            'phone'             => ['nullable', 'string'],
            'type'              => ['required', 'in:fixed,mobile,home_based,freelance'],
            'address'           => ['nullable', 'string'],
            'latitude'          => ['nullable', 'numeric'],
            'longitude'         => ['nullable', 'numeric'],
            'service_radius_km' => ['nullable', 'numeric', 'min:0'],
        ]);

        $vendor = $request->user()->vendor()->create([
            ...$validated,
            'slug' => Str::slug($validated['business_name']) . '-' . Str::random(6),
        ]);

        // Update user role to vendor
        $request->user()->update(['role' => 'vendor']);

        return response()->json($vendor, 201);
    }
}