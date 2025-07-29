<?php

namespace App\Listeners;

use App\Events\OrderPaymentsEvent;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use App\Models\Order;
use App\Models\OrderPayment;

class OrderPaymentsListner
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderPaymentsEvent $event): void
    {
        $order = Order::find($event->order['order_id']);
        $paymentStatus = $this->getPaymentStatus($order);
        $order->payment_status = $paymentStatus['status'];
        $order->paid_amount = $paymentStatus['paidAmount'];
        
        $order->save();
    }
    /**
     * Return Payment Status
     * */
    private function getPaymentStatus(Order $order) 
    {
        $status ='';
        $payableAmount = $order->total_amount;
        $paidAmount = OrderPayment::where('order_id', $order->id)->sum('amount');
        
        if ($payableAmount == $paidAmount) {
            $status = 'Paid';
        }
        
        elseif ($paidAmount == 0) { 
            $status = 'Unpaid';
        }
        
        elseif ($payableAmount > $paidAmount && $paidAmount > 0) {
            $status = 'Partial';
        }
        
        elseif ($payableAmount < $paidAmount) {
            $status = 'Overpaid';
        }
        else{
            $status = '';
        }
        return ['status' => $status, 'paidAmount' => $paidAmount ];
    }
}
