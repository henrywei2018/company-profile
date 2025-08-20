<?php

namespace App\Observers;

use App\Models\ProductOrder;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class ProductOrderObserver
{
    /**
     * Handle the ProductOrder "created" event.
     * This runs AFTER the database transaction is committed.
     */
    public function created(ProductOrder $order): void
    {
        try {
            Log::info('ProductOrder created - sending notifications', [
                'order_id' => $order->id,
                'order_number' => $order->order_number,
                'client_id' => $order->client_id
            ]);

            // Send confirmation to client
            if ($order->client) {
                Notifications::send('product_order.created', $order, $order->client);
            }

            // Notify admin of new order
            Notifications::send('product_order.admin_notification', $order);

        } catch (\Exception $e) {
            Log::error('Error in ProductOrder created observer', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Don't throw exception - let the request continue
            // Notification failure shouldn't break the order creation
        }
    }

    /**
     * Handle the ProductOrder "updated" event.
     * This runs AFTER the database transaction is committed.
     */
    public function updated(ProductOrder $order): void
    {
        try {
            // Check if status was changed
            if ($order->wasChanged('status')) {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;

                Log::info('ProductOrder status changed - sending notifications', [
                    'order_id' => $order->id,
                    'order_number' => $order->order_number,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]);

                // Send status update notification to client
                if ($order->client) {
                    Notifications::send('product_order.status_updated', $order, $order->client);
                }

                // Send specific notifications for certain status changes
                $this->handleSpecificStatusNotifications($order, $newStatus);
            }

        } catch (\Exception $e) {
            Log::error('Error in ProductOrder updated observer', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            
            // Don't throw exception - let the request continue
        }
    }

    /**
     * Handle specific status change notifications
     */
    private function handleSpecificStatusNotifications(ProductOrder $order, string $newStatus): void
    {
        if (!$order->client) {
            return;
        }

        switch ($newStatus) {
            case 'confirmed':
                Notifications::send('product_order.confirmed', $order, $order->client);
                break;
            case 'processing':
                Notifications::send('product_order.processing', $order, $order->client);
                break;
            case 'ready':
                Notifications::send('product_order.ready', $order, $order->client);
                break;
            case 'delivered':
                Notifications::send('product_order.delivered', $order, $order->client);
                break;
            case 'completed':
                Notifications::send('product_order.completed', $order, $order->client);
                break;
        }
    }

    /**
     * Handle the ProductOrder "deleted" event.
     */
    public function deleted(ProductOrder $order): void
    {
        Log::info('ProductOrder deleted', [
            'order_id' => $order->id,
            'order_number' => $order->order_number,
            'client_id' => $order->client_id
        ]);

    }
}
