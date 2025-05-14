<?php
// File: app/Policies/QuotationPolicy.php

namespace App\Policies;

use App\Models\Quotation;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuotationPolicy
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
    public function view(User $user, Quotation $quotation): bool
    {
        // Admin can view any quotation
        if ($user->isAdmin()) {
            return true;
        }
        
        // Client can only view their quotations
        if ($user->isClient()) {
            return $quotation->client_id === $user->id;
        }
        
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        // Anyone can create quotations (admins and clients)
        return $user->isClient() || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Quotation $quotation): bool
    {
        // Admin can update any quotation
        if ($user->isAdmin()) {
            return true;
        }
        
        // Client can only update their quotations if they're in pending or reviewed status
        if ($user->isClient() && $quotation->client_id === $user->id) {
            return in_array($quotation->status, ['pending', 'reviewed']);
        }
        
        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Quotation $quotation): bool
    {
        // Only admin can delete quotations
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can approve or decline the model.
     */
    public function approveOrDecline(User $user, Quotation $quotation): bool
    {
        // Client can only approve/decline their approved quotations
        if ($user->isClient() && $quotation->client_id === $user->id) {
            return $quotation->status === 'approved';
        }
        
        return false;
    }
}