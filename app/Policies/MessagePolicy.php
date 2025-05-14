<?php
// File: app/Policies/MessagePolicy.php

namespace App\Policies;

use App\Models\Message;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class MessagePolicy
{
    use HandlesAuthorization;

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->isClient() || $user->isAdmin();
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Message $message): bool
    {
        // Admin can view any message
        if ($user->isAdmin()) {
            return true;
        }
        
        // Client can only view their own messages
        if ($user->isClient()) {
            return $message->client_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->isClient() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Message $message): bool
    {
        // Admin can update any message
        if ($user->isAdmin()) {
            return true;
        }
        
        // Client can only update their own messages
        if ($user->isClient()) {
            return $message->client_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Message $message): bool
    {
        // Admin can delete any message
        if ($user->isAdmin()) {
            return true;
        }
        
        // Clients cannot delete messages
        return false;
    }

    /**
     * Determine whether the user can reply to the model.
     */
    public function reply(User $user, Message $message): bool
    {
        // Admin can reply to any message
        if ($user->isAdmin()) {
            return true;
        }
        
        // Client can only reply to messages involving them
        if ($user->isClient()) {
            return $message->client_id === $user->id;
        }
        
        return false;
    }
}