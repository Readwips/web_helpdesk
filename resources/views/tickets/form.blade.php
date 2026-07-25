<x-app-layout>
    <x-slot:header>{{ $ticket->exists ? 'Edit Tiket' : 'Buat Tiket' }}</x-slot:header>

    <div class="mx-auto max-w-4xl">
        <a class="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-muted hover:text-accent" href="{{ route('tickets.index') }}"><x-icon name="arrow" size="16" class="rotate-180" /> Kembali ke daftar tiket</a>
        <x-page-header
            eyebrow="Permintaan dukungan"
            :title="$ticket->exists ? 'Perbarui tiket' : 'Laporkan masalah TI'"
            :description="$ticket->exists ? 'Perbarui informasi tiket tanpa mengubah riwayat workflow.' : 'Berikan informasi yang jelas agar tim dapat menindaklanjuti dengan tepat.'"
        />

        <form class="panel" method="POST" action="{{ $ticket->exists ? route('tickets.update', $ticket) : route('tickets.store') }}">
            @csrf
            @if($ticket->exists) @method('PUT') @endif

            <div class="panel-body space-y-6">
                <div>
                    <label class="label" for="title">Judul masalah</label>
                    <input id="title" class="input" name="title" required maxlength="255" value="{{ old('title', $ticket->title) }}" placeholder="Ringkas masalah dalam satu kalimat">
                    <x-input-error :messages="$errors->get('title')" class="mt-2" />
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div><label class="label" for="ticket_category_id">Kategori</label><select id="ticket_category_id" class="input" name="ticket_category_id" required><option value="">Pilih kategori</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('ticket_category_id', $ticket->ticket_category_id) == $category->id)>{{ $category->name }}</option>@endforeach</select><x-input-error :messages="$errors->get('ticket_category_id')" class="mt-2" /></div>
                    <div><label class="label" for="priority">Prioritas</label><select id="priority" class="input" name="priority" required>@foreach(['rendah', 'sedang', 'tinggi', 'kritis'] as $value)<option value="{{ $value }}" @selected(old('priority', $ticket->priority ?: 'sedang') === $value)>{{ ucfirst($value) }}</option>@endforeach</select><p class="field-help">Pilih berdasarkan dampak masalah terhadap pekerjaan.</p><x-input-error :messages="$errors->get('priority')" class="mt-2" /></div>
                </div>

                <div class="grid gap-5 md:grid-cols-2">
                    <div><label class="label" for="location">Lokasi</label><input id="location" class="input" name="location" required value="{{ old('location', $ticket->location) }}" placeholder="Contoh: Lantai 2, Ruang Keuangan"><x-input-error :messages="$errors->get('location')" class="mt-2" /></div>
                    <div><label class="label" for="asset_id">Aset terkait <span class="font-normal text-muted">(opsional)</span></label><select id="asset_id" class="input" name="asset_id"><option value="">Tidak ada aset terkait</option>@foreach($assets as $asset)<option value="{{ $asset->id }}" @selected(old('asset_id', $ticket->asset_id) == $asset->id)>{{ $asset->asset_code }} &mdash; {{ $asset->name }}</option>@endforeach</select><x-input-error :messages="$errors->get('asset_id')" class="mt-2" /></div>
                </div>

                <div>
                    <label class="label" for="description">Deskripsi lengkap</label>
                    <textarea id="description" class="input" rows="7" name="description" required placeholder="Jelaskan apa yang terjadi, kapan mulai terjadi, dan dampaknya pada pekerjaan.">{{ old('description', $ticket->description) }}</textarea>
                    <p class="field-help">Sertakan gejala, waktu kejadian, dan langkah yang sudah dicoba.</p>
                    <x-input-error :messages="$errors->get('description')" class="mt-2" />
                </div>
            </div>
            <div class="flex flex-col-reverse gap-3 border-t border-line bg-[#FAFAF8] px-5 py-4 sm:flex-row sm:justify-end sm:px-6">
                <a class="btn-secondary" href="{{ route('tickets.index') }}">Batal</a>
                <button class="btn-primary" type="submit">{{ $ticket->exists ? 'Simpan perubahan' : 'Buat tiket' }}</button>
            </div>
        </form>
    </div>
</x-app-layout>
