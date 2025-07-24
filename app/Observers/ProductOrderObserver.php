<?php

namespace App\Observers;

use App\Models\ProductOrder;
use App\Facades\Notifications;
use Illuminate\Support\Facades\Log;

class ProductOrderObserver
{
    /**
     * Handle the ProductOrder "created" event.
     */
    public function created(ProductOrder $order)
    {
        try {
            Log::info('ProductOrder created event triggered', ['order_id' => $order->id]);

            // Send confirmation to client
            Notifications::send('product_order.created', $order, $order->getNotifiableEntity());

            // Notify admin of new order
            Notifications::send('product_order.admin_notification', $order);

        } catch (\Exception $e) {
            Log::error('Error in ProductOrder created observer', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Handle the ProductOrder "updated" event.
     */
    public function updated(ProductOrder $order)
    {
        try {
            // Check if status was changed
            if ($order->isDirty('status')) {
                $oldStatus = $order->getOriginal('status');
                $newStatus = $order->status;

                Log::info('ProductOrder status changed', [
                    'order_id' => $order->id,
                    'old_status' => $oldStatus,
                    'new_status' => $newStatus
                ]);

                // Send status update notification to client
                Notifications::send('product_order.status_updated', $order, $order->getNotifiableEntity());

                // Special notifications for specific status changes
                switch ($newStatus) {
                    case 'confirmed':
                        Notifications::send('product_order.confirmed', $order, $order->getNotifiableEntity());
                        break;
                    case 'processing':
                        Notifications::send('product_order.processing', $order, $order->getNotifiableEntity());
                        break;
                    case 'ready':
                        Notifications::send('product_order.ready', $order, $order->getNotifiableEntity());
                        break;
                    case 'delivered':
                        Notifications::send('product_order.delivered', $order, $order->getNotifiableEntity());
                        break;
                    case 'completed':
                        Notifications::send('product_order.completed', $order, $order->getNotifiableEntity());
                        break;
                }
            }

        } catch (\Exception $e) {
            Log::error('Error in ProductOrder updated observer', [
                'order_id' => $order->id,
                'error' => $e->getMessage()
            ]);
        }
    }
}