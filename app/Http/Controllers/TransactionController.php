<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function cart(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }

        return view('pages.cart', compact('store'));
    }

    public function customerInformation(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }

        return view('pages.customer-information', compact('store'));
    }

    public function checkout(Request $request)
    {
        $store = User::where('username', $request->username)->first();

        if (!$store) {
            abort(404);
        }

        $carts = json_decode($request->cart, true);

        $totalPrice = 0;

        foreach ($carts as $cart){
            $product = Product::where('id', $cart['id'])->first();
            $totalPrice += $product->price * $cart['qty'];
        }

        $transaction = $store->transactions()->create([
            'code'  => 'TRX-' . mt_rand(10000, 99999),
            'name'  =>  $request->name,
            'table_number'  =>  $request->table_number,
            'payment_method' =>  $request->payment_method,
            'total_price'   =>  $totalPrice,
            'status'    =>  'pending'
        ]);

        foreach($carts as $cart){
            $product = Product::where('id', $cart['id'])->first();

            $transaction->transactionDetail()->create([
                'product_id' => $product->id,
                'quantity' => $cart['qty'],
                'note' => $cart['notes'],
            ]);
        }

        if($request->payment_method == 'cash') {
            return redirect()->route('success', ['username' => $store->username, 'order_id' => $transaction->code]);
        }else{
            // Set your merchant server key
            \Midtrans\Config::$serverKey = config('midtrans.serverKey');
            // Set to Development/Sandbox Environment (default). Set to true for Production Environtment (accept real transaction)
            \Midtrans\Config::$isProduction = config('midtrans.isProduction');
            // Set sanitazion on (default)
            \Midtrans\Config::$isSanitized = config('midtrans.isSanitized');
            // Set 3DS Transaction for credit card to true
            \Midtrans\Config::$is3ds = config('midtrans.is3ds');

            $params = [
                'transaction_details' => [
                    'order_id'  => $transaction->code,
                    'gross_amount'  => $totalPrice
                ],
                'customer_details'  =>  [
                    'name'  =>  $request->name,
                    'phone' =>  $request->phone_number
                ]
            ];

            $paymentUrl = \Midtrans\Snap::createTransaction($params)->redirect_url;

            return redirect($paymentUrl);
        }
    }

    public function success(Request $request)
    {
        $transaction = Transaction::where('code', $request->order_id)->first();
        $store = User::where('id', $transaction->user_id)->first();

        if (!$store) {
            abort(404);
        }

        return view('pages.success', compact('store', 'transaction'));
    }
}
