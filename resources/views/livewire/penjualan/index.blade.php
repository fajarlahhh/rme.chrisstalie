<div>
    @section('title', 'Penjualan')

    @section('breadcrumb')
        <li class="breadcrumb-item active">Penjualan</li>
    @endsection

    <h1 class="page-header">Penjualan</h1>

    <x-alert />

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" type="text" wire:model="keterangan"></textarea>
                    @error('keterangan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="note alert-secondary mb-0">
                    <div class="note-content table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th class="w-150px">Satuan</th>
                                    <th class="w-150px">Harga</th>
                                    <th class="w-100px">Qty</th>
                                    <th class="w-150px">Sub Total</th>
                                    <th class="w-5px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barang as $index => $row)
                                    <tr>
                                        <td class="with-btn" wire:ignore>
                                            <select class="form-control select2" x-init="window.livewireSelect2 = window.livewireSelect2 || [];
                                            window.livewireSelect2[{{ $index }}] = $($el).select2({
                                                width: '100%',
                                                templateResult: function(data) {
                                                    if (!data.id) { return data.text; }
                                                    var $result = $('<span></span>');
                                                    $result.text(data.text);
                                                    if ($(data.element).data('subtext')) {
                                                        $result.append('<span class=\'text-muted ms-2\' style=\'font-size: 0.9em;\'>(' + $(data.element).data('subtext') + ')</span>');
                                                    }
                                                    return $result;
                                                },
                                                templateSelection: function(data) {
                                                    if (!data.id) { return data.text; }
                                                    var $result = $('<span></span>');
                                                    $result.text(data.text);
                                                    if ($(data.element).data('subtext')) {
                                                        $result.append('<span class=\'text-muted ms-2\' style=\'font-size: 0.9em;\'>(' + $(data.element).data('subtext') + ')</span>');
                                                    }
                                                    return $result;
                                                }
                                            });
                                            $($el).on('change', function(e) { $wire.set('barang.{{ $index }}.id', $(this).val()); });" wire:ignore
                                                data-index="{{ $index }}" @if (isset($row['id']))
                                                data-selected="{{ $row['id'] }}"
                                @endif>
                                <option value="">-- Pilih Barang --</option>
                                @foreach ($dataBarang as $subRow)
                                    <option value="{{ $subRow['id'] }}" data-subtext="{{ $subRow['kategori'] }}"
                                        @if (isset($row['id']) && $row['id'] == $subRow['id']) selected @endif>
                                        {{ $subRow['nama'] }}
                                    </option>
                                @endforeach
                                </select>
                                @error('barang.' . $index . '.id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                </td>
                                <td class="with-btn">
                                    <select class="form-control"
                                        wire:model.lazy="barang.{{ $index }}.barang_satuan_id">
                                        <option value="">-- Pilih Satuan --</option>
                                        @foreach ($row['barangSatuan'] as $subRow)
                                            <option value="{{ $subRow['id'] }}"
                                                data-subtext="{{ $subRow['konversi_satuan'] }}">
                                                {{ $subRow['nama'] }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('barang.' . $index . '.barang_satuan_id')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="with-btn">
                                    <input type="text" class="form-control text-end"
                                        id="barang-harga-{{ $index }}"
                                        value="{{ number_format($row['harga'] ?? 0) }}" disabled autocomplete="off">
                                </td>
                                <td class="with-btn">
                                    <input type="number" class="form-control" min="0" step="1"
                                        min="0" wire:model.lazy="barang.{{ $index }}.qty"
                                        autocomplete="off">
                                    @error('barang.' . $index . '.qty')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                                <td class="with-btn">
                                    <input type="text" class="form-control text-end"
                                        value="{{ number_format((int) ($row['harga'] ?? 0) * (int) ($row['qty'] ?? 0)) }}"
                                        disabled autocomplete="off">
                                </td>
                                <td class="with-btn">
                                    <a href="javascript:;" class="btn btn-danger"
                                        wire:click="hapusBarang({{ $index }})">
                                        <i class="fa fa-times"></i>
                                    </a>
                                </td>
                                </tr>
                                @endforeach
                                <tr>
                                    <th colspan="4" class="text-end align-middle">Total Harga Barang</th">
                                    <th>
                                        <input type="text" class="form-control text-end"
                                            value="{{ number_format($total_harga_barang) }}" disabled
                                            autocomplete="off">
                                    </th>
                                    <th></th>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <div class="text-center">
                                            <a class="btn btn-secondary" href="javascript:;"
                                                wire:click="tambahBarang">Tambah
                                                Barang</a>
                                            <br>
                                            @error('barang')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <br>
                <div class="mb-3">
                    <label class="form-label">Diskon</label>
                    <input class="form-control text-end" type="text" wire:model.lazy="diskon" />
                </div>
                <div class="mb-3">
                    <label class="form-label">Total Tagihan</label>
                    <input class="form-control text-end" type="text" disabled
                        value="{{ number_format($total_harga_barang - $diskon) }}" />
                </div>
                <hr>
                <div class="note alert-success mb-2">
                    <div class="note-content">
                        <h4>Pembayaran</h4>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Metode Bayar</label>
                            <select class="form-control" wire:model.lazy="metode_bayar" data-width="100%">
                                <option hidden selected>-- Pilih Metode Bayar --</option>
                                @foreach ($dataMetodeBayar as $item)
                                    <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                                @endforeach
                            </select>
                            @error('metode_bayar')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        @if ($metode_bayar == 1)
                            <div class="mb-3">
                                <label class="form-label">Cash</label>
                                <input class="form-control" type="number" wire:model.lazy="cash" />
                                @error('cash')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Uang Kembali</label>
                                <input class="form-control text-end" type="text" disabled
                                    value="{{ number_format(($cash ?: 0) - ($total_harga_barang - $diskon ?: 0)) }}" />
                                @error('remainder')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <a href="/penjualan/data" class="btn btn-warning">Data</a>
            </div>
        </form>
    </div>
    <x-modal.cetak judul='Nota' />
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:navigated', function() {
            $('.select2').each(function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2({
                        width: '100%'
                    });
                }
            });
        });
        document.addEventListener('livewire:update', function() {
            $('.select2').each(function() {
                if (!$(this).hasClass("select2-hidden-accessible")) {
                    $(this).select2({
                        width: '100%'
                    });
                }
            });
        });
    </script>
@endpush
