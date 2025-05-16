<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BannerCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BannerCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = BannerCategory::orderBy('display_order')->paginate(10);
        
        return view('admin.banner-categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.banner-categories.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:banner_categories',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        BannerCategory::create($validated);

        return redirect()->route('admin.banner-categories.index')
            ->with('success', 'Banner category created successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(BannerCategory $bannerCategory)
    {
        return view('admin.banner-categories.edit', compact('bannerCategory'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, BannerCategory $bannerCategory)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:banner_categories,slug,' . $bannerCategory->id,
            'description' => 'nullable|string',
            'is_active' => 'boolean',
            'display_order' => 'nullable|integer',
        ]);

        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }

        $bannerCategory->update($validated);

        return redirect()->route('admin.banner-categories.index')
            ->with('success', 'Banner category updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(BannerCategory $bannerCategory)
    {
        // Check if category has banners
        if ($bannerCategory->banners()->count() > 0) {
            return redirect()->route('admin.banner-categories.index')
                ->with('error', 'Cannot delete category with associated banners.');
        }
        
        $bannerCategory->delete();

        return redirect()->route('admin.banner-categories.index')
            ->with('success', 'Banner category deleted successfully.');
    }
}