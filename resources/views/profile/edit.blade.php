<x-app-layout>
    <x-slot:header>Profil Saya</x-slot:header>

    <x-page-header eyebrow="Pengaturan akun" title="Profil saya" description="Kelola informasi dasar dan keamanan akun Anda." />

    <div class="grid gap-6 xl:grid-cols-[minmax(0,1fr)_22rem]">
        <div class="space-y-6">
            <x-panel title="Informasi profil" description="Nama dan alamat email yang digunakan pada sistem.">
                @include('profile.partials.update-profile-information-form')
            </x-panel>
            <x-panel title="Perbarui kata sandi" description="Gunakan kata sandi yang kuat dan unik untuk menjaga keamanan akun.">
                @include('profile.partials.update-password-form')
            </x-panel>
        </div>
        <div class="space-y-6">
            <x-panel title="Ringkasan akun">
                <dl class="space-y-4 text-sm"><div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Peran</dt><dd class="mt-1 font-semibold capitalize">{{ $user->role }}</dd></div><div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Departemen</dt><dd class="mt-1 font-semibold">{{ $user->department?->name ?? 'Belum ditentukan' }}</dd></div><div><dt class="text-xs font-semibold uppercase tracking-wider text-muted">Status akun</dt><dd class="mt-1"><x-badge :value="$user->status" /></dd></div></dl>
            </x-panel>
            <x-panel title="Hapus akun" description="Tindakan ini bersifat permanen dan tidak dapat dibatalkan.">
                @include('profile.partials.delete-user-form')
            </x-panel>
        </div>
    </div>
</x-app-layout>
