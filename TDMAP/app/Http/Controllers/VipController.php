<?php

namespace App\Http\Controllers;

use App\Mail\AdminNewVipOrderMail;
use App\Mail\VipPaymentWaitingMail;
use App\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;

class VipController extends Controller
{
    public function paymentPage()
    {
        $user = Auth::user();

        $packages = [
            [
                'code' => 'vip1',
                'name' => 'VIP 1',
                'price' => 50000,
                'duration' => '30 ngày',
                'limit_text' => 'Mỗi mục tải tối đa 3 file',
            ],
            [
                'code' => 'vip2',
                'name' => 'VIP 2',
                'price' => 100000,
                'duration' => '30 ngày',
                'limit_text' => 'Mỗi mục tải tối đa 9 file',
            ],
            [
                'code' => 'vip3',
                'name' => 'VIP 3',
                'price' => 200000,
                'duration' => '30 ngày',
                'limit_text' => 'Không giới hạn upload',
            ],
        ];

        return view('vip.payment', compact('user', 'packages'));
    }

    public function createOrder(Request $request): JsonResponse
    {
        $request->validate([
            'vip' => ['required', 'in:vip1,vip2,vip3'],
        ]);

        $user = Auth::user();
        $vip = $request->input('vip');

        $amount = match ($vip) {
            'vip1' => 50000,
            'vip2' => 100000,
            'vip3' => 200000,
            default => 0,
        };

        $transactionCode = strtoupper($vip) . '_' . $user->id . '_' . time();

        $transaction = Transaction::create([
            'user_id' => $user->id,
            'vip_level' => $vip,
            'amount' => $amount,
            'transaction_code' => $transactionCode,
            'status' => 'pending',
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Tạo đơn thanh toán thành công. Vui lòng chuyển khoản và chờ admin xác nhận.',
            'transaction_id' => $transaction->id,
            'vip' => $vip,
            'amount' => $amount,
            'content' => $transactionCode,
            'bank_name' => 'MBBank',
            'account_no' => '123456789',
            'account_name' => 'TAI DO MAP',
            'status' => $transaction->status,
            'qr_url' => 'https://img.vietqr.io/image/MB-123456789-compact2.png?amount='
                . $amount
                . '&addInfo=' . urlencode($transactionCode)
                . '&accountName=' . urlencode('TAI DO MAP'),
        ]);
    }

    public function checkStatus(int $transactionId): JsonResponse
    {
        $transaction = Transaction::where('id', $transactionId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$transaction) {
            return response()->json([
                'success' => false,
                'message' => 'Không tìm thấy giao dịch.',
            ], 404);
        }

        return response()->json([
            'success' => true,
            'transaction_id' => $transaction->id,
            'status' => $transaction->status,
            'vip' => $transaction->vip_level,
            'amount' => $transaction->amount,
            'transaction_code' => $transaction->transaction_code,
        ]);
    }

public function userConfirmedPaid(int $transactionId): JsonResponse
{
    $transaction = Transaction::where('id', $transactionId)
        ->where('user_id', Auth::id())
        ->with('user')
        ->first();

    if (!$transaction) {
        return response()->json([
            'success' => false,
            'message' => 'Không tìm thấy giao dịch.',
        ], 404);
    }

    if ($transaction->status === 'completed') {
        return response()->json([
            'success' => false,
            'message' => 'Giao dịch này đã hoàn thành trước đó.',
        ], 400);
    }

    if (!$transaction->user_confirmed_paid) {
        $transaction->update([
            'user_confirmed_paid' => true,
            'user_confirmed_paid_at' => now(),
        ]);

        try {
            if ($transaction->user && $transaction->user->email) {
                Mail::to($transaction->user->email)->send(new VipPaymentWaitingMail($transaction));
            }
        } catch (\Exception $e) {
            \Log::error('Lỗi gửi mail user', [
                'transaction_id' => $transaction->id,
                'error' => $e->getMessage(),
            ]);
        }

        $adminEmail = env('ADMIN_EMAIL');

        if ($adminEmail) {
            try {
                Mail::to($adminEmail)->send(new AdminNewVipOrderMail($transaction));
            } catch (\Exception $e) {
                \Log::error('Lỗi gửi mail admin', [
                    'transaction_id' => $transaction->id,
                    'admin_email' => $adminEmail,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }

    return response()->json([
        'success' => true,
        'message' => 'Đã ghi nhận xác nhận thanh toán. Vui lòng chờ hệ thống kiểm tra.',
    ]);
}
}