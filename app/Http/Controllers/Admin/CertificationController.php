<?php
// File: app/Http/Controllers/Admin/CertificationController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Certification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Services\FileUploadService;

class CertificationController extends Controller
{
    protected $fileUploadService;

    /**
     * Create a new controller instance.
     *
     * @param FileUploadService $fileUploadService
     */
    public function __construct(FileUploadService $fileUploadService)
    {
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display a listing of the certifications.
     */
    public function index(Request $request)
    {
        $certifications = Certification::when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('name', 'like', "%{$request->search}%")
                      ->orWhere('issuer', 'like', "%{$request->search}%");
                });
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('is_active', $request->status === 'active');
            })
            ->when($request->filled('valid'), function ($query) use ($request) {
                if ($request->valid === 'valid') {
                    return $query->valid();
                } else {
                    return $query->where(function ($q) {
                        $q->whereNotNull('expiry_date')
                          ->where('expiry_date', '<', now());
                    });
                }
            })
            ->ordered()
            ->paginate(10);
        
        return view('admin.certifications.index', compact('certifications'));
    }

    /**
     * Show the form for creating a new certification.
     */
    public function create()
    {
        return view('admin.certifications.create');
    }

    /**
     * Store a newly created certification.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        
        // If sort_order not specified, set it to the max + 1
        if (!isset($validated['sort_order'])) {
            $validated['sort_order'] = Certification::max('sort_order') + 1;
        }
        
        // Create certification
        $certification = Certification::create($validated);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $this->fileUploadService->uploadImage(
                $request->file('image'),
                'certifications',
                null,
                800
            );
            $certification->update(['image' => $path]);
        }
        
        return redirect()->route('admin.certifications.index')
            ->with('success', 'Certification created successfully!');
    }

    /**
     * Display the specified certification.
     */
    public function show(Certification $certification)
    {
        return view('admin.certifications.show', compact('certification'));
    }

    /**
     * Show the form for editing the specified certification.
     */
    public function edit(Certification $certification)
    {
        return view('admin.certifications.edit', compact('certification'));
    }

    /**
     * Update the specified certification.
     */
    public function update(Request $request, Certification $certification)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'issuer' => 'required|string|max:255',
            'description' => 'nullable|string',
            'issue_date' => 'nullable|date',
            'expiry_date' => 'nullable|date|after_or_equal:issue_date',
            'is_active' => 'boolean',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:2048',
        ]);
        
        // Update certification
        $certification->update($validated);
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($certification->image) {
                Storage::disk('public')->delete($certification->image);
            }
            
            $path = $this->fileUploadService->uploadImage(
                $request->file('image'),
                'certifications',
                null,
                800
            );
            $certification->update(['image' => $path]);
        }
        
        return redirect()->route('admin.certifications.index')
            ->with('success', 'Certification updated successfully!');
    }

    /**
     * Remove the specified certification.
     */
    public function destroy(Certification $certification)
    {
        // Delete image
        if ($certification->image) {
            Storage::disk('public')->delete($certification->image);
        }
        
        // Delete certification
        $certification->delete();
        
        return redirect()->route('admin.certifications.index')
            ->with('success', 'Certification deleted successfully!');
    }
    
    /**
     * Toggle active status
     */
    public function toggleActive(Certification $certification)
    {
        $certification->update([
            'is_active' => !$certification->is_active
        ]);
        
        return redirect()->back()
            ->with('success', 'Certification status updated!');
    }
    
    /**
     * Update sort order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:certifications,id',
        ]);
        
        foreach ($request->order as $index => $id) {
            Certification::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        
        return response()->json(['success' => true]);
    }
}