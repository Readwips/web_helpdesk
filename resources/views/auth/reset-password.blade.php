<x-guest-layout>
    <div class="mb-8"><p class="eyebrow">Keamanan akun</p><h1 class="mt-3 text-3xl font-bold tracking-[-0.04em]">Buat kata sandi baru</h1><p class="mt-2 text-sm leading-6 text-muted">Gunakan kata sandi yang kuat dan tidak digunakan pada layanan lain.</p></div>
    <form method="POST" action="{{ route('password.store') }}" class="space-y-5">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">
        <div><x-input-label for="email" value="Alamat email" /><x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email', $request->email)" required autofocus autocomplete="username" /><x-input-error :messages="$errors->get('email')" class="mt-2" /></div>
        <div><x-input-label for="password" value="Kata sandi baru" /><x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password')" class="mt-2" /></div>
        <div><x-input-label for="password_confirmation" value="Ulangi kata sandi" /><x-text-input id="password_confirmation" class="block w-full" type="password" name="password_confirmation" required autocomplete="new-password" /><x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" /></div>
        <x-primary-button class="w-full">Simpan kata sandi baru</x-primary-button>
    </form>
</x-guest-layout>
