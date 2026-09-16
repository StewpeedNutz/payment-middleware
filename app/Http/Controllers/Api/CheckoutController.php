<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function store(Request $request)
    {
        // 1. Validasi input payload
        $validated = $request->validate([
            'customer_name'  => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'amount'         => 'required|numeric|min:1',
        ]);

        // 2. Jana rujukan unik order (contoh: ORD-A1B2C3D4)
        $orderRef = 'ORD-' . strtoupper(Str::random(8));

        // 3. Simpan order ke dalam SQLite
        $order = Order::create([
            'order_ref'      => $orderRef,
            'customer_name'  => $validated['customer_name'],
            'customer_email' => $validated['customer_email'],
            'amount'         => $validated['amount'],
            'status'         => 'pending',
        ]);

        // 4. Balas response JSON dengan status 201 (Created)
        return response()->json([
            'message' => 'Order created successfully',
            'data'    => [
                'order_ref'   => $order->order_ref,
                'amount'      => $order->amount,
                'status'      => $order->status,
                'payment_url' => url("/api/mock-pay/{$order->order_ref}"),
            ],
        ], 201);
    }
}
