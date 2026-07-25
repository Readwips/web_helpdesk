<x-app-layout>
    <x-slot:header>Dashboard {{ $user->role === 'admin' ? 'Administrator' : ($user->role === 'technician' ? 'Teknisi' : 'Saya') }}</x-slot:header>

    <x-page-header
        eyebrow="Ringkasan operasional"
        :title="'Selamat datang, '.$user->name"
        description="Pantau tiket, aset, dan pekerjaan terbaru dari satu ruang kerja."
    >
        @if($user->role === 'user' || $user->isAdmin())
            <x-slot:actions>
                <a class="btn-primary" href="{{ route('tickets.create') }}"><x-icon name="plus" size="17" /> Buat tiket</a>
            </x-slot:actions>
        @endif
    </x-page-header>

    @php
        $cards = [
            ['Total tiket', $stats['total_tickets'], 'ticket', 'neutral'],
            ['Tiket aktif', $stats['active'], 'clock', 'warning'],
            ['Menunggu konfirmasi', $stats['waiting'], 'message', 'accent'],
            [$user->isTechnician() ? 'Selesai bulan ini' : 'Tiket selesai', $user->isTechnician() ? $stats['done_month'] : $stats['done'], 'check', 'success'],
            ['Prioritas tinggi / kritis', $stats['urgent'], 'alert', 'danger'],
            ['Total aset', $assetStats['total'], 'asset', 'neutral'],
            ['Aset tersedia', $assetStats['available'], 'check', 'success'],
            ['Aset bermasalah', $assetStats['broken'], 'wrench', 'warning'],
        ];
    @endphp

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
        @foreach($cards as [$label, $value, $icon, $tone])
            <x-stat-card :label="$label" :value="number_format($value)" :icon="$icon" :tone="$tone" />
        @endforeach
    </div>

    @if($user->isAdmin() || $user->isTechnician())
        <div class="mt-4 grid gap-4 sm:grid-cols-2">
            @if($user->isAdmin())
                <x-stat-card label="Total biaya perbaikan" :value="'Rp '.number_format($repairCost, 0, ',', '.')" icon="report" tone="neutral">
                    <x-slot:footer>Akumulasi biaya dari seluruh catatan perbaikan.</x-slot:footer>
                </x-stat-card>
            @else
                <x-stat-card label="Rata-rata durasi penyelesaian" :value="number_format($averageResolution).' menit'" icon="clock" tone="accent">
                    <x-slot:footer>Rata-rata waktu dari tiket dibuat hingga selesai.</x-slot:footer>
                </x-stat-card>
            @endif
        </div>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <x-panel title="Tren tiket enam bulan" description="Jumlah tiket baru dalam setiap bulan." class="xl:col-span-2">
            <div class="h-72"><canvas id="ticketChart" aria-label="Grafik tren tiket enam bulan"></canvas></div>
        </x-panel>

        <x-panel title="Aset terkait" description="Perangkat yang relevan dengan ruang kerja Anda.">
            <div class="space-y-2">
                @forelse($assets as $asset)
                    <a href="{{ route('assets.show', $asset) }}" class="group flex items-center justify-between gap-3 rounded-lg border border-line p-3 transition hover:border-accent">
                        <div class="min-w-0"><p class="truncate text-sm font-bold text-ink">{{ $asset->asset_code }}</p><p class="truncate text-xs text-muted">{{ $asset->name }} &middot; {{ $asset->category->name }}</p></div>
                        <x-icon name="arrow" size="16" class="text-muted transition group-hover:text-accent" />
                    </a>
                @empty
                    <x-empty-state title="Belum ada aset terkait" description="Aset yang ditugaskan atau berkaitan dengan tiket akan muncul di sini." icon="asset" class="min-h-48" />
                @endforelse
            </div>
        </x-panel>
    </div>

    @if($user->isAdmin())
        <div class="mt-6 grid gap-6 md:grid-cols-2 xl:grid-cols-3">
            @foreach([
                ['statusChart', 'Tiket berdasarkan status'],
                ['priorityChart', 'Tiket berdasarkan prioritas'],
                ['categoryChart', 'Tiket berdasarkan kategori'],
                ['assetCategoryChart', 'Aset berdasarkan kategori'],
                ['conditionChart', 'Aset berdasarkan kondisi'],
                ['technicianChart', 'Tiket selesai per teknisi'],
            ] as [$id, $label])
                <x-panel :title="$label"><div class="h-64"><canvas id="{{ $id }}" aria-label="{{ $label }}"></canvas></div></x-panel>
            @endforeach
        </div>
    @endif

    <div class="mt-6 grid gap-6 xl:grid-cols-3">
        <section class="xl:col-span-2">
            <div class="mb-3 flex items-center justify-between"><h2 class="section-title">Tiket terbaru</h2><a class="text-sm font-semibold text-accent hover:text-accent-dark" href="{{ route('tickets.index') }}">Lihat semua</a></div>
            <div class="table-wrap">
                <table class="table">
                    <thead><tr><th>Nomor</th><th>Judul</th><th>Prioritas</th><th>Status</th></tr></thead>
                    <tbody>
                        @forelse($latestTickets as $ticket)
                            <tr>
                                <td><a class="font-bold text-accent hover:text-accent-dark" href="{{ route('tickets.show', $ticket) }}">{{ $ticket->ticket_number }}</a></td>
                                <td class="!whitespace-normal"><span class="font-semibold text-ink">{{ $ticket->title }}</span></td>
                                <td><x-badge :value="$ticket->priority" /></td>
                                <td><x-badge :value="$ticket->status" /></td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="!p-0"><x-empty-state title="Belum ada tiket" description="Tiket terbaru akan tampil di sini." /></td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <x-panel title="Artikel terbaru" description="Panduan praktis dari pusat pengetahuan.">
            <div class="divide-y divide-line">
                @forelse($articles as $article)
                    <a href="{{ route('knowledge.show', $article) }}" class="group flex items-center justify-between gap-3 py-3 first:pt-0 last:pb-0"><span class="text-sm font-semibold leading-6 text-ink group-hover:text-accent">{{ $article->title }}</span><x-icon name="arrow" size="16" class="shrink-0 text-muted" /></a>
                @empty
                    <x-empty-state title="Belum ada artikel" description="Artikel baru akan tampil di sini." icon="book" />
                @endforelse
            </div>
        </x-panel>
    </div>

    @if($user->isAdmin())
        <div class="mt-6 grid gap-6 lg:grid-cols-2">
            <x-panel title="Tiket kritis aktif" description="Permintaan yang perlu mendapat perhatian segera.">
                <div class="space-y-2">@forelse($criticalTickets as $item)<a class="flex items-center justify-between gap-3 rounded-lg border border-red-200 bg-red-50/40 p-3 text-sm" href="{{ route('tickets.show', $item) }}"><span class="min-w-0 truncate font-semibold">{{ $item->ticket_number }} &mdash; {{ $item->title }}</span><x-badge :value="$item->status" /></a>@empty<x-empty-state title="Tidak ada tiket kritis" description="Tidak ada tiket kritis yang masih aktif." icon="check" />@endforelse</div>
            </x-panel>
            <x-panel title="Aset perlu perhatian" description="Perangkat dengan kondisi yang perlu ditindaklanjuti.">
                <div class="space-y-2">@forelse($problemAssets as $item)<a class="flex items-center justify-between gap-3 rounded-lg border border-line p-3 text-sm hover:border-accent" href="{{ route('assets.show', $item) }}"><span class="min-w-0 truncate font-semibold">{{ $item->asset_code }} &mdash; {{ $item->name }}</span><x-badge :value="$item->condition" /></a>@empty<x-empty-state title="Semua aset dalam kondisi baik" description="Tidak ada aset bermasalah saat ini." icon="check" />@endforelse</div>
            </x-panel>
            <x-panel title="Garansi segera berakhir" description="Aset dengan masa garansi berakhir dalam 90 hari.">
                <div class="space-y-2">@forelse($warrantyAssets as $item)<a class="flex justify-between gap-4 rounded-lg border border-line p-3 text-sm hover:border-accent" href="{{ route('assets.show', $item) }}"><span class="font-semibold">{{ $item->asset_code }}</span><span class="text-muted">{{ $item->warranty_end_date->format('d M Y') }}</span></a>@empty<x-empty-state title="Tidak ada tenggat garansi" description="Belum ada garansi yang segera berakhir." icon="asset" />@endforelse</div>
            </x-panel>
            <x-panel title="Perbaikan terbaru" description="Aktivitas perbaikan aset yang baru dicatat.">
                <div class="space-y-2">@forelse($recentRepairs as $item)<a class="block rounded-lg border border-line p-3 text-sm hover:border-accent" href="{{ route('repairs.show', $item) }}"><span class="font-semibold">{{ $item->asset->asset_code }} &middot; {{ $item->technician->name }}</span><span class="mt-1 block text-xs text-muted">Rp {{ number_format($item->repair_cost, 0, ',', '.') }}</span></a>@empty<x-empty-state title="Belum ada perbaikan" description="Catatan perbaikan terbaru akan tampil di sini." icon="wrench" />@endforelse</div>
            </x-panel>
        </div>
    @endif

    @if(in_array($user->role, ['admin', 'technician']) && $upcomingMaintenance->isNotEmpty())
        <x-panel title="Jadwal perawatan aset" description="Agenda perawatan yang akan datang." class="mt-6">
            <div class="grid gap-3 sm:grid-cols-2 xl:grid-cols-3">
                @foreach($upcomingMaintenance as $item)
                    <a href="{{ route('assets.show', $item->asset) }}" class="rounded-lg border border-line p-4 text-sm transition hover:border-accent"><span class="block font-bold">{{ $item->asset->asset_code }}</span><span class="mt-1 block text-muted">{{ $item->next_maintenance_date->format('d M Y') }}</span></a>
                @endforeach
            </div>
        </x-panel>
    @endif

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const make = (id, type, labels, values, color) => {
                    const element = document.getElementById(id);
                    if (!element) return;
                    new Chart(element, {
                        type,
                        data: { labels, datasets: [{ data: values, label: 'Jumlah', backgroundColor: color, borderColor: color, borderWidth: 2, fill: type === 'line', tension: .35 }] },
                        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { display: type === 'doughnut', labels: { usePointStyle: true, boxWidth: 8 } } }, scales: type === 'doughnut' ? {} : { x: { grid: { display: false } }, y: { beginAtZero: true, grid: { color: '#ECECE7' } } } }
                    });
                };
                make('ticketChart', 'line', @json($chart['labels']), @json($chart['values']), '#17715E');
                @if($adminCharts)
                    make('statusChart', 'doughnut', @json($adminCharts['status']['labels']), @json($adminCharts['status']['values']), ['#17715E', '#6D6D67', '#D89B2B', '#4B8CA8', '#4A936F', '#B9B9B2']);
                    make('priorityChart', 'bar', @json($adminCharts['priority']['labels']), @json($adminCharts['priority']['values']), ['#B9B9B2', '#17715E', '#D97706', '#B42318']);
                    make('categoryChart', 'bar', @json($adminCharts['category']['labels']), @json($adminCharts['category']['values']), '#17715E');
                    make('assetCategoryChart', 'bar', @json($adminCharts['assetCategory']['labels']), @json($adminCharts['assetCategory']['values']), '#4B8CA8');
                    make('conditionChart', 'doughnut', @json($adminCharts['condition']['labels']), @json($adminCharts['condition']['values']), ['#4A936F', '#D89B2B', '#D97706', '#B42318']);
                    make('technicianChart', 'bar', @json($adminCharts['technician']['labels']), @json($adminCharts['technician']['values']), '#6A5A9B');
                @endif
            });
        </script>
    @endpush
</x-app-layout>
