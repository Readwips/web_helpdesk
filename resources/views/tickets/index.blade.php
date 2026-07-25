<x-app-layout>
    <x-slot:header>Manajemen Tiket</x-slot:header>

    <x-page-header eyebrow="Permintaan dukungan" title="Tiket Helpdesk" description="Pantau antrean, penugasan, dan penyelesaian masalah TI dalam satu daftar.">
        @can('create', App\Models\Ticket::class)
            <x-slot:actions><a class="btn-primary" href="{{ route('tickets.create') }}"><x-icon name="plus" size="17" /> Tiket baru</a></x-slot:actions>
        @endcan
    </x-page-header>

    <form class="panel mb-6" method="GET" aria-label="Filter tiket">
        <div class="panel-body">
            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-6">
                <div class="md:col-span-2 xl:col-span-2"><label class="label" for="q">Cari tiket</label><div class="relative"><x-icon name="search" size="17" class="pointer-events-none absolute left-3.5 top-1/2 z-10 -translate-y-1/2 text-muted" /><input id="q" class="input !mt-1.5 pl-10" name="q" value="{{ request('q') }}" placeholder="Nomor, judul, pelapor, teknisi"></div></div>
                <div><label class="label" for="status">Status</label><select id="status" class="input" name="status"><option value="">Semua status</option>@foreach(['baru', 'ditugaskan', 'diproses', 'menunggu_konfirmasi', 'selesai', 'dibatalkan'] as $value)<option value="{{ $value }}" @selected(request('status') === $value)>{{ str($value)->replace('_', ' ')->title() }}</option>@endforeach</select></div>
                <div><label class="label" for="priority">Prioritas</label><select id="priority" class="input" name="priority"><option value="">Semua prioritas</option>@foreach(['rendah', 'sedang', 'tinggi', 'kritis'] as $value)<option value="{{ $value }}" @selected(request('priority') === $value)>{{ ucfirst($value) }}</option>@endforeach</select></div>
                <div><label class="label" for="ticket_category_id">Kategori</label><select id="ticket_category_id" class="input" name="ticket_category_id"><option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('ticket_category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
                @if(auth()->user()->isAdmin())
                    <div><label class="label" for="technician_id">Teknisi</label><select id="technician_id" class="input" name="technician_id"><option value="">Semua teknisi</option>@foreach($technicians as $technician)<option value="{{ $technician->id }}" @selected(request('technician_id') == $technician->id)>{{ $technician->name }}</option>@endforeach</select></div>
                @endif
                <div><label class="label" for="date_from">Dari tanggal</label><input id="date_from" type="date" class="input" name="date_from" value="{{ request('date_from') }}"></div>
                <div><label class="label" for="date_to">Sampai tanggal</label><input id="date_to" type="date" class="input" name="date_to" value="{{ request('date_to') }}"></div>
            </div>
            <div class="mt-5 flex flex-wrap justify-end gap-2"><a class="btn-secondary" href="{{ route('tickets.index') }}">Reset</a><button class="btn-primary" type="submit">Terapkan filter</button></div>
        </div>
    </form>

    <div class="table-wrap">
        <table class="table">
            <thead><tr><th>Nomor</th><th>Judul / pelapor</th><th>Kategori</th><th>Teknisi</th><th>Prioritas</th><th>Status</th><th>Dibuat</th></tr></thead>
            <tbody>
                @forelse($tickets as $ticket)
                    <tr class="{{ $ticket->priority === 'kritis' ? 'bg-red-50/50' : '' }}">
                        <td><a class="font-bold text-accent hover:text-accent-dark" href="{{ route('tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                        <td class="!whitespace-normal"><span class="block font-semibold text-ink">{{ $ticket->title }}</span><span class="mt-1 block text-xs text-muted">{{ $ticket->user->name }}</span></td>
                        <td>{{ $ticket->category->name }}</td>
                        <td>{{ $ticket->technician?->name ?? 'Belum ditugaskan' }}</td>
                        <td><x-badge :value="$ticket->priority" /></td>
                        <td><x-badge :value="$ticket->status" /></td>
                        <td>{{ $ticket->created_at->format('d M Y H:i') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="!p-0">
                            <x-empty-state title="Tiket tidak ditemukan" description="Ubah filter pencarian atau buat tiket dukungan baru." icon="ticket">
                                @can('create', App\Models\Ticket::class)
                                    <x-slot:action><a class="btn-primary" href="{{ route('tickets.create') }}">Buat tiket</a></x-slot:action>
                                @endcan
                            </x-empty-state>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-6">{{ $tickets->links() }}</div>
</x-app-layout>
