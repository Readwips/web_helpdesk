<x-app-layout>
    <x-slot:header>{{ $asset->exists ? 'Edit Aset' : 'Tambah Aset' }}</x-slot:header>
    <div class="mx-auto max-w-5xl">
        <a class="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-muted hover:text-accent" href="{{ route('assets.index') }}"><x-icon name="arrow" size="16" class="rotate-180" /> Kembali ke inventaris</a>
        <x-page-header eyebrow="Manajemen aset" :title="$asset->exists ? $asset->asset_code : 'Registrasi perangkat'" description="Lengkapi identitas, kondisi, dan lokasi perangkat." />
        <form class="panel" method="POST" action="{{ $asset->exists ? route('assets.update', $asset) : route('assets.store') }}">@csrf @if($asset->exists) @method('PUT') @endif
            <div class="panel-body grid gap-5 md:grid-cols-2">
                @foreach([['name', 'Nama perangkat', 'text'], ['brand', 'Merek', 'text'], ['model', 'Model', 'text'], ['serial_number', 'Nomor serial (opsional)', 'text'], ['purchase_date', 'Tanggal pembelian', 'date'], ['purchase_price', 'Harga pembelian', 'number'], ['warranty_end_date', 'Garansi berakhir', 'date'], ['location', 'Lokasi', 'text']] as [$field, $label, $type])
                    <div><label class="label" for="{{ $field }}">{{ $label }}</label><input id="{{ $field }}" class="input" type="{{ $type }}" name="{{ $field }}" value="{{ old($field, $asset->$field instanceof Carbon\CarbonInterface ? $asset->$field->format('Y-m-d') : $asset->$field) }}" @required(in_array($field, ['name', 'brand', 'model', 'location'])) @if($field === 'purchase_price') min="0" step="0.01" @endif><x-input-error :messages="$errors->get($field)" class="mt-2" /></div>
                @endforeach
                <div><label class="label" for="asset_category_id">Kategori</label><select id="asset_category_id" class="input" name="asset_category_id" required>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('asset_category_id', $asset->asset_category_id) == $category->id)>{{ $category->name }} ({{ $category->code }})</option>@endforeach</select></div>
                <div><label class="label" for="condition">Kondisi</label><select id="condition" class="input" name="condition">@foreach(['baik', 'perlu_perawatan', 'rusak_ringan', 'rusak_berat'] as $value)<option value="{{ $value }}" @selected(old('condition', $asset->condition ?: 'baik') === $value)>{{ str($value)->replace('_', ' ')->title() }}</option>@endforeach</select></div>
                <div><label class="label" for="status">Status</label><select id="status" class="input" name="status">@foreach(['tersedia', 'digunakan', 'diperbaiki', 'rusak', 'dipinjamkan', 'tidak_aktif'] as $value)<option value="{{ $value }}" @selected(old('status', $asset->status ?: 'tersedia') === $value)>{{ str($value)->replace('_', ' ')->title() }}</option>@endforeach</select></div>
                <div class="md:col-span-2"><label class="label" for="specifications">Spesifikasi</label><textarea id="specifications" class="input" name="specifications" rows="4">{{ old('specifications', $asset->specifications) }}</textarea></div>
                <div class="md:col-span-2"><label class="label" for="notes">Catatan</label><textarea id="notes" class="input" name="notes" rows="3">{{ old('notes', $asset->notes) }}</textarea></div>
            </div>
            <div class="flex flex-col-reverse gap-3 border-t border-line bg-[#FAFAF8] px-5 py-4 sm:flex-row sm:justify-end sm:px-6"><a class="btn-secondary" href="{{ route('assets.index') }}">Batal</a><button class="btn-primary" type="submit">Simpan aset</button></div>
        </form>
    </div>
</x-app-layout>
