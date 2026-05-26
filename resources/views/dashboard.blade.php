<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Dashboard Monitoring RABPay
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-6">
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-sm text-gray-500">Saldo Virtual</div>
                    <div class="text-xl font-bold">Rp{{ number_format($totalBalance, 0, ',', '.') }}</div>
                </div>

                <div class="bg-white p-4 rounded shadow">
                    <div class="text-sm text-gray-500">Success</div>
                    <div class="text-xl font-bold text-green-600">{{ $successCount }}</div>
                </div>

                <div class="bg-white p-4 rounded shadow">
                    <div class="text-sm text-gray-500">Pending</div>
                    <div class="text-xl font-bold text-yellow-600">{{ $pendingCount }}</div>
                </div>

                <div class="bg-white p-4 rounded shadow">
                    <div class="text-sm text-gray-500">Rejected</div>
                    <div class="text-xl font-bold text-red-600">{{ $rejectedCount }}</div>
                </div>

                <div class="bg-white p-4 rounded shadow">
                    <div class="text-sm text-gray-500">High Risk</div>
                    <div class="text-xl font-bold text-red-700">{{ $highRiskCount }}</div>
                </div>
            </div>

            <div class="bg-white p-6 rounded shadow">
                <h3 class="text-lg font-bold mb-4">Transaksi Terbaru</h3>

                <div class="overflow-x-auto">
                    <table class="w-full border text-sm">
                        <thead class="bg-gray-100">
                            <tr>
                                <th class="border px-3 py-2 text-left">Kode</th>
                                <th class="border px-3 py-2 text-left">Bidang</th>
                                <th class="border px-3 py-2 text-left">Vendor</th>
                                <th class="border px-3 py-2 text-left">Item</th>
                                <th class="border px-3 py-2 text-left">Nominal</th>
                                <th class="border px-3 py-2 text-left">Status</th>
                                <th class="border px-3 py-2 text-left">Risk</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($latestTransactions as $transaction)
                                <tr>
                                    <td class="border px-3 py-2">{{ $transaction->transaction_code }}</td>
                                    <td class="border px-3 py-2">{{ $transaction->fieldUnit->name ?? '-' }}</td>
                                    <td class="border px-3 py-2">{{ $transaction->vendor->name ?? '-' }}</td>
                                    <td class="border px-3 py-2">{{ $transaction->item_name }}</td>
                                    <td class="border px-3 py-2">Rp{{ number_format($transaction->amount, 0, ',', '.') }}</td>
                                    <td class="border px-3 py-2">{{ $transaction->status }}</td>
                                    <td class="border px-3 py-2">{{ optional($transaction->riskScore)->risk_level ?? 'low' }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="border px-3 py-3 text-center text-gray-500">
                                        Belum ada transaksi.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
