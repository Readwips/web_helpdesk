<x-guest-layout>
    <div class="mb-8">
        <p class="eyebrow">Selamat datang kembali</p>
        <h1 class="mt-3 text-3xl font-bold tracking-[-0.04em]">Masuk ke IT Helpdesk</h1>
        <p class="mt-2 text-sm leading-6 text-muted">Gunakan akun perusahaan untuk mengakses ruang kerja dukungan Anda.</p>
    </div>

    <x-auth-session-status class="mb-5" :status="session('status')" />

    <form method="POST" action="{{ route('login') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="email" value="Alamat email" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus autocomplete="username" placeholder="nama@perusahaan.co.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <div class="flex items-center justify-between gap-3">
                <x-input-label for="password" value="Kata sandi" />
                @if(Route::has('password.request'))
                    <a class="text-xs font-semibold text-accent hover:text-accent-dark" href="{{ route('password.request') }}">Lupa kata sandi?</a>
                @endif
            </div>
            <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="current-password" placeholder="Masukkan kata sandi" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <label for="remember_me" class="flex cursor-pointer items-center gap-2.5 text-sm text-muted">
            <input id="remember_me" type="checkbox" class="rounded border-line text-accent shadow-none focus:ring-accent" name="remember">
            <span>Ingat saya di perangkat ini</span>
        </label>
        <x-primary-button class="w-full">Masuk ke sistem <x-icon name="arrow" size="17" /></x-primary-button>
    </form>

    @if(Route::has('register'))
        <p class="mt-7 border-t border-line pt-6 text-center text-sm text-muted">Belum memiliki akun? <a href="{{ route('register') }}" class="font-semibold text-ink hover:text-accent">Daftar di sini</a></p>
    @endif
</x-guest-layout>
