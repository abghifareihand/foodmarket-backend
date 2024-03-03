<?php

namespace App\Http\Controllers\API;

use App\Helpers\ResponseFormatter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Midtrans\Config;
use Midtrans\Snap;

class TransactionController extends Controller
{
    public function all(Request $request)
    {
        $id = $request->input('id');
        $limit = $request->input('limit', 10);
        $food_id = $request->input('food_id');
        $status = $request->input('status');

        if ($id) {
            // relasi
            $transaction = Transaction::with(['food', 'user'])->find($id);

            if ($transaction) {
                return ResponseFormatter::success(
                    $transaction,
                    'Data transaksi berhasil di ambil'
                );
            } else {
                return ResponseFormatter::error(
                    null,
                    'Data transaksi tidak ada',
                    404
                );
            }
        }

        // relasi
        $transaction = Transaction::with(['food', 'user'])->where('user_id', Auth::user()->id);

        if ($food_id) {
            $transaction->where('food_id', $food_id);
        }

        if ($status) {
            $transaction->where('status', $status);
        }

        $result = $transaction->paginate($limit);

        if ($result->isEmpty()) {
            return ResponseFormatter::error(
                [],
                'Data transaksi tidak ditemukan',
                404
            );
        }

        return ResponseFormatter::success(
            $result,
            'Data list transaksi berhasil di ambil'
        );
    }

    public function update(Request $request, $id)
    {
        $transaction = Transaction::findOrFail($id);
        $transaction->update($request->all());

        return ResponseFormatter::success($transaction, 'Transaksi berhasil diupdate');
    }

    public function checkout(Request $request)
    {
        $request->validate([
            'food_id' => 'required|exists:food,id',
            'quantity' => 'required',
            'total' => 'required',
            'status' => 'required',
        ]);

        $transaction = Transaction::create([
            'food_id' => $request->food_id,
            'user_id' => $request->user()->id,
            'transaction_number' => 'TRX' . rand(100000, 999999),
            'quantity' => $request->quantity,
            'total' => $request->total,
            'status' => $request->status,
            'payment_url' => '',
        ]);

        // konfigurasi midtrans
        Config::$serverKey = config('services.midtrans.serverKey');
        Config::$isProduction = config('services.midtrans.isProduction');
        Config::$isSanitized = config('services.midtrans.isSanitized');
        Config::$is3ds = config('services.midtrans.is3ds');



        // panggil transaksi yg di buat
        $transaction = Transaction::with(['food', 'user'])->find($transaction->id);

        // membuat transaksi midtrans
        $midtrans = [
            'transaction_details' => [
                'order_id' => $transaction->transaction_number,
                'gross_amount' => (int) $transaction->total,
            ],
            'customer_details' => [
                'first_name' => $transaction->user->name,
                'email' => $transaction->user->email,
                'phone' => $transaction->user->phoneNumber,
                'address' => $transaction->user->address,
            ],
            // "enabled_payments" => [
            //     'bri_va', 'bca_va', 'bni_va',
            // ],
        ];

        // memanggil midtrans
        try {
            // ambil halaman payment midtrans
            $paymentUrl = Snap::createTransaction($midtrans)->redirect_url;
            $transaction->payment_url = $paymentUrl;
            $transaction->save();

            // mengembalikan data di api jika success
            return ResponseFormatter::success($transaction, 'Transaksi Berhasil');
        } catch (Exception $error) {
            // mengembalikan data di api jika error
            return ResponseFormatter::error($error->getMessage(), 'Transaksi Gagal');
        }

        // mengembalikan data ke api (callback)
    }
}
