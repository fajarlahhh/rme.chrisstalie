<div>
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Pegawai')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item">Pegawai</li>
        <li class="breadcrumb-item active">{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Pegawai <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Kantor</label>
                            <select class="form-control" wire:model="kantor" data-width="100%">
                                <option hidden selected>-- Pilih Kantor --</option>
                                @foreach (\App\Enums\KantorEnum::cases() as $item)
                                    <option value="{{ $item->value }}">{{ $item->label() }}</option>
                                @endforeach
                            </select>
                            @error('kantor')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. KTP</label>
                            <input class="form-control" type="number" step="1" maxlength="16" minlength="16"
                                wire:model="nik" @if ($status == 'Non Aktif') disabled @endif />
                            @error('nik')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input class="form-control" type="text" wire:model="nama"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <input class="form-control" type="text" wire:model="alamat"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('alamat')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Hp</label>
                            <input class="form-control" type="text" wire:model="no_hp"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('no_hp')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select data-container="body" class="form-control " wire:model="jenis_kelamin"
                                data-width="100%" @if ($status == 'Non Aktif') disabled @endif>
                                <option selected>-- Pilih Jenis Kelamin --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input class="form-control" type="date" wire:model="tanggal_lahir"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('tanggal_lahir')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input class="form-control" type="date" wire:model="tanggal_masuk"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('tanggal_masuk')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NPWP</label>
                            <input class="form-control" type="text" wire:model="npwp"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('npwp')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. BPJS Kesehatan</label>
                            <input class="form-control" type="text" wire:model="no_bpjs"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('no_bpjs')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Satuan Tugas</label>
                            <input class="form-control" type="text" wire:model="satuan_tugas"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('satuan_tugas')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" wire:model="status"
                                @if ($status == 'Aktif') checked @endif />
                            <label class="form-check-label" for="status">
                                Aktif
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="note alert-secondary mb-0">
                            <div class="note-content">
                                <h4>Gaji & Tunjangan</h4>
                                <hr>
                                <div class="mb-3">
                                    <label class="form-label">Gaji Pokok</label>
                                    <input class="form-control" type="number" step="1" min="0"
                                        wire:model="gaji" @if ($status == 'Non Aktif') disabled @endif />
                                    @error('gaji')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Tunjangan</label>
                                    <input class="form-control" type="number" step="1" min="0"
                                        wire:model="tunjangan" @if ($status == 'Non Aktif') disabled @endif />
                                    @error('tunjangan')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Uang Makan</label>
                                    <input class="form-control" type="number" step="1" min="0"
                                        wire:model="tunjangan_transport"
                                        @if ($status == 'Non Aktif') disabled @endif />
                                    @error('tunjangan_transport')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">BPJS Kesehatan</label>
                                    <input class="form-control" type="number" step="1" min="0"
                                        wire:model="tunjangan_bpjs"
                                        @if ($status == 'Non Aktif') disabled @endif />
                                    @error('tunjangan_bpjs')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <input type="submit" value="Simpan" class="btn btn-success" />
                @endrole
                <a href="{{ $previous }}" class="btn btn-danger" wire:ignore>Batal</a>
            </div>
        </form>
    </div>

    <x-alert />

</div>
