<x-guest-layout>
    <div class="mb-8"><p class="eyebrow">Area aman</p><h1 class="mt-3 text-3xl font-bold tracking-[-0.04em]">Konfirmasi kata sandi</h1><p class="mt-2 text-sm leading-6 text-muted">Masukkan kembali kata sandi Anda untuk melanjutkan tindakan sensitif ini.</p></div>
    <form method="POST" action="{{ route('password.confirm') }}" class="space-y-5">
        @csrf
        <div><x-input-label for="password" value="Kata sandi" /><x-text-input id="password" class="block w-full" type="password" name="password" required autocomplete="current-password" /><x-input-error :messages="$errors->get('password')" class="mt-2" /></div>
        <x-primary-button class="w-full">Konfirmasi dan lanjutkan</x-primary-button>
    </form>
</x-guest-layout>
