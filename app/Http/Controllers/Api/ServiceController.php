<?php
// File: app/Http/Controllers/Api/ServiceController.php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ServiceResource;
use App\Models\Service;
use App\Models\ServiceCategory;
use App\Repositories\Interfaces\ServiceRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\ResourceCollection;

class ServiceController extends Controller
{
    protected $serviceRepository;

    /**
     * Create a new controller instance.
     *
     * @param ServiceRepositoryInterface $serviceRepository
     */
    public function __construct(ServiceRepositoryInterface $serviceRepository)
    {
        $this->serviceRepository = $serviceRepository;
    }

    /**
     * Display a listing of the services.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function index(Request $request)
    {
        $services = Service::query()
            ->active()
            ->when($request->filled('category'), function ($query) use ($request) {
                return $query->where('category_id', $request->category);
            })
            ->when($request->filled('featured'), function ($query) {
                return $query->where('featured', true);
            })
            ->when($request->filled('search'), function ($query) use ($request) {
                return $query->where(function ($q) use ($request) {
                    $q->where('title', 'like', "%{$request->search}%")
                      ->orWhere('short_description', 'like', "%{$request->search}%")
                      ->orWhere('description', 'like', "%{$request->search}%");
                });
            })
            ->with('category')
            ->ordered()
            ->paginate($request->input('per_page', 12));
        
        return ServiceResource::collection($services);
    }

    /**
     * Display the specified service.
     *
     * @param string $slug
     * @return ServiceResource
     */
    public function show($slug)
    {
        $service = $this->serviceRepository->findBySlug($slug);
        
        if (!$service) {
            return response()->json([
                'message' => 'Service not found'
            ], 404);
        }
        
        return new ServiceResource($service);
    }
    
    /**
     * Get featured services.
     *
     * @param Request $request
     * @return ResourceCollection
     */
    public function featured(Request $request)
    {
        $limit = $request->input('limit', 6);
        
        $services = $this->serviceRepository->getFeatured($limit);
        
        return ServiceResource::collection($services);
    }
    
    /**
     * Get related services.
     *
     * @param string $slug
     * @param Request $request
     * @return ResourceCollection
     */
    public function related($slug, Request $request)
    {
        $service = $this->serviceRepository->findBySlug($slug);
        
        if (!$service) {
            return response()->json([
                'message' => 'Service not found'
            ], 404);
        }
        
        $limit = $request->input('limit', 3);
        
        $relatedServices = $this->serviceRepository->getRelated($service, $limit);
        
        return ServiceResource::collection($relatedServices);
    }
    
    /**
     * Get service categories.
     *
     * @return ResourceCollection
     */
    public function categories()
    {
        $categories = ServiceCategory::active()->ordered()->get();
        
        return response()->json([
            'data' => $categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'name' => $category->name,
                    'slug' => $category->slug,
                    'icon' => $category->icon ? asset('storage/' . $category->icon) : null,
                    'services_count' => $category->services()->active()->count()
                ];
            })
        ]);
    }
}