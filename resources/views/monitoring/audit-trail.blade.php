<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Audit Trail
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-6xl mx-auto sm:px-6 lg:px-8 bg-white p-6 rounded shadow">
            <table class="w-full border text-sm">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="border px-3 py-2 text-left">Waktu</th>
                        <th class="border px-3 py-2 text-left">Action</th>
                        <th class="border px-3 py-2 text-left">Module</th>
                        <th class="border px-3 py-2 text-left">Description</th>
                        <th class="border px-3 py-2 text-left">IP</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($logs as $log)
                        <tr>
                            <td class="border px-3 py-2">{{ $log->created_at }}</td>
                            <td class="border px-3 py-2">{{ $log->action }}</td>
                            <td class="border px-3 py-2">{{ $log->module }}</td>
                            <td class="border px-3 py-2">{{ $log->description }}</td>
                            <td class="border px-3 py-2">{{ $log->ip_address }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="border px-3 py-3 text-center text-gray-500">
                                Belum ada audit trail.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            <div class="mt-4">
                {{ $logs->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
