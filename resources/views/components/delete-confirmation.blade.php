<dialog id="delete-confirmation" aria-labelledby="delete-confirmation-title" aria-describedby="delete-confirmation-message" class="delete-dialog card p-6">
    <form method="dialog">
        <div class="w-10 h-10 rounded-full bg-red-50 text-red-700 flex items-center justify-center mb-4" aria-hidden="true">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6 7h12m-10 0 1 13h6l1-13M9 7V4h6v3m-5 3v7m4-7v7"/></svg>
        </div>
        <h2 id="delete-confirmation-title" class="text-xl">Konfirmasi hapus</h2>
        <p id="delete-confirmation-message" data-confirm-message class="text-sm text-slate-600 mt-2"></p>
        <div class="flex justify-end gap-3 mt-6">
            <button type="submit" value="cancel" class="btn btn-outline" autofocus>Batal</button>
            <button type="submit" value="confirm" class="btn btn-destructive">Ya, hapus</button>
        </div>
    </form>
</dialog>
