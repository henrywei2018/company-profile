<?php

namespace App\Notifications;

use App\Notifications\BaseNotification;
use App\Models\ProductOrder;

class ProductOrderStatusUpdatedNotification extends BaseNotification
{
    protected function configure(): void
    {
        $order = $this->data;

        if ($order instanceof ProductOrder) {
            $statusMessages = [
                'pending' => 'Your order is pending review',
                'confirmed' => 'Your order has been confirmed',
                'processing' => 'Your order is being processed',
                'ready' => 'Your order is ready for pickup/delivery',
                'delivered' => 'Your order has been delivered',
                'completed' => 'Your order has been completed'
            ];

            $statusMessage = $statusMessages[$order->status] ?? 'Your order status has been updated';
            
            $this->subject = "Order Update - {$order->order_number}";
            $this->greeting = "Hello {$order->client_name}!";
            
            $this->addLine($statusMessage);
            $this->addLine("Order Number: {$order->order_number}");
            
            // Status-specific messages
            switch ($order->status) {
                case 'confirmed':
                    $this->addLine("Great news! Your order has been confirmed and will be processed shortly.");
                    break;
                case 'processing':
                    $this->addLine("Your order is now being prepared. We'll notify you once it's ready.");
                    break;
                case 'ready':
                    $this->addLine("Your order is ready! Please coordinate with our team for pickup/delivery.");
                    if ($order->admin_notes) {
                        $this->addLine("**Additional Notes:** {$order->admin_notes}");
                    }
                    break;
                case 'delivered':
                    $this->addLine("Your order has been successfully delivered. Thank you for your business!");
                    break;
                case 'completed':
                    $this->addLine("Your order is now complete. We hope you're satisfied with our products and service!");
                    break;
            }
            
            $this->setAction('View Order Details', route('client.orders.show', $order));
            $this->salutation = 'Best regards,<br>' . config('app.name') . ' Team';
        }
    }
}