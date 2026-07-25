<section>
    <p class="text-sm leading-6 text-muted">Setelah akun dihapus, seluruh data akun yang terkait tidak dapat dipulihkan. Pastikan data penting sudah diamankan.</p>
    <x-danger-button class="mt-5" x-data="" x-on:click.prevent="$dispatch('open-modal', 'confirm-user-deletion')">Hapus akun</x-danger-button>

    <x-modal name="confirm-user-deletion" :show="$errors->userDeletion->isNotEmpty()" focusable>
        <form method="POST" action="{{ route('profile.destroy') }}" class="p-6 sm:p-7">
            @csrf @method('DELETE')
            <p class="eyebrow">Konfirmasi tindakan</p><h2 class="mt-3 text-xl font-bold tracking-[-0.02em] text-ink">Yakin ingin menghapus akun?</h2><p class="mt-2 text-sm leading-6 text-muted">Tindakan ini permanen. Masukkan kata sandi Anda untuk mengonfirmasi penghapusan akun.</p>
            <div class="mt-6"><x-input-label for="password" value="Kata sandi" /><x-text-input id="password" name="password" type="password" class="block w-full" placeholder="Masukkan kata sandi" /><x-input-error :messages="$errors->userDeletion->get('password')" class="mt-2" /></div>
            <div class="mt-6 flex flex-col-reverse gap-2 sm:flex-row sm:justify-end"><x-secondary-button x-on:click="$dispatch('close')">Batal</x-secondary-button><x-danger-button>Hapus akun permanen</x-danger-button></div>
        </form>
    </x-modal>
</section>
