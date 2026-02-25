<div>
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Nakes')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengaturan</li>
        <li class="breadcrumb-item">Nakes</li>
        <li class="breadcrumb-item active">{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Nakes <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="row">
                    <div class="col-6">
                        <div class="mb-3">
                            <label class="form-label">Pegawai</label>
                            <select data-container="body" class="form-control" x-init="$($el).selectpicker({
                                liveSearch: true,
                                width: 'auto',
                                size: 10,
                                container: 'body',
                                style: '',
                                showSubtext: true,
                                styleBase: 'form-control'
                            })"
                                wire:model.lazy="kepegawaian_pegawai_id" data-width="100%">
                                <option selected value="">-- Bukan Pegawai --</option>
                                @foreach ($dataPegawai as $item)
                                    <option value="{{ $item['id'] }}" data-subtext="{{ $item['nik'] }}">
                                        {{ $item['nama'] }}</option>
                                @endforeach
                            </select>
                            @error('kepegawaian_pegawai_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        @if (!$kepegawaian_pegawai_id)
                            <div class="mb-3">
                                <label class="form-label">No. KTP</label>
                                <input class="form-control" type="number" step="1" maxlength="16" minlength="16"
                                    wire:model="nik" @if ($kepegawaian_pegawai_id) disabled @endif />
                                @error('nik')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">IHS</label>
                                <input class="form-control" type="text" wire:model="ihs" disabled />
                                @error('ihs')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                <input class="form-control" type="text" wire:model="nama"
                                    @if ($kepegawaian_pegawai_id) disabled @endif />
                                @error('nama')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <input class="form-control" type="text" wire:model="alamat"
                                    @if ($kepegawaian_pegawai_id) disabled @endif />
                                @error('alamat')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. Hp</label>
                                <input class="form-control" type="text" wire:model="no_hp"
                                    @if ($kepegawaian_pegawai_id) disabled @endif />
                                @error('no_hp')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                    <div class="col-6">
                        <div class="alert alert-primary">
                            <h5>Kategori</h5>
                            <hr>
                            <div class="mb-3" x-data="{ dokter: $wire.dokter }">
                                <label class="form-label">Dokter</label>
                                <div class="input-group mb-10px">
                                    <div class="input-group-text">
                                        <input class="checkbox" type="checkbox" id="dokter" value="1"
                                            wire:model="dokter" x-model="dokter" />
                                    </div>
                                    <select class="form-control" wire:model="kode_akun_jasa_dokter_id"
                                        x-init="$($el).selectpicker({
                                            liveSearch: true,
                                            width: 'auto',
                                            size: 10,
                                            container: 'body',
                                            style: '',
                                            showSubtext: true,
                                            styleBase: 'form-control'
                                        })" data-width="100%" :disabled="!dokter">
                                        <option value="">Pilih Kode Akun Jasa Dokter</option>
                                        @foreach ($dataKodeAkun as $akun)
                                            <option value="{{ $akun['id'] }}">
                                                {{ $akun['id'] }} - {{ $akun['nama'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('kode_akun_jasa_dokter_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3" x-data="{ perawat: $wire.perawat }">
                                <label class="form-label">Perawat</label>
                                <div class="input-group mb-10px">
                                    <div class="input-group-text">
                                        <input class="checkbox" type="checkbox" id="perawat" value="1"
                                            wire:model="perawat" x-model="perawat" />
                                    </div>
                                    <select class="form-control" wire:model="kode_akun_jasa_perawat_id"
                                        x-init="$($el).selectpicker({
                                            liveSearch: true,
                                            width: 'auto',
                                            size: 10,
                                            container: 'body',
                                            style: '',
                                            showSubtext: true,
                                            styleBase: 'form-control'
                                        })" data-width="100%" :disabled="!perawat">
                                        <option value="">Pilih Kode Akun Jasa Perawat</option>
                                        @foreach ($dataKodeAkun as $akun)
                                            <option value="{{ $akun['id'] }}">
                                                {{ $akun['id'] }} - {{ $akun['nama'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('kode_akun_jasa_perawat_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                    </div>
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
                <button type="button" onclick="window.location.href='/pengaturan/nakes'" class="btn btn-danger"
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
