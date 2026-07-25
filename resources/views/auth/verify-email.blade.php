<x-guest-layout>
    <div class="mb-8"><p class="eyebrow">Verifikasi email</p><h1 class="mt-3 text-3xl font-bold tracking-[-0.04em]">Periksa kotak masuk Anda</h1><p class="mt-2 text-sm leading-6 text-muted">Kami telah mengirimkan tautan verifikasi. Buka tautan tersebut agar akun Anda dapat digunakan.</p></div>
    @if(session('status') === 'verification-link-sent')<x-alert type="success">Tautan verifikasi baru telah dikirim ke alamat email Anda.</x-alert>@endif
    <div class="flex flex-col gap-3 sm:flex-row">
        <form method="POST" action="{{ route('verification.send') }}" class="flex-1">@csrf<x-primary-button class="w-full">Kirim ulang tautan</x-primary-button></form>
        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="btn-secondary w-full">Keluar</button></form>
    </div>
</x-guest-layout>
