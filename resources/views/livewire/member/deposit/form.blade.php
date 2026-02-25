<div>
    @section('title', 'Deposit')

    @section('breadcrumb')
        <li class="breadcrumb-item">Member</li>
        <li class="breadcrumb-item">Deposit</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Deposit <small>Tambah</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input class="form-control" type="date" wire:model="tanggal" max="{{ date('Y-m-d') }}" />
                    @error('tanggal')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Cari Pasien</label>
                    <div wire:ignore>
                        <select class="form-control" x-init="$($el).select2({
                            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                            dropdownAutoWidth: true,
                            templateResult: format,
                            minimumInputLength: 3,
                            dataType: 'json',
                            ajax: {
                                url: '/cari/pasien',
                                data: function(params) {
                                    var query = {
                                        cari: params.term
                                    }
                                    return query;
                                },
                                processResults: function(data, params) {
                                    return {
                                        results: data,
                                    };
                                },
                                cache: true
                            }
                        });
                        
                        $($el).on('change', function(element) {
                            $wire.set('member_id', $($el).val());
                        });
                        
                        function format(data) {
                            if (!data.id) {
                                return data.text;
                            }
                            var $data = $('<table><tr><th>No. RM</th><th>:</th><th>' + data.rm + '</th></tr>' +
                                '<tr><th>No. KTP</th><th>:</th><th>' + data.nik + '</th></tr>' +
                                '<tr><th>Nama</th><th>:</th><th>' + data.nama + '</th></tr>' +
                                '<tr><th>Alamat</th><th>:</th><th>' + data.alamat + '</th></tr></table>');
                            return $data;
                        }">
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Metode Bayar</label>
                    <select class="form-control" wire:model="metode_bayar" data-width="100%">
                        <option hidden>-- Pilih Metode Bayar --</option>
                        @foreach ($dataMetodeBayar as $item)
                            <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Jumlah</label>
                    <input class="form-control" type="number" wire:model="jumlah" />
                    @error('jumlah')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <input class="form-control" type="text" wire:model="catatan" />
                    @error('catatan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="button" x-init="$($el).on('click', function() {
                        $('#modal-konfirmasi').modal('show');
                    })" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Submit
                    </button>
                @endrole
                <button type="button" onclick="window.location.href='/datamaster/pasien'" class="btn btn-danger"
                    wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
                <x-alert />
            </div>

            <x-modal.konfirmasi />
        </form>
    </div>
    <div wire:loading>
        <x-loading />
    </div>
</div>
