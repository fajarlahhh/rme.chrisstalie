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
                <x-alert />
                <div class="mb-3">
                    <label class="form-label">Persediaan</label>
                    <select class="form-control" wire:model.live="persediaan" data-width="100%"
                        @if ($data->exists) disabled @endif>
                        <option hidden selected>-- Pilih Persediaan --</option>
                        <option value="Apotek">Apotek</option>
                        <option value="Klinik">Klinik</option>
                    </select>
                    @error('persediaan')
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
                @if (!$data->exists)
                    <div class="mb-3">
                        <label class="form-label">Harga Jual</label>
                        <input class="form-control" type="text" wire:model="harga"
                            @if ($data->exists) disabled @endif />
                        @error('harga')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
                <div class="mb-3">
                    <label class="form-label">Kategori</label>
                    <select class="form-control" @if ($data->exists) disabled @endif
                        x-init="$($el).selectpicker({
                            liveSearch: true,
                            width: 'auto',
                            size: 10,
                            container: 'body',
                            style: '',
                            showSubtext: true,
                            styleBase: 'form-control'
                        })" wire:model.live="kode_akun_id" data-width="100%">
                        <option hidden selected>-- Tidak Ada Kode Akun --</option>
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
                    <select class="form-control" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    })" wire:model.live="kode_akun_penjualan_id"
                        data-width="100%">
                        <option hidden selected>-- Tidak Ada Kode Akun --</option>
                        @foreach ($dataKodeAkunPenjualan as $item)
                            <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('kode_akun_penjualan_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Kategori Modal</label>
                    <select class="form-control" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    })" wire:model.live="kode_akun_modal_id"
                        data-width="100%">
                        <option hidden selected>-- Tidak Ada Kode Akun --</option>
                        @foreach ($dataKodeAkunModal as $item)
                            <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('kode_akun_modal_id')
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
                @if ($persediaan == 'Apotek')
                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" wire:model="perlu_resep"
                            @if ($perlu_resep) checked @endif />
                        <label class="form-check-label" for="perlu_resep">
                            Perlu Resep
                        </label>
                    </div>
                @endif
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" wire:model="khusus"
                        @if ($khusus) checked @endif />
                    <label class="form-check-label" for="khusus">
                        Khusus
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
                <button type="button" onclick="window.location.href='/datamaster/barangdagang'" class="btn btn-danger"
                    wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
                <x-alert />
            </div>
        </form>
    </div>

    <div wire:loading>
        <x-loading />
    </div>
</div>
