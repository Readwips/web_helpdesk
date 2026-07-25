<x-guest-layout>
    <div class="mb-8">
        <p class="eyebrow">Akun baru</p>
        <h1 class="mt-3 text-3xl font-bold tracking-[-0.04em]">Bergabung ke IT Helpdesk</h1>
        <p class="mt-2 text-sm leading-6 text-muted">Daftarkan akun untuk mulai membuat dan mengikuti tiket dukungan.</p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="name" value="Nama lengkap" />
            <x-text-input id="name" class="block w-full" type="text" name="name" :value="old('name')" required autofocus autocomplete="name" placeholder="Nama Anda" />
            <x-input-error :messages="$errors->get('name')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="email" value="Alamat email" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autocomplete="username" placeholder="nama@perusahaan.co.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="password" value="Kata sandi" />
            <x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="new-password" placeholder="Minimal 8 karakter" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>
        <div>
            <x-input-label for="password_confirmation" value="Ulangi kata sandi" />
            <x-text-input id="password_confirmation" class="block w-full" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Masukkan kembali kata sandi" />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>
        <x-primary-button class="w-full">Buat akun <x-icon name="arrow" size="17" /></x-primary-button>
    </form>
    <p class="mt-7 border-t border-line pt-6 text-center text-sm text-muted">Sudah memiliki akun? <a href="{{ route('login') }}" class="font-semibold text-ink hover:text-accent">Masuk di sini</a></p>
</x-guest-layout>
