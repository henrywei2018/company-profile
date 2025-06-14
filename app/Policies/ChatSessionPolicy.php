<?php

namespace App\Policies;

use App\Models\ChatSession;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class ChatSessionPolicy
{
    use HandlesAuthorization;

    /**
     * Check if user can participate in the chat session
     */
    public function participate(User $user, ChatSession $chatSession): bool
    {
        // User can participate if they own the session
        return $chatSession->user_id === $user->id;
    }

    /**
     * Check if user can manage the chat session (admin/operator)
     */
    public function manage(User $user, ChatSession $chatSession): bool
    {
        // Admins can manage all sessions
        if ($user->hasRole('admin')) {
            return true;
        }

        // Operators can manage sessions assigned to them
        if ($user->hasRole('operator')) {
            return $chatSession->assigned_operator_id === $user->id;
        }

        return false;
    }

    /**
     * Check if user can view the chat session
     */
    public function view(User $user, ChatSession $chatSession): bool
    {
        return $this->participate($user, $chatSession) || $this->manage($user, $chatSession);
    }

    /**
     * Check if user can assign operators to sessions
     */
    public function assign(User $user): bool
    {
        return $user->hasRole(['admin', 'operator']);
    }

    /**
     * Check if user can close sessions
     */
    public function close(User $user, ChatSession $chatSession): bool
    {
        return $this->manage($user, $chatSession);
    }

    /**
     * Check if user can transfer sessions
     */
    public function transfer(User $user, ChatSession $chatSession): bool
    {
        return $user->hasRole('admin') || 
               ($user->hasRole('operator') && $chatSession->assigned_operator_id === $user->id);
    }

    /**
     * Check if user can view all sessions (admin dashboard)
     */
    public function viewAny(User $user): bool
    {
        return $user->hasRole(['admin', 'operator','super-admin']);
    }
}