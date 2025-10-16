<div>
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Pasien')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item">Pasien</li>
        <li class="breadcrumb-item active">{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Pasien <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    <x-alert />

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">RM</label>
                    <input class="form-control" type="text" wire:model="rm" disabled />
                    @error('rm')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">NIK</label>
                    <input class="form-control" type="number" step="1" maxlength="16" minlength="16"
                        wire:model="nik" />
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
                    <input class="form-control" type="text" wire:model="nama" />
                    @error('nama')
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
                    <select data-container="body" class="form-control " wire:model="jenis_kelamin" data-width="100%">
                        <option selected hidden>-- Tidak Ada Jenis Kelamin --</option>
                        <option value="Laki-laki">Laki-laki</option>
                        <option value="Perempuan">Perempuan</option>
                    </select>
                    @error('jenis_kelamin')
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
                    <label class="form-label">No. Telp.</label>
                    <input class="form-control" type="text" wire:model="no_hp" />
                    @error('no_hp')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Daftar</label>
                    <input class="form-control" type="text" disabled value="{{ $data->tanggal_daftar }}" />
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <a href="{{ $previous }}" class="btn btn-danger" wire:ignore>Batal</a>
                <x-alert />
            </div>
        </form>
    </div>
</div>
