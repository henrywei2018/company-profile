<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Http\Requests\StoreServiceRequest;
use App\Http\Requests\UpdateServiceRequest;
use App\Repositories\Interfaces\ServiceRepositoryInterface;
use App\Services\FileUploadService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ServiceController extends Controller
{
    protected $serviceRepository;
    protected $fileUploadService;

    /**
     * Create a new controller instance.
     *
     * @param ServiceRepositoryInterface $serviceRepository
     * @param FileUploadService $fileUploadService
     */
    public function __construct(
        ServiceRepositoryInterface $serviceRepository,
        FileUploadService $fileUploadService
    ) {
        $this->serviceRepository = $serviceRepository;
        $this->fileUploadService = $fileUploadService;
    }

    /**
     * Display a listing of the services.
     */
    public function index(Request $request)
    {
        // Apply filters and pagination
        $query = Service::with('category')
            ->when($request->filled('category'), function ($query) use ($request) {
                return $query->where('category_id', $request->category);
            })
            ->when($request->filled('status'), function ($query) use ($request) {
                return $query->where('is_active', $request->status === 'active');
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                        ->orWhere('short_description', 'like', "%{$request->search}%");
                });
            });

        $services = $query->ordered()->paginate(10);
        
        // Get categories for filter dropdown
        $categories = ServiceCategory::active()->get();
        
        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.services.index', compact('services', 'categories', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Show the form for creating a new service.
     */
    public function create()
    {
        $categories = ServiceCategory::active()->get();
        
        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.services.create', compact('categories', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Store a newly created service.
     */
    public function store(StoreServiceRequest $request)
    {
        // Create the service
        $serviceData = $request->validated();
        
        // Generate slug if not provided
        if (empty($serviceData['slug'])) {
            $serviceData['slug'] = Str::slug($serviceData['title']);
        }
        
        $service = $this->serviceRepository->create($serviceData);
        
        // Handle icon upload
        if ($request->hasFile('icon')) {
            $path = $this->fileUploadService->uploadImage(
                $request->file('icon'),
                'services/icons',
                null,
                200,
                200
            );
            $service->update(['icon' => $path]);
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            $path = $this->fileUploadService->uploadImage(
                $request->file('image'),
                'services/images',
                null,
                800
            );
            $service->update(['image' => $path]);
        }
        
        // Handle SEO
        if ($request->filled('meta_title') || $request->filled('meta_description') || $request->filled('meta_keywords')) {
            $service->updateSeo([
                'title' => $request->meta_title,
                'description' => $request->meta_description,
                'keywords' => $request->meta_keywords,
            ]);
        }
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Service created successfully!');
    }

    /**
     * Display the specified service.
     */
    public function show(Service $service)
    {
        $service->load('category', 'seo', 'quotations');
        
        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.services.show', compact('service', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Show the form for editing the specified service.
     */
    public function edit(Service $service)
    {
        $service->load('category', 'seo');
        $categories = ServiceCategory::active()->get();
        
        // Get unread messages and pending quotations counts for header notifications
        $unreadMessages = \App\Models\Message::unread()->count();
        $pendingQuotations = \App\Models\Quotation::pending()->count();
        
        return view('admin.services.edit', compact('service', 'categories', 'unreadMessages', 'pendingQuotations'));
    }

    /**
     * Update the specified service.
     */
    public function update(UpdateServiceRequest $request, Service $service)
    {
        // Update the service
        $serviceData = $request->validated();
        
        // Generate slug if not provided
        if (empty($serviceData['slug'])) {
            $serviceData['slug'] = Str::slug($serviceData['title']);
        }
        
        $this->serviceRepository->update($service, $serviceData);
        
        // Handle icon upload
        if ($request->hasFile('icon')) {
            // Delete old icon if exists
            if ($service->icon) {
                Storage::disk('public')->delete($service->icon);
            }
            
            $path = $this->fileUploadService->uploadImage(
                $request->file('icon'),
                'services/icons',
                null,
                200,
                200
            );
            $service->update(['icon' => $path]);
        }
        
        // Handle image upload
        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($service->image) {
                Storage::disk('public')->delete($service->image);
            }
            
            $path = $this->fileUploadService->uploadImage(
                $request->file('image'),
                'services/images',
                null,
                800
            );
            $service->update(['image' => $path]);
        }
        
        // Handle SEO
        $service->updateSeo([
            'title' => $request->meta_title,
            'description' => $request->meta_description,
            'keywords' => $request->meta_keywords,
        ]);
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Service updated successfully!');
    }

    /**
     * Remove the specified service.
     */
    public function destroy(Service $service)
    {
        // Check if service has quotations
        if ($service->quotations()->count() > 0) {
            return redirect()->route('admin.services.index')
                ->with('error', 'Cannot delete service with associated quotations!');
        }
        
        // Delete icon
        if ($service->icon) {
            Storage::disk('public')->delete($service->icon);
        }
        
        // Delete image
        if ($service->image) {
            Storage::disk('public')->delete($service->image);
        }
        
        // Delete service
        $this->serviceRepository->delete($service);
        
        return redirect()->route('admin.services.index')
            ->with('success', 'Service deleted successfully!');
    }
    
    /**
     * Toggle active status
     */
    public function toggleActive(Service $service)
    {
        $service = $this->serviceRepository->toggleActive($service);
        
        return redirect()->back()
            ->with('success', 'Service status updated!');
    }
    
    /**
     * Toggle featured status
     */
    public function toggleFeatured(Service $service)
    {
        $service = $this->serviceRepository->toggleFeatured($service);
        
        return redirect()->back()
            ->with('success', 'Service featured status updated!');
    }
    
    /**
     * Update sort order
     */
    public function updateOrder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
            'order.*' => 'integer|exists:services,id',
        ]);
        
        foreach ($request->order as $index => $id) {
            Service::where('id', $id)->update(['sort_order' => $index + 1]);
        }
        
        return response()->json(['success' => true]);
    }
}