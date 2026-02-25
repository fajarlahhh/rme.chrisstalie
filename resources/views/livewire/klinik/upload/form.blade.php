<div x-ref="alpineRoot">
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Upload')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item">Upload</li>
        <li class="breadcrumb-item active">{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Upload <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                @if ($data->exists)
                    <div class="mb-3">
                        <label class="form-label">Data Registrasi</label>
                        <input type="text" class="form-control"
                            value="{{ $data->id }} - {{ $data->pasien->nama }} - {{ $data->pasien->alamat }}"
                            disabled />
                    </div>
                @else
                    <div class="mb-3">
                        <label class="form-label">Data Registrasi</label>
                        <div wire:ignore>
                            <select class="form-control" x-init="$($el).select2({
                                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                                dropdownAutoWidth: true,
                                templateResult: format,
                                placeholder: 'Ketik No. Registrasi/Nama/No. KTP/No. RM',
                                minimumInputLength: 3,
                                dataType: 'json',
                                ajax: {
                                    url: '/cari/registrasi',
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
                                $wire.set('registrasi_id', $($el).val());
                            });
                            
                            function format(data) {
                                if (!data.id) {
                                    return data.text;
                                }
                                var $data = $('<table><tr><th>No. Registrasi</th><th>:</th><th>' + data.id + '</th></tr>' +
                                    '<tr><th>No. RM</th><th>:</th><th>' + data.rm + '</th></tr>' +
                                    '<tr><th>Nama</th><th>:</th><th>' + data.nama + '</th></tr>' +
                                    '<tr><th>Alamat</th><th>:</th><th>' + data.alamat + '</th></tr></table>');
                                return $data;
                            }">
                                @if ($registrasi_id)
                                    <option value="{{ $registrasi_id }}" selected>
                                        {{ $data->id . ' - ' . $data->pasien->nama . ', ' . $data->pasien->alamat }}
                                    </option>
                                @endif
                            </select>
                        </div>
                    </div>
                @endif

                <x-upload :fileDiupload="$fileDiupload" :fileDihapus="$fileDihapus" />
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
                <button type="button" onclick="window.location.href='/klinik/upload'" class="btn btn-danger"
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
