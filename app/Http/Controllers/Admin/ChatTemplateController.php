<?php
// app/Http/Controllers/Admin/ChatTemplateController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ChatTemplateController extends Controller
{
    /**
     * Display a listing of chat templates.
     */
    public function index(Request $request)
    {
        $query = ChatTemplate::query();

        // Apply filters
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%")
                    ->orWhere('trigger', 'like', "%{$search}%");
            });
        }

        $templates = $query->orderBy('type')
            ->orderBy('usage_count', 'desc')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.chat.templates.index', compact('templates'));
    }
    public function getStatistics(): JsonResponse
{
    try {
        $statistics = [
            'total_templates' => ChatTemplate::count(),
            'active_templates' => ChatTemplate::where('is_active', true)->count(),
            'by_type' => ChatTemplate::selectRaw('type, COUNT(*) as count')
                ->groupBy('type')
                ->pluck('count', 'type')
                ->toArray(),
            'most_used' => ChatTemplate::where('is_active', true)
                ->orderBy('usage_count', 'desc')
                ->limit(5)
                ->get(['id', 'name', 'type', 'usage_count']),
            'recent_templates' => ChatTemplate::latest()
                ->limit(5)
                ->get(['id', 'name', 'type', 'created_at']),
        ];

        return response()->json([
            'success' => true,
            'statistics' => $statistics
        ]);

    } catch (\Exception $e) {
        Log::error('Get template statistics failed: ' . $e->getMessage());
        
        return response()->json([
            'success' => false,
            'message' => 'Failed to get statistics'
        ], 500);
    }
}

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        return view('admin.chat.templates.create');
    }

    public function duplicate(Request $request, ChatTemplate $template)
    {
        $newTemplate = $template->replicate();
        $newTemplate->name = $template->name . ' (Copy)';
        $newTemplate->usage_count = 0;
        $newTemplate->save();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template duplicated successfully',
                'template' => $newTemplate
            ]);
        }

        return redirect()->route('admin.chat.templates.edit', $newTemplate)
            ->with('success', 'Template duplicated successfully! You can now edit the copy.');
    }

    /**
     * Store a newly created template.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:greeting,auto_response,quick_reply,offline',
            'trigger' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        ChatTemplate::create($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template created successfully'
            ]);
        }

        return redirect()->route('admin.chat.templates')
            ->with('success', 'Template created successfully!');
    }

    /**
     * Display the specified template.
     */
    public function show(ChatTemplate $template)
    {
        return view('admin.chat.templates.show', compact('template'));
    }

    /**
     * Show the form for editing the specified template.
     */
    public function edit(ChatTemplate $template)
    {
        return view('admin.chat.templates.edit', compact('template'));
    }

    /**
     * Update the specified template.
     */
    public function update(Request $request, ChatTemplate $template)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'message' => 'required|string|max:1000',
            'type' => 'required|in:greeting,auto_response,quick_reply,offline',
            'trigger' => 'nullable|string|max:100',
            'is_active' => 'boolean',
        ]);

        $template->update($validated);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Template updated successfully'
            ]);
        }

        return redirect()->route('admin.chat.templates')
            ->with('success', 'Template updated successfully!');
    }

    /**
     * Remove the specified template.
     */
    public function destroy(ChatTemplate $template)
    {
        $template->delete();

        return response()->json([
            'success' => true,
            'message' => 'Template deleted successfully'
        ]);
    }

    /**
     * Toggle template active status.
     */
    public function toggleActive(ChatTemplate $template): JsonResponse
    {
        $template->update(['is_active' => !$template->is_active]);

        return response()->json([
            'success' => true,
            'message' => $template->is_active ? 'Template activated' : 'Template deactivated',
            'is_active' => $template->is_active
        ]);
    }

    /**
     * Get templates by type for API.
     */
    public function getByType(Request $request): JsonResponse
    {
        $type = $request->get('type');

        $templates = ChatTemplate::where('is_active', true)
            ->when($type, function ($query, $type) {
                return $query->where('type', $type);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'message', 'type', 'trigger']);

        return response()->json([
            'success' => true,
            'templates' => $templates
        ]);
    }

    /**
     * Increment template usage count.
     */
    public function incrementUsage(ChatTemplate $template): JsonResponse
    {
        $template->incrementUsage();

        return response()->json([
            'success' => true,
            'usage_count' => $template->usage_count
        ]);
    }
}