<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Simulasi Transaksi RABPay
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white p-6 rounded-lg shadow mb-6">
                <h3 class="text-lg font-bold mb-4">Kartu Terakhir yang Ditap</h3>

                <div id="cardStatus" class="p-4 rounded bg-gray-100 text-gray-700">
                    Menunggu kartu RFID ditap...
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                    <div>
                        <label class="block text-sm font-medium">UID Kartu</label>
                        <input id="rfid_uid" type="text" class="w-full border rounded p-2 bg-gray-100" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Kode Kartu</label>
                        <input id="card_code" type="text" class="w-full border rounded p-2 bg-gray-100" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Kode Bidang</label>
                        <input id="field_unit_code" type="text" class="w-full border rounded p-2 bg-gray-100" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Nama Bidang</label>
                        <input id="field_unit_name" type="text" class="w-full border rounded p-2 bg-gray-100" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Saldo Virtual</label>
                        <input id="balance" type="text" class="w-full border rounded p-2 bg-gray-100" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-medium">Waktu Tap</label>
                        <input id="tapped_at" type="text" class="w-full border rounded p-2 bg-gray-100" readonly>
                    </div>
                </div>
            </div>

            <div class="bg-white p-6 rounded-lg shadow">
                <h3 class="text-lg font-bold mb-4">Form Transaksi</h3>

                <form id="transactionForm">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium">Vendor</label>
                            <select id="vendor_code" class="w-full border rounded p-2">
                                <option value="">Pilih Vendor</option>
                                @foreach ($vendors as $vendor)
                                    <option value="{{ $vendor->code }}">
                                        {{ $vendor->code }} - {{ $vendor->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Item Belanja</label>
                            <input id="item_name" type="text" class="w-full border rounded p-2" placeholder="Contoh: Kertas A4">
                        </div>

                        <div>
                            <label class="block text-sm font-medium">Nominal</label>
                            <input id="amount" type="number" class="w-full border rounded p-2" placeholder="Contoh: 30000">
                        </div>
                    </div>

                    <button type="submit" class="mt-5 px-4 py-2 bg-blue-600 text-white rounded">
                        Proses Transaksi
                    </button>
                </form>

                <div id="transactionResult" class="mt-5 hidden p-4 rounded"></div>
            </div>
        </div>
    </div>

    <script>
        let latestUid = null;

        async function fetchLatestCardTap() {
            try {
                const response = await fetch('/api/latest-card-tap');
                const result = await response.json();

                const cardStatus = document.getElementById('cardStatus');

                if (result.status === 'valid') {
                    const data = result.data;
                    latestUid = data.rfid_uid;

                    cardStatus.className = 'p-4 rounded bg-green-100 text-green-800';
                    cardStatus.innerText = 'Kartu valid terdeteksi: ' + data.card_label;

                    document.getElementById('rfid_uid').value = data.rfid_uid;
                    document.getElementById('card_code').value = data.card_code;
                    document.getElementById('field_unit_code').value = data.field_unit_code;
                    document.getElementById('field_unit_name').value = data.field_unit_name;
                    document.getElementById('balance').value = 'Rp' + Number(data.balance).toLocaleString('id-ID');
                    document.getElementById('tapped_at').value = data.tapped_at;
                } else if (result.status === 'invalid') {
                    latestUid = null;

                    cardStatus.className = 'p-4 rounded bg-red-100 text-red-800';
                    cardStatus.innerText = 'Kartu tidak valid: ' + result.message;

                    document.getElementById('rfid_uid').value = result.data?.rfid_uid ?? '';
                    document.getElementById('card_code').value = '';
                    document.getElementById('field_unit_code').value = '';
                    document.getElementById('field_unit_name').value = '';
                    document.getElementById('balance').value = '';
                    document.getElementById('tapped_at').value = result.data?.tapped_at ?? '';
                }
            } catch (error) {
                console.error(error);
            }
        }

        document.getElementById('transactionForm').addEventListener('submit', async function (event) {
            event.preventDefault();

            const resultBox = document.getElementById('transactionResult');

            if (!latestUid) {
                resultBox.className = 'mt-5 p-4 rounded bg-red-100 text-red-800';
                resultBox.innerText = 'Tap kartu terlebih dahulu sebelum transaksi.';
                resultBox.classList.remove('hidden');
                return;
            }

            const payload = {
                rfid_uid: latestUid,
                vendor_code: document.getElementById('vendor_code').value,
                item_name: document.getElementById('item_name').value,
                amount: Number(document.getElementById('amount').value),
            };

            try {
                const response = await fetch('/api/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                    },
                    body: JSON.stringify(payload),
                });

                const result = await response.json();

                if (result.status === 'success') {
                    resultBox.className = 'mt-5 p-4 rounded bg-green-100 text-green-800';
                } else if (result.status === 'pending_approval') {
                    resultBox.className = 'mt-5 p-4 rounded bg-yellow-100 text-yellow-800';
                } else {
                    resultBox.className = 'mt-5 p-4 rounded bg-red-100 text-red-800';
                }

                resultBox.innerText = result.message;
                resultBox.classList.remove('hidden');

                fetchLatestCardTap();
            } catch (error) {
                resultBox.className = 'mt-5 p-4 rounded bg-red-100 text-red-800';
                resultBox.innerText = 'Terjadi error saat memproses transaksi.';
                resultBox.classList.remove('hidden');
            }
        });

        fetchLatestCardTap();
        setInterval(fetchLatestCardTap, 2000);
    </script>
</x-app-layout>
