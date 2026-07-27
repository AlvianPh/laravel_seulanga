<div x-data="{ actionUrl: '', itemName: '', isSoftDelete: false, customMethod: 'DELETE' }"
     @open-delete-modal.window="
        actionUrl = $event.detail.url;
        itemName = $event.detail.name;
        isSoftDelete = $event.detail.softDelete || false;
        customMethod = $event.detail.method || 'DELETE';
        $dispatch('open-modal', 'confirm-delete');
     ">
     
    <x-modal name="confirm-delete" focusable>
        <div class="p-6">
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100 flex items-center gap-2">
                <svg class="w-6 h-6 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                Konfirmasi Hapus Data
            </h2>

            <p class="mt-4 text-sm text-gray-600 dark:text-gray-400">
                Apakah Anda yakin ingin menghapus <span class="font-bold text-gray-900 dark:text-gray-100" x-text="itemName"></span>?
            </p>
            
            <p x-show="!isSoftDelete" x-cloak class="mt-4 text-sm text-red-600 dark:text-red-400 font-medium bg-red-50 dark:bg-red-900/20 p-3 rounded-lg border border-red-100 dark:border-red-800">
                Peringatan: Aksi ini tidak dapat dibatalkan (data akan dihapus permanen).
            </p>
            
            <p x-show="isSoftDelete" x-cloak class="mt-4 text-sm text-yellow-600 dark:text-yellow-400 font-medium bg-yellow-50 dark:bg-yellow-900/20 p-3 rounded-lg border border-yellow-100 dark:border-yellow-800">
                Peringatan: Data ini hanya akan disembunyikan (soft delete) dan tidak dihapus permanen dari database.
            </p>

            <div class="mt-6 flex justify-end">
                <x-secondary-button x-on:click="$dispatch('close-modal', 'confirm-delete')">
                    Batal
                </x-secondary-button>

                <form x-bind:action="actionUrl" method="POST" class="ml-3 inline">
                    @csrf
                    <input type="hidden" name="_method" x-bind:value="customMethod">
                    <x-danger-button type="submit">
                        Ya, Hapus
                    </x-danger-button>
                </form>
            </div>
        </div>
    </x-modal>
</div>
