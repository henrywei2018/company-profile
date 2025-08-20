<?php

namespace App\Policies;

use App\Models\ProductOrder;
use App\Models\User;

class ProductOrderPolicy
{
    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, ProductOrder $order)
    {
        // Clients can only view their own orders
        if ($user->hasRole('client')) {
            return $order->client_id === $user->id;
        }

        // Admins can view all orders
        return $user->hasAnyRole(['admin', 'manager', 'super-admin']);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, ProductOrder $order)
    {
        // Only admins can update orders
        return $user->hasAnyRole(['admin', 'manager', 'super-admin']);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, ProductOrder $order)
    {
        // Only super-admin can delete orders
        return $user->hasRole('super-admin');
    }
}