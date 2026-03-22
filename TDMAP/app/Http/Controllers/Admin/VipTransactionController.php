<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class VipTransactionController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->get('status');

        $query = Transaction::with('user')->latest();

        if (in_array($status, ['pending', 'completed', 'cancelled'])) {
            $query->where('status', $status);
        }

        $transactions = $query->paginate(20);

        return view('admin.vip-transactions.index', compact('transactions', 'status'));
    }

    public function confirm(Transaction $transaction)
    {
        if ($transaction->status === 'completed') {
            return back()->with('error', 'Giao dịch này đã được xác nhận trước đó.');
        }

        if ($transaction->status === 'cancelled') {
            return back()->with('error', 'Giao dịch này đã bị hủy, không thể xác nhận.');
        }

        $user = $transaction->user;

        if (!$user) {
            return back()->with('error', 'Không tìm thấy người dùng của giao dịch.');
        }

        $transaction->update([
            'status' => 'completed',
        ]);

        $newVipLevel = match ($transaction->vip_level) {
            'vip1' => 1,
            'vip2' => 2,
            'vip3' => 3,
            default => 0,
        };

        $baseDate = (
            $user->vip_expired_at &&
            $user->vip_expired_at->isFuture()
        ) ? $user->vip_expired_at->copy() : now();

        $user->update([
            'vip_level' => $newVipLevel,
            'vip_expired_at' => $baseDate->addDays(30),
        ]);

        return back()->with('success', 'Đã xác nhận thanh toán và bật VIP cho người dùng.');
    }

    public function cancel(Transaction $transaction)
    {
        if ($transaction->status === 'completed') {
            return back()->with('error', 'Giao dịch đã hoàn thành, không thể hủy.');
        }

        $transaction->update([
            'status' => 'cancelled',
        ]);

        return back()->with('success', 'Đã hủy giao dịch.');
    }
}