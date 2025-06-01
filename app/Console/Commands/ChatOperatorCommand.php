<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\ChatOperator;
use App\Services\ChatService;
use Illuminate\Console\Command;

class ChatOperatorCommand extends Command
{
    protected $signature = 'chat:operator
                            {action : Action to perform (list, online, offline, status)}
                            {user? : User ID or email for specific actions}
                            {--all : Apply to all operators}';

    protected $description = 'Manage chat operators';

    protected ChatService $chatService;

    public function __construct(ChatService $chatService)
    {
        parent::__construct();
        $this->chatService = $chatService;
    }

    public function handle()
    {
        $action = $this->argument('action');
        $userIdentifier = $this->argument('user');
        $all = $this->option('all');

        switch ($action) {
            case 'list':
                return $this->listOperators();
            case 'online':
                return $this->setOperatorStatus($userIdentifier, true, $all);
            case 'offline':
                return $this->setOperatorStatus($userIdentifier, false, $all);
            case 'status':
                return $this->showOperatorStatus($userIdentifier);
            default:
                $this->error("Unknown action: {$action}");
                return Command::FAILURE;
        }
    }

    protected function listOperators()
    {
        $operators = ChatOperator::with('user')->get();

        if ($operators->isEmpty()) {
            $this->info('No chat operators found.');
            return Command::SUCCESS;
        }

        $this->info('👥 Chat Operators:');
        $this->table(
            ['ID', 'Name', 'Email', 'Online', 'Available', 'Active Chats', 'Max Chats', 'Last Seen'],
            $operators->map(function ($operator) {
                return [
                    $operator->user->id,
                    $operator->user->name,
                    $operator->user->email,
                    $operator->is_online ? '✅' : '❌',
                    $operator->is_available ? '✅' : '❌',
                    $operator->current_chats_count,
                    $operator->max_concurrent_chats,
                    $operator->last_seen_at ? $operator->last_seen_at->diffForHumans() : 'Never',
                ];
            })->toArray()
        );

        return Command::SUCCESS;
    }

    protected function setOperatorStatus($userIdentifier, $online, $all)
    {
        if ($all) {
            $users = User::whereHas('roles', function ($q) {
                $q->whereIn('name', ['super-admin', 'admin', 'manager']);
            })->get();
        } elseif ($userIdentifier) {
            $user = is_numeric($userIdentifier) 
                ? User::find($userIdentifier)
                : User::where('email', $userIdentifier)->first();
            
            if (!$user) {
                $this->error("User not found: {$userIdentifier}");
                return Command::FAILURE;
            }
            
            $users = collect([$user]);
        } else {
            $this->error('Please specify a user ID/email or use --all flag');
            return Command::FAILURE;
        }

        $status = $online ? 'online' : 'offline';
        $count = 0;

        foreach ($users as $user) {
            if ($online) {
                $this->chatService->setOperatorOnline($user);
            } else {
                $this->chatService->setOperatorOffline($user);
            }
            $count++;
        }

        $this->info("✅ Set {$count} operator(s) {$status}");
        return Command::SUCCESS;
    }

    protected function showOperatorStatus($userIdentifier)
    {
        if (!$userIdentifier) {
            $this->error('Please specify a user ID or email');
            return Command::FAILURE;
        }

        $user = is_numeric($userIdentifier) 
            ? User::find($userIdentifier)
            : User::where('email', $userIdentifier)->first();
        
        if (!$user) {
            $this->error("User not found: {$userIdentifier}");
            return Command::FAILURE;
        }

        $operator = $this->chatService->getOperator($user);
        $status = $user->getChatOperatorStatus();

        $this->info("👤 Operator Status for {$user->name}:");
        $this->table(
            ['Property', 'Value'],
            [
                ['Online', $status['is_online'] ? '✅ Yes' : '❌ No'],
                ['Available', $status['is_available'] ? '✅ Yes' : '❌ No'],
                ['Active Chats', $status['current_chats_count']],
                ['Max Chats', $status['max_concurrent_chats']],
                ['Last Seen', $status['last_seen_at'] ? $status['last_seen_at']->format('Y-m-d H:i:s') : 'Never'],
                ['Can Take More', $user->canTakeMoreChatSessions() ? '✅ Yes' : '❌ No'],
                ['Unread Messages', $user->getUnreadChatMessagesCount()],
            ]
        );

        // Show active sessions
        $activeSessions = $user->activeOperatingChatSessions()->get();
        
        if ($activeSessions->isNotEmpty()) {
            $this->newLine();
            $this->info('💬 Active Sessions:');
            $this->table(
                ['Session ID', 'Visitor', 'Status', 'Started', 'Last Activity'],
                $activeSessions->map(function ($session) {
                    return [
                        $session->session_id,
                        $session->getVisitorName(),
                        $session->status,
                        $session->started_at->diffForHumans(),
                        $session->last_activity_at ? $session->last_activity_at->diffForHumans() : 'Never',
                    ];
                })->toArray()
            );
        }

        return Command::SUCCESS;
    }
}