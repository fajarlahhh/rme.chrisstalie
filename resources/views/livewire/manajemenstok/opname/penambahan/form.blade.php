<div>
    @section('title', 'Penambahan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Manajemen Stok</li>
        <li class="breadcrumb-item">Opname</li>
        <li class="breadcrumb-item">Penambahan</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Opname <small>Penambahan</small></h1>

    <form wire:submit="submit">
        <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
            <div class="panel-heading ui-sortable-handle">
                <h4 class="panel-title">Form</h4>
            </div>
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Cari Barang</label>
                    <div wire:ignore>
                        <select class="form-control" wire:model="barang_id"x-init="$($el).select2({
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                        $($el).on('change', function(e) {
                            $wire.set('barang_id', e.target.value);
                        });">
                            <option value="" selected>-- Tidak Ada Barang --</option>
                            @foreach ($dataBarang as $item)
                                <option value="{{ $item['id'] }}">{{ $item['nama'] }} - {{ $item['satuan'] }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">No. Batch</label>
                    <input type="text" class="form-control" wire:model="no_batch">
                    @error('no_batch')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Tgl. Kedaluarsa</label>
                    <input type="date" class="form-control" wire:model="tanggal_kedaluarsa">
                    @error('tanggal_kedaluarsa')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga Beli</label>
                    <input type="text" class="form-control text-end" wire:model="harga_beli">
                    @error('harga_beli')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Qty Masuk (Dalam {{ $barang['satuan'] ?? '' }})</label>
                    <input type="number" class="form-control" wire:model="qty_masuk" autocomplete="off">
                    @error('qty_masuk')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" type="text" wire:model="catatan"></textarea>
                    @error('catatan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <button type="button" onclick="window.location.href='/manajemenstok/pengurangan/index'"
                    class="btn btn-warning" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Kembali
                </button>
                <x-alert />
            </div>
        </div>
    </form>

    <div wire:loading>
        <x-loading />
    </div>
</div>
