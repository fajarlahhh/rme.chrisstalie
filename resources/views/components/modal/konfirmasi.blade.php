<div>
    <div wire:ignore.self class="modal fade" id="modal-konfirmasi">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <h3>Apakah Anda yakin ingin melanjutkan?</h3>
                    <input type="submit" class="btn btn-primary" x-init="$($el).on('click', function() {
                        $('#modal-konfirmasi').modal('hide');
                    })" value="Ya">
                    <input type="button" class="btn btn-danger" x-init="$($el).on('click', function() {
                        $('#modal-konfirmasi').modal('hide');
                    })" value="Tidak">
                </div>
            </div>
        </div>
    </div>
</div>
