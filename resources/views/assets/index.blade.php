<x-app-layout>
    <x-slot:header>Inventaris Perangkat TI</x-slot:header>
    <x-page-header eyebrow="Manajemen aset" title="Inventaris aset" description="Pantau perangkat, kondisi, lokasi, dan penanggung jawabnya.">
        @can('create', App\Models\Asset::class)
            <x-slot:actions>
                <a class="btn-primary" href="{{ route('assets.create') }}"><x-icon name="plus" size="17" /> Tambah aset</a>
            </x-slot:actions>
        @endcan
    </x-page-header>
    <form class="panel mb-6" method="GET" aria-label="Filter aset"><div class="panel-body"><div class="grid gap-4 md:grid-cols-2 xl:grid-cols-5">
        <div class="md:col-span-2"><label class="label" for="asset_q">Cari aset</label><div class="relative"><x-icon name="search" size="17" class="pointer-events-none absolute left-3.5 top-1/2 z-10 -translate-y-1/2 text-muted" /><input id="asset_q" class="input pl-10" name="q" value="{{ request('q') }}" placeholder="Kode, serial, atau nama perangkat"></div></div>
        <div><label class="label" for="asset_category_id">Kategori</label><select id="asset_category_id" class="input" name="asset_category_id"><option value="">Semua kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(request('asset_category_id') == $category->id)>{{ $category->name }}</option>@endforeach</select></div>
        <div><label class="label" for="asset_status">Status</label><select id="asset_status" class="input" name="status"><option value="">Semua status</option>@foreach(['tersedia', 'digunakan', 'diperbaiki', 'rusak', 'dipinjamkan', 'tidak_aktif'] as $value)<option value="{{ $value }}" @selected(request('status') === $value)>{{ str($value)->replace('_', ' ')->title() }}</option>@endforeach</select></div>
        <div><label class="label" for="asset_condition">Kondisi</label><select id="asset_condition" class="input" name="condition"><option value="">Semua kondisi</option>@foreach(['baik', 'perlu_perawatan', 'rusak_ringan', 'rusak_berat'] as $value)<option value="{{ $value }}" @selected(request('condition') === $value)>{{ str($value)->replace('_', ' ')->title() }}</option>@endforeach</select></div>
        <div><label class="label" for="asset_location">Lokasi</label><input id="asset_location" class="input" name="location" value="{{ request('location') }}" placeholder="Lokasi aset"></div>
    </div><div class="mt-5 flex justify-end gap-2"><a class="btn-secondary" href="{{ route('assets.index') }}">Reset</a><button class="btn-primary" type="submit">Terapkan filter</button></div></div></form>
    <div class="table-wrap"><table class="table"><thead><tr><th>Kode</th><th>Perangkat</th><th>Serial</th><th>Lokasi / pengguna</th><th>Kondisi</th><th>Status</th></tr></thead><tbody>
        @forelse($assets as $asset)<tr><td><a class="font-bold text-accent hover:text-accent-dark" href="{{ route('assets.show', $asset) }}">{{ $asset->asset_code }}</a></td><td><span class="block font-semibold text-ink">{{ $asset->name }}</span><span class="mt-1 block text-xs text-muted">{{ $asset->brand }} {{ $asset->model }} &middot; {{ $asset->category->name }}</span></td><td>{{ $asset->serial_number ?? '-' }}</td><td><span class="block">{{ $asset->location }}</span><span class="mt-1 block text-xs text-muted">{{ $asset->assignedUser?->name ?? 'Tidak ditugaskan' }}</span></td><td><x-badge :value="$asset->condition" /></td><td><x-badge :value="$asset->status" /></td></tr>
        @empty<tr><td colspan="6" class="!p-0"><x-empty-state title="Aset tidak ditemukan" description="Ubah filter atau tambahkan aset baru ke inventaris." icon="asset" /></td></tr>@endforelse
    </tbody></table></div><div class="mt-6">{{ $assets->links() }}</div>
</x-app-layout>
