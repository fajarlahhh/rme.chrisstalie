<div>
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Nakes')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengaturan</li>
        <li class="breadcrumb-item">Nakes</li>
        <li class="breadcrumb-item active">{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Nakes <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    <x-alert />

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
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
                        wire:model.lazy="pegawai_id" data-width="100%">
                        <option selected value="">-- Bukan Pegawai --</option>
                        @foreach ($dataPegawai as $item)
                            <option value="{{ $item['id'] }}" data-subtext="{{ $item['nik'] }}">
                                {{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('pegawai_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">No. KTP</label>
                    <input class="form-control" type="number" step="1" maxlength="16" minlength="16"
                        wire:model="nik" @if ($pegawai_id) disabled @endif />
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
                        @if ($pegawai_id) disabled @endif />
                    @error('nama')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Alamat</label>
                    <input class="form-control" type="text" wire:model="alamat"
                        @if ($pegawai_id) disabled @endif />
                    @error('alamat')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">No. Hp</label>
                    <input class="form-control" type="text" wire:model="no_hp"
                        @if ($pegawai_id) disabled @endif />
                    @error('no_hp')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" id="dokter" value="1" wire:model="dokter" />
                    <label class="form-check-label" for="dokter">
                        Dokter
                    </label>
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <button type="button" onclick="window.location.href='/pengaturan/nakes'" class="btn btn-danger"
                    wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
            </div>
        </form>
    </div>
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
