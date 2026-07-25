<section>
    <form id="send-verification" method="POST" action="{{ route('verification.send') }}">@csrf</form>
    <form method="POST" action="{{ route('profile.update') }}" class="max-w-2xl space-y-5">
        @csrf @method('PATCH')
        <div><x-input-label for="name" value="Nama lengkap" /><x-text-input id="name" name="name" type="text" class="block w-full" :value="old('name', $user->name)" required autofocus autocomplete="name" /><x-input-error class="mt-2" :messages="$errors->get('name')" /></div>
        <div>
            <x-input-label for="email" value="Alamat email" /><x-text-input id="email" name="email" type="email" class="block w-full" :value="old('email', $user->email)" required autocomplete="username" /><x-input-error class="mt-2" :messages="$errors->get('email')" />
            @if($user instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! $user->hasVerifiedEmail())
                <x-alert type="warning" class="mt-4">Alamat email belum diverifikasi. <button form="send-verification" class="font-bold underline underline-offset-2">Kirim ulang tautan verifikasi.</button></x-alert>
                @if(session('status') === 'verification-link-sent')<p class="mt-2 text-sm font-semibold text-emerald-700">Tautan verifikasi baru telah dikirim.</p>@endif
            @endif
        </div>
        <div class="flex items-center gap-4"><x-primary-button>Simpan profil</x-primary-button>@if(session('status') === 'profile-updated')<p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-semibold text-emerald-700">Perubahan tersimpan.</p>@endif</div>
    </form>
</section>
