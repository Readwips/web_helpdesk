<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'IT Helpdesk') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-canvas text-ink antialiased" x-data="{ menu: false }" @keydown.escape.window="menu = false">
    <div class="min-h-screen lg:flex">
        @include('layouts.navigation')

        <div class="min-w-0 flex-1 lg:pl-[264px]">
            <header class="sticky top-0 z-20 flex h-[4.5rem] items-center justify-between border-b border-line bg-white/95 px-4 backdrop-blur lg:px-8">
                <div class="flex min-w-0 items-center gap-3">
                    <button type="button" class="icon-button shrink-0 lg:hidden" @click="menu = true" aria-label="Buka menu navigasi">
                        <x-icon name="menu" class="h-5 w-5" />
                    </button>
                    <div class="min-w-0">
                        <p class="eyebrow">Pusat layanan TI</p>
                        <p class="truncate text-sm font-semibold text-ink sm:text-base">{{ $header ?? 'Helpdesk & Manajemen Aset' }}</p>
                    </div>
                </div>

                @php
                    $initials = collect(explode(' ', auth()->user()->name))->filter()->take(2)->map(fn ($part) => mb_strtoupper(mb_substr($part, 0, 1)))->join('');
                @endphp
                <a href="{{ route('profile.edit') }}" class="group flex items-center gap-3 rounded-lg p-1.5 transition hover:bg-canvas" aria-label="Buka profil">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-accent-soft text-xs font-bold text-accent-dark">{{ $initials }}</span>
                    <span class="hidden text-left sm:block">
                        <span class="block max-w-40 truncate text-sm font-semibold text-ink">{{ auth()->user()->name }}</span>
                        <span class="block text-xs capitalize text-muted">{{ auth()->user()->role }}</span>
                    </span>
                </a>
            </header>

            <main id="main-content" class="mx-auto w-full max-w-[96rem] p-4 sm:p-6 lg:p-8">
                @if(session('success'))
                    <x-alert type="success" class="mb-6">{{ session('success') }}</x-alert>
                @endif

                @if($errors->any())
                    <x-alert type="error" class="mb-6">
                        <p class="mb-1 font-semibold">Periksa kembali data berikut.</p>
                        <ul class="list-disc space-y-1 pl-5">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </x-alert>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>

    @stack('scripts')
</body>
</html>
