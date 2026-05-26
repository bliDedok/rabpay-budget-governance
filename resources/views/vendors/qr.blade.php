<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            QR Vendor
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-3 gap-6">

            @foreach ($vendors as $vendor)
                <div class="bg-white p-6 rounded shadow text-center">
                    <h3 class="font-bold text-lg">{{ $vendor->name }}</h3>
                    <p class="text-sm text-gray-500">{{ $vendor->code }} - {{ $vendor->category }}</p>

                    <div class="mt-4 flex justify-center">
                        <img
                            src="https://api.qrserver.com/v1/create-qr-code/?size=180x180&data={{ urlencode($vendor->code) }}"
                            alt="QR {{ $vendor->code }}"
                        >
                    </div>

                    <p class="mt-4 text-sm">
                        QR ini berisi kode vendor:
                        <strong>{{ $vendor->code }}</strong>
                    </p>
                </div>
            @endforeach

        </div>
    </div>
</x-app-layout>
