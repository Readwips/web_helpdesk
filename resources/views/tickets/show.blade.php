<x-app-layout>
    <x-slot:header>Detail {{ $ticket->ticket_number }}</x-slot:header>

    <a class="mb-6 inline-flex items-center gap-2 text-sm font-semibold text-muted hover:text-accent" href="{{ route('tickets.index') }}"><x-icon name="arrow" size="16" class="rotate-180" /> Semua tiket</a>
    <x-page-header :eyebrow="$ticket->ticket_number" :title="$ticket->title" description="Informasi, percakapan, dan riwayat penanganan tiket.">
        <x-slot:actions>
            @can('update', $ticket)<a class="btn-secondary" href="{{ route('tickets.edit', $ticket) }}">Edit tiket</a>@endcan
            @can('delete', $ticket)
                <form method="POST" action="{{ route('tickets.destroy', $ticket) }}" onsubmit="return confirm('Hapus tiket ini? Tindakan ini tidak dapat dibatalkan.')">
                    @csrf @method('DELETE')
                    <button class="btn-danger" type="submit">Hapus</button>
                </form>
            @endcan
        </x-slot:actions>
    </x-page-header>

    <div class="mb-6 flex flex-wrap gap-2"><x-badge :value="$ticket->priority" /><x-badge :value="$ticket->status" /></div>

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="min-w-0 space-y-6">
            <x-panel title="Informasi masalah" description="Konteks utama yang diberikan saat tiket dibuat.">
                <dl class="grid gap-x-6 gap-y-5 text-sm sm:grid-cols-2 lg:grid-cols-3">
                    <div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Pelapor</dt><dd class="mt-1.5 font-semibold text-ink">{{ $ticket->user->name }}</dd><dd class="mt-0.5 text-xs text-muted">{{ $ticket->user->department?->name ?? 'Tanpa departemen' }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Teknisi</dt><dd class="mt-1.5 font-semibold text-ink">{{ $ticket->technician?->name ?? 'Belum ditugaskan' }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Kategori</dt><dd class="mt-1.5 font-semibold text-ink">{{ $ticket->category->name }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Lokasi</dt><dd class="mt-1.5 font-semibold text-ink">{{ $ticket->location }}</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Aset terkait</dt><dd class="mt-1.5 font-semibold">@if($ticket->asset)<a class="text-accent hover:text-accent-dark" href="{{ route('assets.show', $ticket->asset) }}">{{ $ticket->asset->asset_code }}</a>@else<span class="text-muted">Tidak ada</span>@endif</dd></div>
                    <div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Durasi</dt><dd class="mt-1.5 font-semibold text-ink">{{ $ticket->resolution_minutes !== null ? $ticket->resolution_minutes.' menit' : 'Masih berjalan' }}</dd></div>
                </dl>

                <div class="mt-7 border-t border-line pt-6"><h3 class="text-sm font-bold text-ink">Deskripsi</h3><p class="mt-3 whitespace-pre-line text-sm leading-7 text-[#444440]">{{ $ticket->description }}</p></div>

                @if($ticket->diagnosis || $ticket->solution)
                    <div class="mt-7 grid gap-4 border-t border-line pt-6 md:grid-cols-2">
                        @if($ticket->diagnosis)<div class="rounded-lg border border-line bg-[#FAFAF8] p-4"><p class="flex items-center gap-2 text-sm font-bold"><x-icon name="search" size="16" class="text-accent" /> Diagnosis</p><p class="mt-3 whitespace-pre-line text-sm leading-6 text-muted">{{ $ticket->diagnosis }}</p></div>@endif
                        @if($ticket->solution)<div class="rounded-lg border border-[#CDE5DE] bg-accent-pale p-4"><p class="flex items-center gap-2 text-sm font-bold text-[#174C40]"><x-icon name="check" size="16" /> Solusi</p><p class="mt-3 whitespace-pre-line text-sm leading-6 text-[#365E54]">{{ $ticket->solution }}</p></div>@endif
                    </div>
                @endif
            </x-panel>

            <x-panel title="Diskusi dan catatan" description="Pembaruan yang dapat dilihat sesuai hak akses pengguna.">
                <div class="space-y-4">
                    @forelse($comments as $comment)
                        <article class="rounded-lg border p-4 {{ $comment->is_internal ? 'border-amber-200 bg-amber-50/60' : 'border-line bg-white' }}">
                            <header class="flex flex-wrap items-center justify-between gap-2">
                                <div class="flex items-center gap-2"><span class="grid h-8 w-8 place-items-center rounded-full bg-[#EFEFEA] text-xs font-bold">{{ mb_strtoupper(mb_substr($comment->user->name, 0, 1)) }}</span><div><p class="text-sm font-bold">{{ $comment->user->name }}</p>@if($comment->is_internal)<p class="text-[10px] font-bold uppercase tracking-wider text-amber-700">Catatan internal</p>@endif</div></div>
                                <time class="text-xs text-muted" datetime="{{ $comment->created_at->toIso8601String() }}">{{ $comment->created_at->format('d M Y H:i') }}</time>
                            </header>
                            <p class="mt-3 whitespace-pre-line text-sm leading-6 text-[#444440]">{{ $comment->comment }}</p>
                            @if($comment->attachments->isNotEmpty())
                                <div class="mt-4 flex flex-wrap gap-2">@foreach($comment->attachments as $file)<a class="inline-flex items-center gap-2 rounded-lg border border-line bg-white px-3 py-2 text-xs font-semibold text-accent hover:border-accent" href="{{ route('attachments.download', $file) }}"><x-icon name="paperclip" size="15" /> {{ $file->original_name }} <span class="text-muted">({{ number_format($file->file_size / 1024) }} KB)</span></a>@endforeach</div>
                            @endif
                        </article>
                    @empty
                        <x-empty-state title="Belum ada percakapan" description="Tambahkan komentar untuk memberikan konteks atau pembaruan." icon="message" />
                    @endforelse
                </div>

                <form class="mt-6 border-t border-line pt-6" method="POST" enctype="multipart/form-data" action="{{ route('tickets.comments.store', $ticket) }}">
                    @csrf
                    <label class="label" for="comment">Tambahkan komentar</label>
                    <textarea id="comment" class="input" name="comment" rows="4" required placeholder="Tulis komentar atau pembaruan...">{{ old('comment') }}</textarea>
                    <div class="mt-4 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex flex-col gap-3 sm:flex-row sm:items-center">
                            <label class="inline-flex cursor-pointer items-center gap-2 text-sm font-semibold text-muted"><x-icon name="paperclip" size="17" /><span>Lampirkan berkas</span><input type="file" name="attachments[]" multiple accept=".jpg,.jpeg,.png,.pdf" class="max-w-52 text-xs text-muted file:mr-2 file:rounded-md file:border-0 file:bg-[#EFEFEA] file:px-3 file:py-2 file:text-xs file:font-semibold file:text-ink"></label>
                            @if(auth()->user()->role !== 'user')<label class="inline-flex items-center gap-2 text-sm text-muted"><input type="checkbox" name="is_internal" value="1" class="rounded border-line text-accent focus:ring-accent"> Catatan internal</label>@endif
                        </div>
                        <button class="btn-primary" type="submit">Kirim komentar</button>
                    </div>
                    <p class="field-help">Format JPG, PNG, atau PDF; maksimal 5 MB per berkas.</p>
                </form>
            </x-panel>

            <x-panel title="Riwayat status" description="Setiap perubahan penting tercatat bersama pelaku dan waktunya.">
                <ol>
                    @forelse($ticket->histories as $history)
                        <li class="relative flex gap-4 pb-7 last:pb-0">
                            @unless($loop->last)<span class="absolute left-[9px] top-5 h-full w-px bg-line"></span>@endunless
                            <span class="relative z-10 mt-0.5 h-[19px] w-[19px] shrink-0 rounded-full border-4 border-accent-soft bg-accent"></span>
                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-2"><p class="text-sm font-bold text-ink">{{ $history->action }}</p><time class="text-xs text-muted" datetime="{{ $history->created_at->toIso8601String() }}">{{ $history->created_at->format('d M Y H:i') }}</time></div>
                                <p class="mt-1 text-xs text-muted">Oleh {{ $history->actor->name }}</p>
                                @if($history->old_status || $history->new_status)<div class="mt-2 flex items-center gap-2 text-xs"><span>{{ str($history->old_status ?? '-')->replace('_', ' ')->title() }}</span><x-icon name="arrow" size="14" class="text-muted" /><span class="font-semibold text-accent">{{ str($history->new_status ?? '-')->replace('_', ' ')->title() }}</span></div>@endif
                                @if($history->note)<p class="mt-2 text-sm leading-6 text-muted">{{ $history->note }}</p>@endif
                            </div>
                        </li>
                    @empty
                        <x-empty-state title="Belum ada riwayat" description="Perubahan status tiket akan tercatat di sini." icon="clock" />
                    @endforelse
                </ol>
            </x-panel>
        </div>

        <aside class="space-y-6">
            @if(auth()->user()->isAdmin())
                <x-panel title="Penugasan teknisi" description="Pilih teknisi dan sesuaikan prioritas tiket.">
                    <form method="POST" action="{{ route('tickets.assign', $ticket) }}" class="space-y-4">
                        @csrf @method('PUT')
                        <div><label class="label" for="technician_id">Teknisi</label><select id="technician_id" class="input" name="technician_id" required><option value="">Pilih teknisi</option>@foreach($technicians as $technician)<option value="{{ $technician->id }}" @selected($ticket->technician_id === $technician->id)>{{ $technician->name }}</option>@endforeach</select></div>
                        <div><label class="label" for="assignment_priority">Prioritas</label><select id="assignment_priority" class="input" name="priority">@foreach(['rendah', 'sedang', 'tinggi', 'kritis'] as $value)<option value="{{ $value }}" @selected($ticket->priority === $value)>{{ ucfirst($value) }}</option>@endforeach</select></div>
                        <button class="btn-primary w-full" type="submit">Simpan penugasan</button>
                    </form>
                </x-panel>
            @endif

            @can('handle', $ticket)
                <x-panel title="Proses tiket" description="Catat diagnosis, solusi, dan tahap pekerjaan terbaru.">
                    <form method="POST" action="{{ route('tickets.status', $ticket) }}" class="space-y-4">
                        @csrf @method('PUT')
                        <div><label class="label" for="workflow_status">Tahap selanjutnya</label><select id="workflow_status" class="input" name="status"><option value="diproses">Mulai / lanjutkan proses</option><option value="menunggu_konfirmasi">Kirim untuk konfirmasi</option><option value="dibatalkan">Batalkan tiket</option></select></div>
                        <div><label class="label" for="diagnosis">Diagnosis</label><textarea id="diagnosis" class="input" name="diagnosis" rows="3" placeholder="Jelaskan penyebab masalah">{{ old('diagnosis', $ticket->diagnosis) }}</textarea></div>
                        <div><label class="label" for="solution">Solusi</label><textarea id="solution" class="input" name="solution" rows="4" placeholder="Wajib diisi sebelum meminta konfirmasi">{{ old('solution', $ticket->solution) }}</textarea></div>
                        <div><label class="label" for="note">Catatan tindakan</label><textarea id="note" class="input" name="note" rows="2" placeholder="Rangkuman pekerjaan yang dilakukan">{{ old('note') }}</textarea></div>
                        <button class="btn-primary w-full" type="submit">Perbarui workflow</button>
                    </form>
                </x-panel>
            @endcan

            @if(auth()->id() === $ticket->user_id && in_array($ticket->status, ['menunggu_konfirmasi', 'selesai']))
                <x-panel title="Konfirmasi pengguna" description="Pastikan masalah sudah benar-benar selesai.">
                    @if($ticket->status === 'menunggu_konfirmasi')
                        <form method="POST" action="{{ route('tickets.confirm', $ticket) }}">@csrf @method('PUT')<button class="btn-accent w-full" type="submit"><x-icon name="check" size="17" /> Konfirmasi selesai</button></form>
                    @endif
                    <form class="{{ $ticket->status === 'menunggu_konfirmasi' ? 'mt-5 border-t border-line pt-5' : '' }}" method="POST" action="{{ route('tickets.reopen', $ticket) }}">
                        @csrf @method('PUT')
                        <label class="label" for="reopen_note">Masalah masih terjadi?</label>
                        <textarea id="reopen_note" class="input" name="note" required placeholder="Jelaskan bagian yang masih bermasalah"></textarea>
                        <button class="btn-secondary mt-3 w-full" type="submit">Buka kembali tiket</button>
                    </form>
                </x-panel>
            @endif
        </aside>
    </div>
</x-app-layout>
