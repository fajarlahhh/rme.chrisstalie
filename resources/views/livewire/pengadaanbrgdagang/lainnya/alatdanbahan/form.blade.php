<div x-data="pembelianForm()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Alat Dan Bahan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Lainnya</li>
        <li class="breadcrumb-item">Alat Dan Bahan</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Alat Dan Bahan <small>Tambah</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input class="form-control" type="date" wire:model="tanggal" x-model="tanggal"
                        max="{{ now()->format('Y-m-d') }}" required />
                    @error('tanggal')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Uraian/No. Faktur/Nota Pembelian</label>
                    <input class="form-control" type="text" wire:model="uraian" x-model="uraian" required />
                    @error('uraian')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Supplier</label>
                    <div wire:ignore>
                        <select class="form-control" x-model="supplier_id" x-init="$($el).select2({
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                        $($el).on('change', function(e) {
                            supplier_id = e.target.value;
                        });
                        $watch('supplier_id', (value) => {
                            if (value !== $($el).val()) {
                                $($el).val(value).trigger('change');
                            }
                        });"
                            wire:model="supplier_id">
                            <option value="">-- Tidak Ada Supplier --</option>
                            @foreach ($dataSupplier as $row)
                                <option value="{{ $row['id'] }}">
                                    {{ $row['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('supplier_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Pembayaran</label>
                    <select data-container="body" class="form-control" wire:model="pembayaran" x-model="pembayaran"
                        @change="pembayaranChanged()" data-width="100%">
                        <option selected value="Jatuh Tempo">Jatuh Tempo</option>
                        @foreach ($dataKodeAkun as $row)
                            <option value="{{ $row['id'] }}">{{ $row['nama'] }}</option>
                        @endforeach
                    </select>
                </div>
                <template x-if="pembayaran == 'Jatuh Tempo'">
                    <div class="mb-3">
                        <label class="form-label">Tanggal Jatuh Tempo</label>
                        <input class="form-control" type="date" wire:model="jatuh_tempo"
                            min="{{ now()->format('Y-m-d') }}" x-model="jatuh_tempo" required />
                        @error('jatuh_tempo')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </template>
                <div class="note alert-secondary mb-0">
                    <div class="note-content">
                        <div class="table-responsive">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Barang/Item</th>
                                        <th class="w-100px">Qty</th>
                                        <th class="w-150px">Harga Beli</th>
                                        <th class="w-150px">No. Batch</th>
                                        <th class="w-150px">Tanggal Kedaluarsa</th>
                                        <th class="w-150px">Sub Total</th>
                                        <th class="w-5px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @php
                                        $index = 0;
                                    @endphp
                                    <template x-for="(row, index) in barang" :key="index">
                                        <tr>
                                            <td>
                                                <div wire:ignore>
                                                    <select class="form-control" x-model="row.id" required
                                                        x-init="$($el).select2({
                                                            width: '100%',
                                                            dropdownAutoWidth: true
                                                        });
                                                        $($el).on('change', function(e) {
                                                            row.id = e.target.value;
                                                        });
                                                        $watch('row.id', (value) => {
                                                            if (value !== $($el).val()) {
                                                                $($el).val(value).trigger('change');
                                                            }
                                                        });">
                                                        <option value="" selected>-- Tidak Ada Barang --</option>
                                                        <template x-for="item in dataBarang" :key="item.id">
                                                            <option :value="item.id" :selected="row.id == item.id"
                                                                x-text="`${item.nama} - ${item.satuan}`">
                                                            </option>
                                                        </template>
                                                    </select>
                                                </div>
                                                @error('barang.' . $index . '.id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" min="1" step="1"
                                                    x-model.number="row.qty" @input="hitungTotal()" autocomplete="off">
                                                @error('barang.' . $index . '.qty')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" min="0" step="1"
                                                    x-model="row.harga_beli" @input="hitungTotal()" autocomplete="off"
                                                    required>
                                                @error('barang.' . $index . '.harga_beli')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" x-model="row.no_batch"
                                                    autocomplete="off">
                                                @error('barang.' . $index . '.no_batch')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="date" class="form-control"
                                                    min="{{ now()->format('Y-m-d') }}"
                                                    x-model="row.tanggal_kedaluarsa" autocomplete="off" required>
                                                @error('barang.' . $index . '.tanggal_kedaluarsa')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td>
                                                <input type="text" class="form-control text-end" min="0"
                                                    step="1" :value="formatNumber(row.qty * row.harga_beli)"
                                                    disabled autocomplete="off">
                                            </td>
                                            <td>
                                                <a href="javascript:;" class="btn btn-danger"
                                                    @click="hapusBarang(index)">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        @php
                                            $index++;
                                        @endphp
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="5" class="text-end">Total Harga Barang</th>
                                        <td>
                                            <input type="text" class="form-control text-end" min="0"
                                                step="1" :value="formatNumber(totalHargaBeli)" disabled
                                                autocomplete="off">
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="7">
                                            <div class="text-center">
                                                <a class="btn btn-secondary" href="javascript:;"
                                                    @click="tambahBarang">
                                                    Tambah Barang
                                                </a>
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
                </div>
                <br>
                <div class="mb-3">
                    <label class="form-label">Diskon <small>(Rp.)</small></label>
                    <input class="form-control" type="number" wire:model="diskon" x-model="diskon" required
                        @input="hitungTotal()" autocomplete="off" />
                    @error('diskon')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">PPN <small>(Rp.)</small></label>
                    <input class="form-control" type="number" wire:model="ppn" x-model="ppn" required
                        @input="hitungTotal()" autocomplete="off" />
                    @error('ppn')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Total</label>
                    <input class="form-control" type="text"
                        :value="formatNumber(totalHargaBeli - parseInt(diskon) + parseInt(ppn))" disabled
                        autocomplete="off" />
                </div>
            </div>
            <div class="panel-footer">
                @unlessrole('guest')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endunlessrole
                <button type="button" onclick="window.location.href='/pengadaanbrgdagang/lainnya/alatdanbahan'"
                    class="btn btn-danger" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
            </div>
        </form>
    </div>
    <x-alert />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>

<script>
    function pembelianForm() {
        return {
            tanggal: @js($tanggal ?? ''),
            uraian: @js($uraian ?? ''),
            supplier_id: @js($supplier_id ?? ''),
            pembayaran: @js($pembayaran ?? 'Jatuh Tempo'),
            jatuh_tempo: @js($jatuh_tempo ?? ''),
            diskon: @js($diskon ?? 0),
            ppn: @js($ppn ?? 0),
            barang: @json($barang ?? []),
            errors: {},
            totalHargaBeli: @js($totalHargaBeli ?? 0),
            barang: @js($barang).map(row => ({
                ...row
            })),
            dataBarang: @json($dataBarang),
            tambahBarang() {
                this.barang.push({
                    id: '',
                    qty: 1,
                    harga_beli: 0,
                    no_batch: '',
                    tanggal_kedaluarsa: '',
                    rasio_dari_terkecil: 0,
                });
            },
            hapusBarang(index) {
                this.barang.splice(index, 1);
                this.$nextTick(() => {
                    this.refreshSelect2();
                });
            },
            refreshSelect2() {
                let root = this.$root ?? document;
                $(root).find('select.form-control').each(function(i, el) {
                    if ($(el).hasClass('select2-hidden-accessible')) {
                        $(el).select2('destroy');
                    }
                    $(el).select2({
                        width: '100%'
                    });
                    el.dispatchEvent(new CustomEvent('updateSelect2Value', {
                        bubbles: true
                    }));
                });
            },
            hitungTotal() {
                let total = 0;
                for (let row of this.barang) {
                    let harga = parseFloat(row.harga_beli) || 0;
                    let qty = parseFloat(row.qty) || 0;
                    total += harga * qty;
                }
                this.totalHargaBeli = total;
            },
            formatNumber(num) {
                if (isNaN(num)) return '0';
                return Number(num).toLocaleString('id-ID');
            },
            pembayaranChanged() {
                if (this.pembayaran !== 'Jatuh Tempo') {
                    this.jatuh_tempo = '';
                }
            },
            updateHargaBeli(index) {
                this.hitungTotal();
            },
            syncToLivewire() {
                if (window.Livewire && window.Livewire.find) {
                    let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        let $wire = window.Livewire.find(componentId);
                        if ($wire && typeof $wire.set === 'function') {
                            console.log(this.barang);
                            $wire.set('tanggal', this.tanggal, false);
                            $wire.set('uraian', this.uraian, false);
                            $wire.set('supplier_id', this.supplier_id, false);
                            $wire.set('pembayaran', this.pembayaran, false);
                            $wire.set('jatuh_tempo', this.jatuh_tempo, false);
                            $wire.set('diskon', this.diskon, false);
                            $wire.set('ppn', this.ppn, false);
                            $wire.set('barang', JSON.parse(JSON.stringify(this.barang)), false);
                        }
                    }
                }
            },
            init() {
                this.hitungTotal();
            }
        }
    }
</script>
