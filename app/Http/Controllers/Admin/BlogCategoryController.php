<?php
// File: app/Http/Controllers/Admin/BlogCategoryController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PostCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $categories = PostCategory::withCount('posts')
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where('name', 'like', "%{$request->search}%");
            })
            ->orderBy('name')
            ->paginate(10);
        
        return view('admin.blog.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create()
    {
        return view('admin.blog.categories.create');
    }

    /**
     * Store a newly created category.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:post_categories,slug',
            'description' => 'nullable|string',
        ]);
        
        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Create category
        PostCategory::create($validated);
        
        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category created successfully!');
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(PostCategory $category)
    {
        return view('admin.blog.categories.edit', compact('category'));
    }

    /**
     * Update the specified category.
     */
    public function update(Request $request, PostCategory $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:post_categories,slug,' . $category->id,
            'description' => 'nullable|string',
        ]);
        
        // Generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        
        // Update category
        $category->update($validated);
        
        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category updated successfully!');
    }

    /**
     * Remove the specified category.
     */
    public function destroy(PostCategory $category)
    {
        // Check if category has posts
        $postsCount = $category->posts()->count();
        if ($postsCount > 0) {
            return redirect()->route('admin.blog.categories.index')
                ->with('error', "Cannot delete category that has {$postsCount} associated posts. Please reassign the posts first.");
        }
        
        // Delete category
        $category->delete();
        
        return redirect()->route('admin.blog.categories.index')
            ->with('success', 'Category deleted successfully!');
    }
}