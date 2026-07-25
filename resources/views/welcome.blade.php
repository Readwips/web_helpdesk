<!doctype html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="IT Helpdesk membantu tim perusahaan mengelola tiket dukungan dan aset TI dalam satu ruang kerja.">
    <title>{{ config('app.name', 'IT Helpdesk') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-white text-ink">
    <header class="sticky top-0 z-30 border-b border-line bg-white/95 backdrop-blur">
        <nav class="landing-container flex h-[4.75rem] items-center justify-between" aria-label="Navigasi utama">
            <x-brand-mark href="{{ url('/') }}" />
            <div class="hidden items-center gap-8 md:flex">
                <a href="#cara-kerja" class="text-sm font-semibold text-muted transition hover:text-ink">Cara kerja</a>
                <a href="#fitur" class="text-sm font-semibold text-muted transition hover:text-ink">Fitur</a>
                <a href="#kontak" class="text-sm font-semibold text-muted transition hover:text-ink">Mulai</a>
            </div>
            <div class="flex items-center gap-2">
                @auth
                    <a class="btn-primary" href="{{ route('dashboard') }}">Buka dashboard <x-icon name="arrow" size="17" /></a>
                @else
                    <a class="btn-ghost hidden sm:inline-flex" href="{{ route('login') }}">Masuk</a>
                    @if(Route::has('register'))
                        <a class="btn-primary" href="{{ route('register') }}">Buat akun</a>
                    @else
                        <a class="btn-primary" href="{{ route('login') }}">Masuk</a>
                    @endif
                @endauth
            </div>
        </nav>
    </header>

    <main>
        <section class="overflow-hidden border-b border-line bg-canvas">
            <div class="landing-container pb-16 pt-20 sm:pb-24 sm:pt-28 lg:pb-28 lg:pt-36">
                <div class="mx-auto max-w-5xl text-center">
                    <p class="eyebrow">Layanan TI internal yang lebih teratur</p>
                    <h1 class="mt-6 text-5xl font-semibold leading-[0.98] tracking-[-0.055em] sm:text-7xl lg:text-[5.5rem]">Setiap masalah tercatat.<br class="hidden sm:block"> Setiap solusi terlihat.</h1>
                    <p class="mx-auto mt-7 max-w-2xl text-base leading-7 text-muted sm:text-lg">IT Helpdesk menyatukan pelaporan masalah, penugasan teknisi, dokumentasi solusi, dan pengelolaan aset dalam alur yang ringkas.</p>
                    <div class="mt-9 flex flex-col justify-center gap-3 sm:flex-row">
                        @auth
                            <a class="btn-primary !px-6" href="{{ route('tickets.create') }}"><x-icon name="plus" size="17" /> Buat tiket</a>
                            <a class="btn-secondary !px-6" href="{{ route('dashboard') }}">Lihat dashboard</a>
                        @else
                            <a class="btn-primary !px-6" href="{{ route('login') }}">Masuk ke sistem <x-icon name="arrow" size="17" /></a>
                            <a class="btn-secondary !px-6" href="#cara-kerja">Pelajari alurnya</a>
                        @endauth
                    </div>
                </div>

                <div class="relative mx-auto mt-16 max-w-6xl sm:mt-20">
                    <div class="absolute -left-10 -top-12 h-48 w-48 rounded-full bg-accent-soft blur-3xl"></div>
                    <div class="mock-window relative shadow-float">
                        <div class="flex h-12 items-center justify-between border-b border-line px-4 sm:px-5">
                            <div class="flex gap-1.5" aria-hidden="true"><span class="h-2.5 w-2.5 rounded-full bg-line"></span><span class="h-2.5 w-2.5 rounded-full bg-line"></span><span class="h-2.5 w-2.5 rounded-full bg-accent"></span></div>
                            <span class="text-[10px] font-bold uppercase tracking-[0.14em] text-muted">Ruang kerja dukungan</span>
                        </div>
                        <div class="grid min-h-[27rem] grid-cols-[4.5rem_1fr] sm:grid-cols-[13rem_1fr]">
                            <div class="border-r border-line bg-[#FAFAF8] p-3 sm:p-5">
                                <div class="hidden sm:block"><x-brand-mark :compact="true" /></div>
                                <div class="mt-5 space-y-2 sm:mt-8">
                                    @foreach(['dashboard', 'ticket', 'asset', 'book'] as $index => $icon)
                                        <div class="flex h-10 items-center gap-3 rounded-lg {{ $index === 1 ? 'bg-accent-soft text-accent' : 'text-muted' }} px-3">
                                            <x-icon :name="$icon" size="16" />
                                            <span class="hidden h-2 rounded-full sm:block {{ $index === 1 ? 'w-20 bg-accent/25' : 'w-16 bg-line' }}"></span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="min-w-0 p-4 sm:p-7 lg:p-9">
                                <div class="flex items-end justify-between gap-4">
                                    <div><div class="h-2 w-20 rounded bg-accent/40"></div><div class="mt-3 h-5 w-36 rounded bg-ink sm:w-52"></div></div>
                                    <div class="h-10 w-24 rounded-lg bg-ink"></div>
                                </div>
                                <div class="mt-7 grid grid-cols-2 gap-3 xl:grid-cols-4">
                                    @foreach([['24', 'Tiket aktif'], ['08', 'Diproses'], ['05', 'Konfirmasi'], ['19', 'Selesai']] as [$value, $label])
                                        <div class="rounded-lg border border-line p-3 sm:p-4"><span class="block text-xl font-bold sm:text-2xl">{{ $value }}</span><span class="mt-2 block text-[9px] font-semibold uppercase tracking-wider text-muted">{{ $label }}</span></div>
                                    @endforeach
                                </div>
                                <div class="mt-4 overflow-hidden rounded-lg border border-line">
                                    <div class="grid grid-cols-[1fr_5rem] border-b border-line bg-[#FAFAF8] px-4 py-3 sm:grid-cols-[6rem_1fr_7rem_7rem]"><span class="mock-line w-12"></span><span class="mock-line hidden w-20 sm:block"></span><span class="mock-line hidden w-12 sm:block"></span><span class="mock-line w-12"></span></div>
                                    @foreach([['HD-0241', 'Akses Wi-Fi lantai dua terputus', 'Tinggi', 'Diproses'], ['HD-0240', 'Permintaan instalasi aplikasi', 'Normal', 'Ditugaskan'], ['HD-0239', 'Monitor ruang rapat tidak tampil', 'Normal', 'Konfirmasi'], ['HD-0238', 'Reset kata sandi akun internal', 'Rendah', 'Selesai']] as $row)
                                        <div class="grid grid-cols-[1fr_5rem] items-center border-b border-line px-4 py-3.5 last:border-0 sm:grid-cols-[6rem_1fr_7rem_7rem]">
                                            <span class="text-[10px] font-bold text-accent">{{ $row[0] }}</span><span class="hidden truncate text-xs font-semibold sm:block">{{ $row[1] }}</span><span class="hidden text-[10px] text-muted sm:block">{{ $row[2] }}</span><span class="rounded-full border border-line px-2 py-1 text-center text-[9px] font-bold text-muted">{{ $row[3] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <section id="cara-kerja" class="landing-section border-b border-line bg-white">
            <div class="landing-container">
                <div class="max-w-2xl">
                    <p class="eyebrow">Tiga langkah inti</p>
                    <h2 class="mt-4 text-4xl font-semibold leading-tight tracking-[-0.045em] sm:text-5xl">Alur yang jelas bagi semua orang.</h2>
                </div>

                <div class="mt-16 divide-y divide-line border-y border-line">
                    @foreach([
                        ['01', 'Laporkan tanpa kerumitan', 'Pengguna mengisi detail masalah dan sistem membuat nomor tiket otomatis. Semua informasi awal tersimpan rapi sejak tiket dibuat.', 'ticket'],
                        ['02', 'Tangani dengan konteks lengkap', 'Admin menugaskan teknisi yang tepat. Teknisi melihat prioritas, aset terkait, komentar, dan riwayat sebelum mulai bekerja.', 'wrench'],
                        ['03', 'Selesaikan dengan transparan', 'Diagnosis dan solusi dicatat. Tiket baru ditutup setelah pengguna mengonfirmasi bahwa masalah benar-benar selesai.', 'check'],
                    ] as [$number, $title, $copy, $icon])
                        <article class="grid gap-7 py-10 sm:grid-cols-[5rem_1fr] lg:grid-cols-[7rem_1fr_1fr] lg:items-center lg:py-14">
                            <span class="text-sm font-bold text-accent">{{ $number }}</span>
                            <div><div class="mb-4 grid h-11 w-11 place-items-center rounded-lg bg-accent-soft text-accent"><x-icon :name="$icon" size="20" /></div><h3 class="text-2xl font-bold tracking-[-0.03em]">{{ $title }}</h3></div>
                            <p class="max-w-lg text-sm leading-7 text-muted sm:col-start-2 lg:col-start-auto lg:text-base">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section class="landing-section overflow-hidden bg-canvas">
            <div class="landing-container grid items-center gap-14 lg:grid-cols-[0.9fr_1.1fr]">
                <div>
                    <p class="eyebrow">Riwayat yang utuh</p>
                    <h2 class="mt-4 text-4xl font-semibold leading-tight tracking-[-0.045em] sm:text-5xl">Progres tidak lagi menjadi teka-teki.</h2>
                    <p class="mt-6 max-w-lg text-base leading-7 text-muted">Setiap perubahan status meninggalkan jejak. Pengguna, admin, dan teknisi melihat konteks yang sama tanpa perlu mengejar pembaruan melalui banyak kanal.</p>
                </div>
                <div class="rounded-xl border border-line bg-white p-5 sm:p-8">
                    <div class="flex items-center justify-between border-b border-line pb-5"><div><p class="text-xs font-bold text-accent">HD-0241</p><p class="mt-1 font-bold">Akses Wi-Fi lantai dua terputus</p></div><span class="rounded-full bg-accent-soft px-3 py-1.5 text-xs font-bold text-accent">Diproses</span></div>
                    <ol class="mt-7 space-y-0">
                        @foreach([['Tiket dibuat', '08.42', true], ['Ditugaskan kepada teknisi', '08.55', true], ['Pemeriksaan sedang berlangsung', '09.20', true], ['Menunggu konfirmasi pengguna', '—', false]] as [$label, $time, $done])
                            <li class="relative flex gap-4 pb-7 last:pb-0">
                                @unless($loop->last)<span class="absolute left-[9px] top-5 h-full w-px bg-line"></span>@endunless
                                <span class="relative z-10 mt-0.5 h-[19px] w-[19px] shrink-0 rounded-full border-2 {{ $done ? 'border-accent bg-accent' : 'border-line bg-white' }}"></span>
                                <div class="flex flex-1 justify-between gap-4"><span class="text-sm font-semibold {{ $done ? 'text-ink' : 'text-muted' }}">{{ $label }}</span><span class="text-xs text-muted">{{ $time }}</span></div>
                            </li>
                        @endforeach
                    </ol>
                </div>
            </div>
            <div class="landing-container mt-16 sm:mt-20">
                <p class="mb-6 text-xs font-bold uppercase tracking-[0.16em] text-muted">Alur lengkap tiket</p>
                <ol class="grid gap-px overflow-hidden rounded-xl border border-line bg-line sm:grid-cols-2 lg:grid-cols-6">
                    @foreach([
                        ['01', 'Pengguna membuat tiket'],
                        ['02', 'Admin menugaskan'],
                        ['03', 'Teknisi memproses'],
                        ['04', 'Diagnosis dan solusi dicatat'],
                        ['05', 'Pengguna mengonfirmasi'],
                        ['06', 'Riwayat tetap tersimpan'],
                    ] as [$number, $label])
                        <li class="min-h-32 bg-white p-5"><span class="text-xs font-bold text-accent">{{ $number }}</span><span class="mt-5 block text-sm font-semibold leading-5">{{ $label }}</span></li>
                    @endforeach
                </ol>
            </div>
        </section>

        <section id="fitur" class="landing-section border-y border-line bg-white">
            <div class="landing-container">
                <div class="grid gap-8 lg:grid-cols-2 lg:items-end">
                    <div><p class="eyebrow">Kemampuan utama</p><h2 class="mt-4 text-4xl font-semibold leading-tight tracking-[-0.045em] sm:text-5xl">Dibangun untuk operasi TI sehari-hari.</h2></div>
                    <p class="max-w-lg text-base leading-7 text-muted lg:justify-self-end">Mulai dari permintaan sederhana hingga pemantauan aset, semua fungsi penting berada dalam sistem yang konsisten.</p>
                </div>
                <div class="mt-14 grid border-l border-t border-line sm:grid-cols-2 lg:grid-cols-3">
                    @foreach([
                        ['ticket', 'Tiket terstruktur', 'Nomor otomatis, kategori, prioritas, lampiran, dan status yang mudah dilacak.'],
                        ['users', 'Akses sesuai peran', 'Ruang kerja berbeda untuk pengguna, teknisi, dan administrator.'],
                        ['asset', 'Inventaris aset', 'Kondisi, lokasi, kepemilikan, dan riwayat perangkat dalam satu catatan.'],
                        ['book', 'Pusat pengetahuan', 'Solusi yang sudah teruji dapat ditemukan kembali oleh seluruh tim.'],
                        ['report', 'Laporan operasional', 'Ringkasan performa dan data yang siap diekspor untuk kebutuhan evaluasi.'],
                        ['clock', 'Riwayat status', 'Setiap transisi penting tersimpan bersama waktu dan pelakunya.'],
                    ] as [$icon, $title, $copy])
                        <article class="border-b border-r border-line p-7 sm:p-8">
                            <div class="grid h-11 w-11 place-items-center rounded-lg bg-accent-soft text-accent"><x-icon :name="$icon" size="20" /></div>
                            <h3 class="mt-7 text-lg font-bold">{{ $title }}</h3>
                            <p class="mt-3 text-sm leading-6 text-muted">{{ $copy }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>

        <section id="kontak" class="bg-ink py-20 text-white sm:py-24">
            <div class="landing-container flex flex-col items-start justify-between gap-10 lg:flex-row lg:items-end">
                <div class="max-w-3xl"><p class="text-xs font-bold uppercase tracking-[0.18em] text-white/50">Mulai bekerja lebih teratur</p><h2 class="mt-5 text-4xl font-semibold leading-tight tracking-[-0.045em] sm:text-6xl">Dukungan TI yang mudah diikuti, dari awal sampai selesai.</h2></div>
                <div class="flex shrink-0 flex-wrap gap-3">
                    @auth
                        <a class="btn !border-white !bg-white !text-ink hover:!bg-accent-soft" href="{{ route('tickets.create') }}">Buat tiket <x-icon name="arrow" size="17" /></a>
                    @else
                        <a class="btn !border-white !bg-white !text-ink hover:!bg-accent-soft" href="{{ route('login') }}">Masuk sekarang <x-icon name="arrow" size="17" /></a>
                    @endauth
                </div>
            </div>
        </section>
    </main>

    <footer class="border-t border-white/10 bg-ink py-8 text-white">
        <div class="landing-container flex flex-col justify-between gap-5 sm:flex-row sm:items-center">
            <x-brand-mark :inverse="true" />
            <p class="text-xs text-white/45">&copy; {{ date('Y') }} IT Helpdesk. Ruang kerja dukungan internal.</p>
        </div>
    </footer>
</body>
</html>
