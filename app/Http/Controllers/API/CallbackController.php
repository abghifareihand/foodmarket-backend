<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\Midtrans\CallbackService;
use Illuminate\Http\Request;

class CallbackController extends Controller
{
    public function callback()
    {
        $callback = new CallbackService;
        $transaction = $callback->getOrder();

        if ($callback->isSuccess()) {
            $transaction->update([
                'status' => 'SUCCESS',
            ]);
        }

        if ($callback->isExpire()) {
            $transaction->update([
                'status' => 'EXPIRED',
            ]);
        }

        if ($callback->isCancelled()) {
            $transaction->update([
                'status' => 'CANCELLED',
            ]);
        }

        return response()
            ->json([
                'success' => true,
                'message' => 'Notification callback success',
            ]);
    }
}
