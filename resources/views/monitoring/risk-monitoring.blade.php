<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Risk Monitoring
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 bg-white p-6 rounded shadow">
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-left">Kode Transaksi</th>
                        <th class="border px-3 py-2 text-left">Bidang</th>
                        <th class="border px-3 py-2 text-left">Vendor</th>
                        <th class="border px-3 py-2 text-left">Score</th>
                        <th class="border px-3 py-2 text-left">Level</th>
                        <th class="border px-3 py-2 text-left">Indikator</th>
                        <th class="border px-3 py-2 text-left">Rekomendasi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($risks as $risk)
                        <tr>
                            <td class="border px-3 py-2">{{ $risk->transaction->transaction_code ?? '-' }}</td>
                            <td class="border px-3 py-2">{{ $risk->transaction->fieldUnit->name ?? '-' }}</td>
                            <td class="border px-3 py-2">{{ $risk->transaction->vendor->name ?? '-' }}</td>
                            <td class="border px-3 py-2">{{ $risk->score }}</td>
                            <td class="border px-3 py-2">{{ $risk->risk_level }}</td>
                            <td class="border px-3 py-2">
                                {{ is_array($risk->risk_indicators) ? implode(', ', $risk->risk_indicators) : '-' }}
                            </td>
                            <td class="border px-3 py-2">{{ $risk->recommendation }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="border px-3 py-3 text-center text-gray-500">
                                Belum ada data risiko.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $risks->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
