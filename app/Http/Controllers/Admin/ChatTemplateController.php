<?php
// app/Http/Controllers/Admin/ChatTemplateController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ChatTemplate;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class ChatTemplateController extends Controller
{
    /**
     * Display a listing of chat templates.
     */
    public function index()
    {
        $templates = ChatTemplate::orderBy('type')
            ->orderBy('name')
            ->paginate(15);

        return view('admin.chat.templates.index', compact('templates'));
    }

    /**
     * Show the form for creating a new template.
     */
    public function create()
    {
        return view('admin.chat.templates.create');
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
            ->when($type, function($query, $type) {
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