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
                                        <td class="with-btn">
                                            <select class="form-control" x-init="$($el).selectpicker({
                                                liveSearch: true,
                                                width: 'auto',
                                                size: 10,
                                                container: 'body',
                                                style: '',
                                                showSubtext: true,
                                                styleBase: 'form-control'
                                            })"
                                                wire:model.live="barang.{{ $index }}.id">
                                                <option value="">-- Pilih Barang --</option>
                                                @foreach ($dataBarang as $subRow)
                                                    <option value="{{ $subRow['id'] }}">
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
                                                wire:model.live="barang.{{ $index }}.barang_satuan_id">
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
                                                value="{{ number_format($row['harga'] ?? 0) }}" disabled
                                                autocomplete="off">
                                        </td>
                                        <td class="with-btn">
                                            <input type="number" class="form-control" min="0" step="1"
                                                min="0" wire:model.live="barang.{{ $index }}.qty"
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
                    <input class="form-control text-end" type="text" wire:model.live="diskon" />
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
                            <label class="form-label">Jenis Bayar</label>
                            <select class="form-control" wire:model.live="metode_bayar" data-width="100%">
                                <option hidden selected>-- Pilih Jenis Bayar --</option>
                                @foreach (\App\Enums\MetodeBayarEnum::cases() as $item)
                                    <option value="{{ $item->value }}">{{ $item->label() }}</option>
                                @endforeach
                            </select>
                            @error('metode_bayar')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        @if ($metode_bayar == 'Cash')
                            <div class="mb-3">
                                <label class="form-label">Cash</label>
                                <input class="form-control" type="number" wire:model.live="cash" />
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
                    <input type="submit" value="Simpan" class="btn btn-success" />
                @endrole
                <a href="/penjualan/data" class="btn btn-warning">Data</a>
            </div>
        </form>
    </div>
    <x-modal.cetak judul='Nota' />
</div>

{{-- @push('scripts') --}}
{{-- <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('calculation', (data) => {
                calculation(data.index, data.harga);
            });
        });

        function calculation(index, harga = null) {
            let harga = harga ?? document.getElementById('barang-harga-' + index).value.replace(/\,/g, '');
            let qty = document.getElementById('barang-qty-' + index).value;
            let discount = document.getElementById('barang-discount-' + index).value;
            let total = (harga * qty) - (harga * qty * discount / 100);
            document.getElementById('barang-total-' + index).value = numberFormat(total);
        }
    </script> --}}
{{-- @endpush --}}
