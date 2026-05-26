<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\RiskScore;

class MonitoringController extends Controller
{
    public function auditTrail()
    {
        $logs = AuditLog::latest()->paginate(20);

        return view('monitoring.audit-trail', compact('logs'));
    }

    public function riskMonitoring()
    {
        $risks = RiskScore::with('transaction.fieldUnit', 'transaction.vendor')
            ->latest()
            ->paginate(20);

        return view('monitoring.risk-monitoring', compact('risks'));
    }
}
