<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Invoice;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class WebhookController extends Controller
{
    public function handle(Request $request)
    {
        // 1. Validasi struktur payload yang dihantar oleh mock payment gateway
        $validated = $request->validate([
            'payment_ref'      => 'required|string',
            'gateway_status'   => 'required|in:SUCCESS,FAILED',
            'payment_channel'  => 'required|string',
            'transaction_time' => 'required|date',
        ]);

        // 2. Cari order yang sepadan guna order_ref
        $order = Order::where('order_ref', $validated['payment_ref'])->first();

        if (!$order) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Order not found for reference: ' . $validated['payment_ref']
            ], 404);
        }

        // 3. Konsep Idempotency: Jika order sudah dibayar sebelum ini, abaikan pemprosesan berulang
        if ($order->status === 'paid') {
            return response()->json([
                'status'  => 'ignored',
                'message' => 'Order has already been processed.'
            ], 200);
        }

        // 4. Data Mapping & Kemas kini Order
        if ($validated['gateway_status'] === 'SUCCESS') {
            $order->update([
                'status' => 'paid',
            ]);

            // 5. Jana rekod Invoice baharu secara automatik melalui perhubungan Model
            $invoiceNumber = 'INV-' . date('Y') . '-' . strtoupper(Str::random(6));

            $invoice = $order->invoice()->create([
                'invoice_no'     => $invoiceNumber,
                'payment_method' => $validated['payment_channel'],
                'paid_at'        => Carbon::parse($validated['transaction_time']),
            ]);

            return response()->json([
                'status'  => 'success',
                'message' => 'Payment processed and invoice generated successfully.',
                'data'    => [
                    'order_ref'      => $order->order_ref,
                    'order_status'   => $order->status,
                    'invoice_number' => $invoice->invoice_no,
                    'payment_method' => $invoice->payment_method,
                    'paid_at'        => $invoice->paid_at->toDateTimeString(),
                ]
            ], 200);
        }

        // Jika gateway_status adalah FAILED
        $order->update([
            'status' => 'failed',
        ]);

        return response()->json([
            'status'  => 'failed',
            'message' => 'Payment failed. Order status updated.',
            'data'    => [
                'order_ref'    => $order->order_ref,
                'order_status' => $order->status,
            ]
        ], 200);
    }
}