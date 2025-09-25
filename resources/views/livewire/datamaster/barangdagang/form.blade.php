<div>
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Barang Dagang')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item">Barang Dagang</li>
        <li class="breadcrumb-item active">{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Barang Dagang <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

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
                    <label class="form-label">Satuan <small>(Satuan Terkecil)</small></label>
                    <input class="form-control" type="text" wire:model="satuan"
                        @if ($data->exists) disabled @endif />
                    @error('satuan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga</label>
                    <input class="form-control" type="text" wire:model="harga"
                        @if ($data->exists) disabled @endif />
                    @error('harga')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select class="form-control" wire:model.live="kode_akun_id" data-width="100%">
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
                    <label class="form-label">Kategori Penjualan</label>
                    <select class="form-control" wire:model.live="kode_akun_penjualan_id" data-width="100%">
                        <option hidden selected>-- Pilih Kode Akun --</option>
                        @foreach ($dataKodeAkunPenjualan as $item)
                            <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('kode_akun_penjualan_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">KFA</label>
                    <input class="form-control" type="text" wire:model="kfa" />
                    @error('kfa')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" wire:model="perlu_resep"
                        @if ($perlu_resep) checked @endif />
                    <label class="form-check-label" for="perlu_resep">
                        Perlu Resep
                    </label>
                </div>
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" wire:model="klinik"
                        @if ($klinik) checked @endif />
                    <label class="form-check-label" for="klinik">
                        Khusus Tarif Tindakan Klinik
                    </label>
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <a href="{{ $previous }}" class="btn btn-danger" wire:ignore wire:loading.remove>Batal</a>
            </div>
        </form>
    </div>

    <x-alert />

</div>
