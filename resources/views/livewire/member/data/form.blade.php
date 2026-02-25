<div>
    @section('title', 'Registrasi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Member</li>
        <li class="breadcrumb-item">Data</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Member <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    @if ($data->exists)
        <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
            <!-- begin panel-heading -->
            <div class="panel-heading ui-sortable-handle">

                <h4 class="panel-title">Form</h4>
            </div>
            <form wire:submit.prevent="submit">
                <div class="panel-body">
                    <div class="mb-3">
                        <label class="form-label">ID</label>
                        <input class="form-control" type="text" value="{{ $data->id }}" disabled />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NIK</label>
                        <input class="form-control" type="number" step="1" maxlength="16" minlength="16"
                            value="{{ $data->pasien->nik }}" disabled />
                        @error('nik')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Nama</label>
                        <input class="form-control" type="text" value="{{ $data->pasien->nama }}" disabled />
                        @error('nama')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Lahir</label>
                        <input class="form-control" type="date" value="{{ $data->pasien->tanggal_lahir }}" disabled />
                        @error('tanggal_lahir')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Jenis Kelamin</label>
                        <input class="form-control" type="text" value="{{ $data->pasien->jenis_kelamin }}" disabled />                        
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Alamat</label>
                        <input class="form-control" type="text" value="{{ $data->pasien->alamat }}" disabled />
                        @error('alamat')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Telp.</label>
                        <input class="form-control" type="text" value="{{ $data->pasien->no_hp }}" disabled />
                        @error('no_hp')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal Daftar</label>
                        <input class="form-control" type="text" disabled value="{{ $data->created_at->format('d-m-Y') }}" />
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
    @else
        <form wire:submit.prevent="submit">
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
            <div class="tab-content panel rounded-0 p-3 m-0">
                <!-- BEGIN tab-pane -->
                <div class="tab-pane fade active show" id="default-tab-1" wire:ignore.self>
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
                    <hr>
                    @role('administrator|supervisor|operator')
                        <button type="button" x-init="$($el).on('click', function() {
                            $('#modal-konfirmasi').modal('show');
                        })" class="btn btn-success"
                            wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                            Submit
                        </button>
                    @endrole
                    <button type="button" class="btn btn-secondary m-r-3" wire:loading.attr="disabled"
                        onclick="window.location.href='/member/registrasi/data'">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Data
                    </button>
                    <x-alert />
                </div>
                <!-- END tab-pane -->
                <!-- BEGIN tab-pane -->
                <div class="tab-pane fade" id="default-tab-2" wire:ignore.self>
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
                    <hr>
                    @role('administrator|supervisor|operator')
                        <button type="button" x-init="$($el).on('click', function() {
                            $('#modal-konfirmasi').modal('show');
                        })" class="btn btn-success"
                            wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                            Submit
                        </button>
                    @endrole
                    <button type="button" class="btn btn-secondary m-r-3" wire:loading.attr="disabled"
                        onclick="window.location.href='/member/registrasi/data'">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Data
                    </button>
                    <x-alert />
                </div>
                <!-- END tab-pane -->
            </div>
            <!-- END tab-content -->
            <x-modal.konfirmasi />
        </form>
    @endif
    <div wire:loading>
        <x-loading />
    </div>
</div>
