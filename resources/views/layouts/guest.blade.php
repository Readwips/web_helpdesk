<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'IT Helpdesk') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas font-sans text-ink antialiased">
    <main class="min-h-screen lg:grid lg:grid-cols-[minmax(22rem,0.9fr)_minmax(32rem,1.1fr)]">
        <section class="relative hidden overflow-hidden bg-ink p-10 text-white lg:flex lg:flex-col lg:justify-between xl:p-16" aria-label="Tentang IT Helpdesk">
            <x-brand-mark href="{{ url('/') }}" :inverse="true" />

            <div class="relative z-10 max-w-lg">
                <p class="mb-6 text-xs font-bold uppercase tracking-[0.2em] text-white/55">Dukungan yang terukur</p>
                <h1 class="text-5xl font-semibold leading-[1.02] tracking-[-0.045em] xl:text-6xl">Satu tempat untuk setiap kebutuhan TI.</h1>
                <p class="mt-6 max-w-md text-base leading-7 text-white/65">Buat tiket, ikuti progres, dan temukan solusi dengan alur yang jelas dari awal hingga selesai.</p>
            </div>

            <div class="relative z-10 grid grid-cols-3 gap-px overflow-hidden rounded-xl border border-white/15 bg-white/15">
                @foreach([['01', 'Laporkan'], ['02', 'Ditangani'], ['03', 'Konfirmasi']] as [$number, $label])
                    <div class="bg-ink p-4">
                        <span class="block text-xs text-white/40">{{ $number }}</span>
                        <span class="mt-3 block text-sm font-semibold">{{ $label }}</span>
                    </div>
                @endforeach
            </div>

            <div class="absolute -bottom-32 -right-24 h-96 w-96 rounded-full bg-accent/25 blur-3xl"></div>
        </section>

        <section class="flex min-h-screen items-center justify-center px-4 py-10 sm:px-8 lg:px-12">
            <div class="w-full max-w-md">
                <div class="mb-8 flex items-center justify-between lg:hidden">
                    <x-brand-mark href="{{ url('/') }}" />
                    <a href="{{ url('/') }}" class="text-sm font-semibold text-muted hover:text-ink">Beranda</a>
                </div>

                <div class="auth-card">
                    {{ $slot }}
                </div>

                <p class="mt-6 text-center text-xs leading-5 text-muted">Akses aman untuk layanan dukungan internal perusahaan.</p>
            </div>
        </section>
    </main>
</body>
</html>
