<?php

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\VirtualAccount;
use App\Models\RiskScore;

class DashboardController extends Controller
{
    public function index()
    {
        $totalBalance = VirtualAccount::sum('current_balance');

        $successCount = Transaction::where('status', 'success')->count();
        $pendingCount = Transaction::where('status', 'pending_approval')->count();
        $rejectedCount = Transaction::where('status', 'rejected')->count();
        $highRiskCount = RiskScore::where('risk_level', 'high')->count();

        $latestTransactions = Transaction::with(['fieldUnit', 'vendor', 'riskScore'])
            ->latest()
            ->take(8)
            ->get();

        return view('dashboard', compact(
            'totalBalance',
            'successCount',
            'pendingCount',
            'rejectedCount',
            'highRiskCount',
            'latestTransactions'
        ));
    }
}
