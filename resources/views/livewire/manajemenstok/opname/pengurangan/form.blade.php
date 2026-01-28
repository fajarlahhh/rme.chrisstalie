<div>
    @section('title', 'Opname Pengurangan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Manajemen Stok</li>
        <li class="breadcrumb-item">Opname</li>
        <li class="breadcrumb-item">Pengurangan</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Opname <small>Pengurangan</small></h1>

    <form wire:submit="submit">
        <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
            <div class="panel-heading ui-sortable-handle">
                <h4 class="panel-title">Form</h4>
            </div>
            <div class="panel-body">
                <div class="alert alert-primary">
                    <div class="mb-3">
                        <label class="form-label">Cari Barang</label>
                        <div wire:ignore>
                            <select class="form-control" wire:model="barang_id" x-init="$($el).select2({
                                width: '100%',
                                dropdownAutoWidth: true,
                                placeholder: 'Pilih Barang',
                                templateResult: function(state) {
                                    if (!state.id) return state.text;
                                    var option = $(state.element);
                                    var nama = option.data('nama');
                                    var harga = option.data('harga');
                                    var satuan = option.data('satuan');
                                    var tglExp = option.data('exp');
                                    var batch = option.data('batch');
                                    var qty = option.data('qty');
                                    if (!nama) return state.text;
                                    return $(`
                                                                                                                                                                                                <div>
                                                                                                                                                                                                    <div style='font-weight:bold'>${nama}</div>
                                                                                                                                                                                                    <div style='font-size:90%;color:#6c757d'>
                                                                                                                                                                                                        Harga Beli : ${harga} <br/>
                                                                                                                                                                                                        Tgl. Kedaluarsa : ${tglExp} <br/>
                                                                                                                                                                                                        Batch : ${batch} <br/>
                                                                                                                                                                                                        Qty : ${qty} ${satuan} <br/>
                                                                                                                                                                                                    </div>
                                                                                                                                                                                                </div>
                                                                                                                                                                                            `);
                                },
                                templateSelection: function(state) {
                                    if (!state.id) return state.text;
                                    var option = $(state.element);
                                    var nama = option.data('nama');
                                    var batch = option.data('batch');
                                    return nama;
                                },
                                escapeMarkup: function(markup) { return markup; }
                            });
                            $($el).on('change', function(e) {
                                console.log(e.target.value);
                                $wire.set('barang_id', e.target.value);
                            });">
                                <option value="" selected>-- Pilih Barang --</option>
                                @foreach (collect($dataBarang)->sortBy('nama') as $row)
                                    <option value="{{ $row['id'] }}" data-nama="{{ $row['nama'] }}"
                                        data-harga="{{ number_format($row['harga'], 0, ',', '.') }}"
                                        data-satuan="{{ $row['satuan'] }}" data-exp="{{ $row['tanggal_kedaluarsa'] }}"
                                        data-batch="{{ $row['no_batch'] }}" data-qty="{{ $row['qty'] }}">
                                        {{ $row['nama'] }} (Batch: {{ $row['no_batch'] }}) - {{ $row['qty'] }}
                                        {{ $row['satuan'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">No. Batch</label>
                        <input type="text" class="form-control" value="{{ $barang['no_batch'] ?? '' }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tgl. Kedaluarsa</label>
                        <input type="date" class="form-control" value="{{ $barang['tanggal_kedaluarsa'] ?? '' }}"
                            disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Harga Beli</label>
                        <input type="text" class="form-control text-end"
                            value="{{ number_format($barang['harga'] ?? 0) }}" disabled>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Stok</label>
                        <input type="text" class="form-control"
                            value="{{ number_format($barang['qty'] ?? 0) }} {{ $barang['satuan'] ?? '' }}" disabled>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Qty Dikeluarkan (Dalam Satuan {{ $barang['satuan'] ?? '' }})</label>
                    <input type="number" class="form-control" wire:model="qty_keluar" min="1"
                        max="{{ $barang['qty'] }}" autocomplete="off">
                    @error('qty_keluar')
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
                <button type="button" onclick="window.location.href='/manajemenstok/opname/pengurangan/index'"
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
