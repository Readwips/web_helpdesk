<x-guest-layout>
    <div class="mb-8">
        <p class="eyebrow">Pemulihan akun</p>
        <h1 class="mt-3 text-3xl font-bold tracking-[-0.04em]">Atur ulang kata sandi</h1>
        <p class="mt-2 text-sm leading-6 text-muted">Masukkan alamat email akun Anda. Kami akan mengirimkan tautan untuk membuat kata sandi baru.</p>
    </div>
    <x-auth-session-status class="mb-5" :status="session('status')" />
    <form method="POST" action="{{ route('password.email') }}" class="space-y-5">
        @csrf
        <div>
            <x-input-label for="email" value="Alamat email" />
            <x-text-input id="email" class="block w-full" type="email" name="email" :value="old('email')" required autofocus placeholder="nama@perusahaan.co.id" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>
        <x-primary-button class="w-full">Kirim tautan pemulihan</x-primary-button>
    </form>
    <p class="mt-7 border-t border-line pt-6 text-center text-sm"><a href="{{ route('login') }}" class="font-semibold text-muted hover:text-ink">Kembali ke halaman masuk</a></p>
</x-guest-layout>
