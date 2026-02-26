<div>
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Harga Jual')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item">Harga Jual</li>
        <li class="breadcrumb-item active">{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Harga Jual <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Barang</label>
                    <select class="form-control" @if ($data->exists) disabled @endif
                        wire:model.live="barang_id" x-init="$($el).selectpicker({
                            liveSearch: true,
                            width: 'auto',
                            size: 10,
                            container: 'body',
                            style: '',
                            showSubtext: true,
                            styleBase: 'form-control'
                        })" data-width="100%"
                        @if ($data->rasio_dari_terkecil == 1) disabled @endif>
                        <option hidden selected>-- Tidak Ada Barang --</option>
                        @foreach ($dataBarang as $item)
                            <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('jenis')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama Satuan</label>
                    <input class="form-control" type="text" wire:model="nama"
                        @if ($data->rasio_dari_terkecil == 1) disabled @endif />
                    @error('nama')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga Jual</label>
                    <input class="form-control" type="text" wire:model="harga_jual" />
                    @error('harga')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                @if ($data->rasio_dari_terkecil != 1)
                    <div class="mb-3" wire:loading.remove wire:target="barang_id">
                        <label class="form-label">Satuan Konversi</label>
                        <div class="input-group">
                            <select @if ($data->exists) disabled @endif class="form-control"
                                wire:model="satuan_konversi_id" data-width="100%" x-init="$($el).selectpicker({
                                    liveSearch: true,
                                    width: 'auto',
                                    size: 10,
                                    container: 'body',
                                    style: '',
                                    showSubtext: true,
                                    styleBase: 'form-control'
                                })"
                                @if ($data->exists) disabled @endif>
                                <option hidden selected>-- Tidak Ada Satuan Konversi --</option>
                                @foreach ($dataBarangSatuan as $item)
                                    <option data-subtext="Rp. {{ number_format($item['harga_jual'], 0, ',', '.') }}"
                                        value="{{ $item['id'] }}">
                                        {{ $item['nama'] }}</option>
                                @endforeach
                            </select>
                            <input class="form-control" type="text"
                                placeholder="Jumlah yang ada dalam satuan yg baru terhadap satuan konversi yang dipilih"
                                wire:model="faktor_konversi" @if ($data->exists) disabled @endif>
                        </div>
                        @error('faktor_konversi')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        @error('satuan_konversi')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
                <div class="form-check mb-3">
                    <input class="form-check-input" type="checkbox" wire:model="utama"
                        @if ($utama) checked disabled @endif />
                    <label class="form-check-label" for="utama">
                        Utama
                    </label>
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
                <button type="button" onclick="window.location.href='/pengaturan/hargajual'" class="btn btn-danger"
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
