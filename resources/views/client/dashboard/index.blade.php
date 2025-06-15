<!-- resources/views/client/dashboard/index.blade.php -->
<!-- Example of how to integrate the message widget into your main dashboard -->

<x-layouts.admin title="Dashboard" :unreadMessages="$unreadMessages ?? 0" :pendingQuotations="$pendingQuotations ?? 0">
    
    <!-- Dashboard Header -->
    <div class="mb-8">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">
            Welcome back, {{ auth()->user()->name }}!
        </h1>
        <p class="text-gray-600 dark:text-gray-400">
            Here's what's happening with your projects and communications
        </p>
    </div>

    <!-- Dashboard Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Main Content Area -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Project Overview Widget -->
            <x-admin.card>
                <x-slot name="title">Project Overview</x-slot>
                <!-- Your existing project overview content -->
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    Project overview content here...
                </div>
            </x-admin.card>
            
            <!-- Recent Activities -->
            <x-admin.card>
                <x-slot name="title">Recent Activities</x-slot>
                <!-- Your existing activities content -->
                <div class="text-center py-8 text-gray-500 dark:text-gray-400">
                    Recent activities content here...
                </div>
            </x-admin.card>
            
        </div>
        
        <!-- Sidebar -->
        <div class="space-y-6">
            
            <!-- Messages Widget -->
            @include('client.dashboard.widgets.messages', [
                'messageSummary' => $messageSummary ?? [],
                'recentActivity' => $recentActivity ?? []
            ])
            
            <!-- Quick Links Widget -->
            <x-admin.card>
                <x-slot name="title">Quick Links</x-slot>
                
                <div class="space-y-2">
                    <a href="{{ route('client.projects.index') }}" class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md transition-colors">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        View All Projects
                    </a>
                    
                    <a href="{{ route('client.messages.index') }}" class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md transition-colors">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        All Messages
                    </a>
                    
                    <a href="{{ route('client.quotations.index') }}" class="flex items-center p-3 text-sm text-gray-700 dark:text-gray-300 hover:bg-gray-50 dark:hover:bg-gray-700 rounded-md transition-colors">
                        <svg class="w-5 h-5 text-gray-400 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 002.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 00-1.123-.08m-5.801 0c-.065.21-.1.433-.1.656 0 .414.336.75.75.75h4.5a.75.75 0 00.75-.75 2.25 2.25 0 00-.1-.664m-5.8 0A2.251 2.251 0 0113.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25zM6.75 12h.008v.008H6.75V12zm0 3h.008v.008H6.75V15zm0 3h.008v.008H6.75V18z"></path>
                        </svg>
                        View Quotations
                    </a>
                </div>
            </x-admin.card>
            
            <!-- Support Info Widget -->
            <x-admin.card>
                <x-slot name="title">Need Help?</x-slot>
                
                <div class="space-y-3 text-sm">
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-400">24/7 Support Available</span>
                    </div>
                    
                    <div class="flex items-center space-x-2">
                        <svg class="w-4 h-4 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <span class="text-gray-600 dark:text-gray-400">Average response: 2 hours</span>
                    </div>
                    
                    <div class="pt-3 border-t border-gray-200 dark:border-gray-600">
                        <x-admin.button
                            href="{{ route('client.messages.create', ['type' => 'support']) }}"
                            color="primary"
                            size="sm"
                            class="w-full"
                        >
                            Contact Support
                        </x-admin.button>
                    </div>
                </div>
            </x-admin.card>
            
        </div>
    </div>
</x-layouts.admin>

<!-- Add this to your dashboard controller method -->
{{--
// app/Http/Controllers/Client/DashboardController.php

public function index()
{
    $user = auth()->user();
    
    // Get message summary for widget
    $messageSummary = $this->clientAccessService->getMessageActivitySummary($user);
    
    // Get recent message activity
    $recentActivity = $this->messageService->getRecentActivity($user, 7);
    
    // Your existing dashboard data...
    $projects = $this->clientAccessService->getClientProjects($user)->latest()->take(5)->get();
    $quotations = $user->quotations()->latest()->take(3)->get();
    
    return view('client.dashboard.index', compact(
        'messageSummary',
        'recentActivity',
        'projects',
        'quotations'
    ));
}
--}}

<!-- Real-time Dashboard Updates -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check for urgent messages on dashboard load
    checkUrgentMessages();
    
    // Refresh dashboard data periodically
    setInterval(refreshDashboardData, 120000); // Every 2 minutes
});

async function checkUrgentMessages() {
    try {
        const response = await fetch('{{ route("api.client.messages.check-urgent") }}');
        const data = await response.json();
        
        if (data.success && data.has_urgent) {
            showUrgentAlert(data.urgent_messages);
        }
    } catch (error) {
        console.error('Failed to check urgent messages:', error);
    }
}

function showUrgentAlert(urgentMessages) {
    // Only show if not already shown in current session
    if (sessionStorage.getItem('urgent_alert_shown')) {
        return;
    }
    
    const alertHtml = `
        <div id="urgent-alert" class="fixed top-4 left-1/2 transform -translate-x-1/2 bg-red-100 border border-red-400 text-red-700 px-6 py-4 rounded-lg shadow-lg z-50 max-w-md">
            <div class="flex items-start">
                <svg class="w-5 h-5 text-red-500 mr-3 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.728-.833-2.498 0L3.316 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                </svg>
                <div class="flex-1">
                    <h4 class="font-medium">Urgent Messages Require Attention</h4>
                    <p class="text-sm mt-1">You have ${urgentMessages.length} urgent message${urgentMessages.length > 1 ? 's' : ''} waiting for your response.</p>
                    <div class="mt-3 flex space-x-2">
                        <a href="{{ route('client.messages.index', ['priority' => 'urgent']) }}" class="text-sm bg-red-600 text-white px-3 py-1 rounded hover:bg-red-700">
                            View Messages
                        </a>
                        <button onclick="dismissUrgentAlert()" class="text-sm text-red-600 hover:text-red-800">
                            Dismiss
                        </button>
                    </div>
                </div>
                <button onclick="dismissUrgentAlert()" class="ml-2 text-red-400 hover:text-red-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', alertHtml);
    sessionStorage.setItem('urgent_alert_shown', 'true');
}

function dismissUrgentAlert() {
    const alert = document.getElementById('urgent-alert');
    if (alert) {
        alert.style.opacity = '0';
        alert.style.transform = 'translate(-50%, -100%)';
        setTimeout(() => alert.remove(), 300);
    }
}

async function refreshDashboardData() {
    try {
        // Refresh message widget data
        if (typeof refreshWidgetData === 'function') {
            refreshWidgetData();
        }
        
        // Check for new urgent messages
        checkUrgentMessages();
        
        // You can add more dashboard refresh logic here
        
    } catch (error) {
        console.error('Failed to refresh dashboard:', error);
    }
}

// Clear urgent alert flag when navigating to messages
window.addEventListener('beforeunload', function() {
    if (window.location.pathname.includes('/messages')) {
        sessionStorage.removeItem('urgent_alert_shown');
    }
});

// Show success message when returning from message creation
const urlParams = new URLSearchParams(window.location.search);
if (urlParams.get('message_sent') === 'true') {
    setTimeout(() => {
        const notification = document.createElement('div');
        notification.className = 'fixed top-4 right-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg shadow-lg z-50';
        notification.innerHTML = `
            <div class="flex items-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span>Message sent successfully!</span>
                <button onclick="this.parentElement.parentElement.remove()" class="ml-3 text-green-500 hover:text-green-700">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        setTimeout(() => {
            if (notification.parentNode) {
                notification.remove();
            }
        }, 5000);
        
        // Remove the parameter from URL
        const newUrl = window.location.pathname;
        window.history.replaceState({}, '', newUrl);
    }, 500);
}
</script>