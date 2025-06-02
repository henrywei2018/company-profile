<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Project;
use App\Models\Quotation;
use App\Models\Message;
use App\Models\Post;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('admin');
    }

    public function dashboard()
    {
        $user = auth()->user();
        
        // Get dashboard statistics based on user permissions
        $stats = [];
        
        if ($user->can('view projects')) {
            $stats['total_projects'] = Project::count();
            $stats['active_projects'] = Project::where('status', 'in_progress')->count();
            $stats['completed_projects'] = Project::where('status', 'completed')->count();
        }
        
        if ($user->can('view quotations')) {
            $stats['total_quotations'] = Quotation::count();
            $stats['pending_quotations'] = Quotation::where('status', 'pending')->count();
            $stats['approved_quotations'] = Quotation::where('status', 'approved')->count();
        }
        
        if ($user->can('view messages')) {
            $stats['total_messages'] = Message::count();
            $stats['unread_messages'] = Message::where('is_read', false)->count();
            $stats['unreplied_messages'] = Message::where('is_replied', false)->where('type', '!=', 'reply')->count();
        }
        
        if ($user->can('view users')) {
            $stats['total_users'] = User::count();
            $stats['active_users'] = User::where('is_active', true)->count();
        }
        
        if ($user->can('view posts')) {
            $stats['total_posts'] = Post::count();
            $stats['published_posts'] = Post::where('status', 'published')->count();
            $stats['draft_posts'] = Post::where('status', 'draft')->count();
        }

        // Get recent activities based on permissions
        $recent_activities = [];
        
        if ($user->can('view quotations')) {
            $recent_quotations = Quotation::with(['service'])
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            $recent_activities['quotations'] = $recent_quotations;
        }
        
        if ($user->can('view messages')) {
            $recent_messages = Message::whereNull('parent_id')
                ->orderBy('created_at', 'desc')
                ->limit(5)
                ->get();
            $recent_activities['messages'] = $recent_messages;
        }
        
        if ($user->can('view projects')) {
            $recent_projects = Project::with(['category'])
                ->orderBy('updated_at', 'desc')
                ->limit(5)
                ->get();
            $recent_activities['projects'] = $recent_projects;
        }
        
        return view('admin.dashboard', compact('stats', 'recent_activities'));
    }
}