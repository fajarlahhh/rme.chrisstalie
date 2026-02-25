<div>
    @section('title', 'Registrasi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Member</li>
        <li class="breadcrumb-item active">Registrasi</li>
    @endsection

    <h1 class="page-header">Registrasi <small>Member</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <ul class="nav nav-tabs bg-gray-100">
                    <li class="nav-item">
                        <a href="#default-tab-1" data-bs-toggle="tab" class="nav-link active" wire:click="resetPasien"
                            wire:ignore.self>
                            <span class="d-sm-none">Pasien Baru</span>
                            <span class="d-sm-block d-none">Pasien Baru</span>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="#default-tab-2" data-bs-toggle="tab" class="nav-link" wire:click="resetPasien"
                            wire:ignore.self>
                            <span class="d-sm-none">Pasien Lama</span>
                            <span class="d-sm-block d-none">Pasien Lama</span>
                        </a>
                    </li>
                </ul>
                <!-- END nav-tabs -->
                <!-- BEGIN tab-content -->
                <div class="tab-content panel rounded-0 p-3 m-0 bg-gray-100">
                    <!-- BEGIN tab-pane -->
                    <div class="tab-pane fade active show" id="default-tab-1" wire:ignore.self>
                        <h4 class="mt-10px">Data Pasien</h4>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">No. KTP</label>
                            <input class="form-control" type="text" wire:model="nik" />
                            @error('nik')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input class="form-control" type="text" wire:model="nama" />
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <input class="form-control" type="text" wire:model="alamat" />
                            @error('alamat')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input class="form-control" type="date" wire:model="tanggal_lahir" />
                            @error('tanggal_lahir')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select data-container="body" class="form-control " wire:model="jenis_kelamin"
                                data-width="100%">
                                <option selected hidden>-- Tidak Ada Jenis Kelamin --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telpon</label>
                            <input class="form-control" type="text" wire:model="no_hp" />
                            @error('no_hp')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" wire:model="email" />
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- END tab-pane -->
                    <!-- BEGIN tab-pane -->
                    <div class="tab-pane fade" id="default-tab-2" wire:ignore.self>
                        <h4 class="mt-10px">Data Pasien</h4>
                        <hr>
                        @if (!$pasien_id)
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
                                        $wire.set('pasien_id', $($el).val());
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
                        @endif
                        <div class="mb-3">
                            <label class="form-label">No. RM</label>
                            <input class="form-control" type="text" wire:model="rm"
                                @if ($nik) disabled @endif
                                @if (!$pasien_id) disabled @endif />
                            @error('rm')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input class="form-control" type="text" wire:model="nama"
                                @if ($nama) disabled @endif
                                @if (!$pasien_id) disabled @endif />
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. KTP</label>
                            <input class="form-control" type="text" wire:model="nik"
                                @if ($nik) disabled @endif
                                @if (!$pasien_id) disabled @endif />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <input class="form-control" type="text" wire:model="alamat"
                                @if ($alamat) disabled @endif
                                @if (!$pasien_id) disabled @endif />
                            @error('alamat')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input class="form-control" type="date" wire:model="tanggal_lahir"
                                @if ($tanggal_lahir) disabled @endif
                                @if (!$pasien_id) disabled @endif />
                            @error('tanggal_lahir')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <input class="form-control" type="text" wire:model="jenis_kelamin"
                                @if ($jenis_kelamin) disabled @endif
                                @if (!$pasien_id) disabled @endif />
                            @error('jenis_kelamin')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Telpon</label>
                            <input class="form-control" type="text" wire:model="no_hp"
                                @if ($no_hp) disabled @endif
                                @if (!$pasien_id) disabled @endif />
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input class="form-control" type="email" wire:model="email" />
                            @error('email')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <!-- END tab-pane -->
                </div>
                <!-- END tab-content -->
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
                <button type="button" class="btn btn-secondary m-r-3" wire:loading.attr="disabled"
                    onclick="window.location.href='/member/registrasi/data'">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Data
                </button>
                <button type="button" class="btn btn-warning m-r-3"
                    onclick="window.location.href='/member/registrasi'" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Reset
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
