<section>
    <form method="POST" action="{{ route('password.update') }}" class="max-w-2xl space-y-5">
        @csrf @method('PUT')
        <div><x-input-label for="update_password_current_password" value="Kata sandi saat ini" /><x-text-input id="update_password_current_password" name="current_password" type="password" class="block w-full" autocomplete="current-password" /><x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" /></div>
        <div><x-input-label for="update_password_password" value="Kata sandi baru" /><x-text-input id="update_password_password" name="password" type="password" class="block w-full" autocomplete="new-password" /><x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" /></div>
        <div><x-input-label for="update_password_password_confirmation" value="Ulangi kata sandi baru" /><x-text-input id="update_password_password_confirmation" name="password_confirmation" type="password" class="block w-full" autocomplete="new-password" /><x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" /></div>
        <div class="flex items-center gap-4"><x-primary-button>Simpan kata sandi</x-primary-button>@if(session('status') === 'password-updated')<p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2500)" class="text-sm font-semibold text-emerald-700">Kata sandi diperbarui.</p>@endif</div>
    </form>
</section>
