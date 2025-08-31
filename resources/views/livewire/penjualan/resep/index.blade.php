<div>
    @section('title', 'Penjualan Resep')

    @section('breadcrumb')
        <li class="breadcrumb-item">Apotek</li>
        <li class="breadcrumb-item">Penjualan</li>
        <li class="breadcrumb-item active">Resep</li>
    @endsection

    <h1 class="page-header">Penjualan Resep</h1>

    <x-alert />

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input class="form-control" type="date" wire:model="date" />
                    @error('date')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Cari Pasien</label>
                    <div wire:ignore>
                        <select class="form-control" x-init="$($el).select2({
                            width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                            dropdownAutoWidth: true,
                            templateResult: format,
                            minimumInputLength: 3,
                            dataType: 'json',
                            ajax: {
                                url: '/cari/pasien',
                                data: function(params) {
                                    var query = {
                                        search: params.term
                                    }
                                    return query;
                                },
                                processResults: function(data, params) {
                                    return {
                                        results: data,
                                    };
                                },
                                cache: true
                            }
                        });
                        
                        $($el).on('change', function(element) {
                            $wire.set('pasien_id', $($el).val());
                        });
                        
                        function format(data) {
                            if (!data.id) {
                                return data.text;
                            }
                            var $data = $('<table><tr><th>No. RM</th><th>:</th><th>' + data.rm + '</th></tr>' +
                                '<tr><th>No. KTP</th><th>:</th><th>' + data.nik + '</th></tr>' +
                                '<tr><th>Nama</th><th>:</th><th>' + data.name + '</th></tr>' +
                                '<tr><th>Alamat</th><th>:</th><th>' + data.alamat + '</th></tr></table>');
                            return $data;
                        }">
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Dokter</label>
                    <select data-container="body" class="form-control" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    })"
                        wire:model="nakes_id" data-width="100%">
                        <option selected value="">-- Pilih Dokter --</option>
                        @foreach ($nakesData as $row)
                            <option value="{{ $row['id'] }}" data-subtext="{{ $row['dokter'] }}">
                                {{ $row['nama'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('nakes_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <input class="form-control" type="text" wire:model="uraian" />
                    @error('uraian')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                @foreach ($receipt as $index => $receipt)
                    <div class="note alert-secondary mb-3">
                        <div class="note-content table-responsive">
                            <div class="row">
                                <div class="col-md-1">
                                    <h4>
                                        R {{ $index + 1 }}
                                    </h4>
                                </div>
                                <div class="col-md-10">
                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi</label>
                                        <input class="form-control" type="text"
                                            wire:model="receipt.{{ $index }}.uraian" />
                                        @error('receipt.' . $index . '.uraian')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="alert bg-gray-300 mb-3 table-responsive">
                                        <table class="table table-borderless">
                                            <thead>
                                                <tr>
                                                    <th>Barang</th>
                                                    <th class="w-150px">Harga</th>
                                                    <th class="w-100px">Qty</th>
                                                    <th class="w-150px">Total</th>
                                                    <th class="w-5px"></th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($receipt['goods'] as $index2 => $goods)
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
                                                                wire:model.live="receipt.{{ $index }}.goods.{{ $index2 }}.id"
                                                                wire:change="setPrice({{ $index }}, {{ $index2 }})">
                                                                <option value="">-- Pilih Barang --</option>
                                                                @foreach ($goodsData as $subRow)
                                                                    <option value="{{ $subRow['id'] }}"
                                                                        data-subtext="{{ number_format($subRow['harga']) }}">
                                                                        {{ $subRow['nama'] . ' (' . $subRow['satuan'] . ')' }}
                                                                    </option>
                                                                @endforeach
                                                            </select>
                                                            @error('receipt.' . $index . '.goods.' . $index2 . '.id')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </td>
                                                        <td class="with-btn">
                                                            <input type="text" class="form-control text-end"
                                                                id="goods-harga-{{ $index2 }}"
                                                                value="{{ number_format($goods['harga']) }}" disabled
                                                                autocomplete="off">
                                                        </td>
                                                        <td class="with-btn">
                                                            <input type="number" class="form-control" min="0"
                                                                step="any" min="0" max="100"
                                                                wire:model.lazy="receipt.{{ $index }}.goods.{{ $index2 }}.qty"
                                                                wire:change="setPrice({{ $index }}, {{ $index2 }})"
                                                                autocomplete="off">
                                                            @error('receipt.' . $index . '.goods.' . $index2 . '.qty')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </td>
                                                        <td class="with-btn">
                                                            <input type="text" class="form-control text-end"
                                                                value="{{ number_format($goods['total']) }}" disabled
                                                                autocomplete="off">
                                                        </td>
                                                        <td class="with-btn">
                                                            <a href="javascript:;" class="btn btn-danger"
                                                                wire:click="deleteGoods({{ $index }},{{ $index2 }})">
                                                                <i class="fa fa-times"></i>
                                                            </a>
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                <tr>
                                                    <th colspan="3" class="text-end">Total</th">
                                                    <th>
                                                        <input type="text" class="form-control text-end"
                                                            value="{{ number_format(collect($receipt['goods'])->sum('total')) }}"
                                                            disabled autocomplete="off">
                                                    </th>
                                                    <th></th>
                                                </tr>
                                            </tbody>
                                            <tfoot>
                                                <tr>
                                                    <td colspan="5">
                                                        <div class="text-center">
                                                            <a class="btn btn-gray-400" href="javascript:;"
                                                                wire:click="addGoods({{ $index }})">Tambah
                                                                Barang</a>
                                                            <br>
                                                            @error('goods')
                                                                <span class="text-danger">{{ $message }}</span>
                                                            @enderror
                                                        </div>
                                                    </td>
                                                </tr>
                                            </tfoot>
                                        </table>
                                    </div>
                                </div>
                                <div class="col-md-1 text-end">
                                    <a class="btn btn-sm btn-danger" href="javascript:;"
                                        wire:click="deleteReceipt({{ $index }})">x</a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
                <div class="text-center">
                    <a class="btn btn-gray-300" href="javascript:;" wire:click="addReceipt">Tambah
                        Resep</a>
                    <br>
                    @error('goods')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Total Tagihan</label>
                    <input class="form-control text-end" type="text" disabled
                        value="{{ number_format($total) }}" />
                </div>
                <div class="mb-3">
                    <label class="form-label">Biaya Resep</label>
                    <input class="form-control" type="number" wire:model="receiptFee" />
                    @error('receiptFee')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Biaya Listrik</label>
                    <input class="form-control" type="number" wire:model="powerFee" />
                    @error('powerFee')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <hr>
                {{-- <div class="note-content">
                    <div class="mb-3">
                        <label class="form-label">Total Tagihan</label>
                        <input class="form-control text-end" type="text"
                            value="{{ number_format($adminFee + collect($pelayananTindakan)->sum(fn($q) => ($q['harga'] - (($q['discount'] ?: 0) / 100) * $q['harga']) * $q['qty']) + collect($toolsAndMaterial)->sum(fn($q) => ($q['harga'] - (($q['discount'] ?: 0) / 100) * $q['harga']) * $q['qty'])) }}"
                            disabled />
                        @error('adminFee')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div> --}}
                <div class="note alert-success mb-2">
                    <div class="note-content">
                        <h4>Pembayaran</h4>
                        <hr>
                        {{-- <div class="mb-3">
                            <label class="form-label">Jenis Bayar</label>
                            <select class="form-control" wire:model.lazy="type" data-width="100%" disabled>
                                <option hidden selected>-- Pilih Jenis Bayar --</option>
                                @foreach (\App\Enums\KasirEnum::cases() as $item)
                                    <option value="{{ $item->value }}">{{ $item->label() }}</option>
                                @endforeach
                            </select>
                            @error('type')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div> --}}
                        @if ($type == 'Cash')
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
                                    value="{{ number_format(($cash ?: 0) - (($receiptFee ?: 0) + ($powerFee ?: 0) + ($total ?: 0))) }}" />
                                @error('remainder')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan Bayar</label>
                                <input class="form-control" type="text" wire:model.live="kasir_description" />
                                @error('cash')
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
            let harga = harga ?? document.getElementById('goods-harga-' + index).value.replace(/\,/g, '');
            let qty = document.getElementById('goods-qty-' + index).value;
            let discount = document.getElementById('goods-discount-' + index).value;
            let total = (harga * qty) - (harga * qty * discount / 100);
            document.getElementById('goods-total-' + index).value = numberFormat(total);
        }
    </script> --}}
{{-- @endpush --}}
