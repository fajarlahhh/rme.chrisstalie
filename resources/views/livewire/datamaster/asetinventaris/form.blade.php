<div>
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Aset/Inventaris')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item">Aset/Inventaris</li>
        <li class="breadcrumb-item active">{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Aset/Inventaris <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Unit Bisnis</label>
                    <select class="form-control" wire:model.live="unit_bisnis" data-width="100%">
                        <option hidden selected>-- Pilih Unit Bisnis --</option>
                        @foreach (\App\Enums\UnitBisnisEnum::cases() as $item)
                            <option value="{{ $item->value }}">{{ $item->label() }}</option>
                        @endforeach
                    </select>
                    @error('unit_bisnis')
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
                    <label class="form-label">Detail</label>
                    <textarea class="form-control" wire:model="detail" rows="3"></textarea>
                    @error('detail')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Satuan</label>
                    <input class="form-control" type="text" wire:model="satuan" />
                    @error('satuan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Perolehan</label>
                    <input class="form-control" @if ($data->exists) disabled @endif type="date"
                        wire:model="tanggal_perolehan" @if ($data->exists) disabled @endif />
                    @error('tanggal_perolehan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select class="form-control" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    })" wire:model.live="kode_akun_id"
                        @if ($data->exists) disabled @endif data-width="100%">
                        <option hidden selected>-- Pilih Kode Akun --</option>
                        @foreach ($dataKodeAkun as $item)
                            <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('kode_akun_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Sumber Dana</label>
                    <select class="form-control" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    })" wire:model.live="kode_akun_sumber_dana_id"
                        @if ($data->exists) disabled @endif data-width="100%">
                        <option hidden selected>-- Pilih Kode Akun --</option>
                        @foreach ($dataKodeAkunSumberDana as $item)
                            <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('kode_akun_sumber_dana_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Metode Penyusutan</label>
                    <select class="form-control" @if ($data->exists) disabled @endif
                        wire:model.live="metode_penyusutan" data-width="100%">
                        <option hidden selected>-- Pilih Metode Penyusutan --</option>
                        <option value="Garis Lurus">Garis Lurus</option>
                        <option value="Satuan Hasil Produksi">Satuan Hasil Produksi</option>
                    </select>
                    @error('metode_penyusutan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga Perolehan</label>
                    <input class="form-control" type="text" wire:model="harga_perolehan"
                        @if ($data->exists) disabled @endif />
                    @error('harga_perolehan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">
                        @if ($metode_penyusutan == 'Garis Lurus')
                            Masa Manfaat <small>(bulan)</small>
                        @else
                            Pemakaian <small>(x)</small>
                        @endif
                    </label>
                    <input class="form-control" type="text" wire:model="masa_manfaat"
                        @if ($data->exists) disabled @endif />
                    @error('masa_manfaat')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Lokasi</label>
                    <input class="form-control" type="text" wire:model="lokasi" />
                    @error('lokasi')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                @if ($data->exists)
                    <div class="mb-3">
                        <label class="form-label">Status</label>
                        <select class="form-control" wire:model="status" data-width="100%">
                            <option hidden selected>-- Pilih Status --</option>
                            <option value="Aktif">Aktif</option>
                            <option value="Tidak Aktif">Tidak Aktif</option>
                        </select>
                        @error('status')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <a href="{{ $previous }}" class="btn btn-danger" wire:ignore wire:loading.remove>Batal</a>
            </div>
        </form>
    </div>

    <x-alert />

</div>
