<div x-data="penjualanForm()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Penjualan')

    @section('breadcrumb')
        <li class="breadcrumb-item active">Penjualan</li>
    @endsection

    <h1 class="page-header">Penjualan</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Keterangan</label>
                    <textarea class="form-control" type="text" wire:model="keterangan" x-model="keterangan"></textarea>
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
                                    <th class="w-150px">Harga</th>
                                    <th class="w-100px">Qty</th>
                                    <th class="w-150px">Subtotal</th>
                                    <th class="w-5px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in barang" :key="index">
                                    <tr>
                                        <td wire:ignore>
                                            <select class="form-control" x-model="row.id" x-init="$($el).select2({
                                                width: '100%',
                                                dropdownAutoWidth: true
                                            });
                                            $($el).on('change', function(e) {
                                                row.id = e.target.value;
                                                updateBarang(index);
                                            });
                                            $watch('row.id', (value) => {
                                                if (value !== $($el).val()) {
                                                    $($el).val(value).trigger('change');
                                                }
                                            });">
                                                <option value="" selected>-- Pilih Barang --</option>
                                                <template x-for="item in dataBarang" :key="item.id">
                                                    <option :value="item.id" :selected="row.id == item.id"
                                                        x-text="`${item.nama} (Rp. ${new Intl.NumberFormat('id-ID').format(item.harga)} / ${item.satuan})`">
                                                    </option>
                                                </template>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-end w-150px"
                                                :value="formatNumber(row.harga)" disabled>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control w-100px" min="1"
                                                step="any" x-model.number="row.qty" @input="calculateBarang(index)">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-end w-150px"
                                                :value="formatNumber(row.subtotal)" disabled>
                                        </td>
                                        <td>
                                            <button type="button" class="btn btn-danger" @click="hapusBarang(index)">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr>
                                    <th colspan="3" class="text-end align-middle">Total Harga Barang</th>
                                    <th>
                                        <input type="text" class="form-control text-end"
                                            :value="formatNumber(total_harga_barang)" disabled autocomplete="off">
                                    </th>
                                    <th></th>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="5">
                                        <div class="text-center">
                                            <button type="button" class="btn btn-secondary " @click="addBarang">
                                                Tambah Barang
                                            </button>
                                            <br>
                                            <template x-if="$store.wireErrors?.barang">
                                                <span class="text-danger" x-text="$store.wireErrors.barang"></span>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <br>
                <div class="mb-3">
                    <label class="form-label">Diskon <small>(Rp.)</small></label>
                    <input class="form-control text-end" type="text" wire:model="diskon" @change="hitungTotal()"
                        x-model.number="diskon" />
                </div>
                <div class="mb-3">
                    <label class="form-label">Total Tagihan</label>
                    <input class="form-control text-end" type="text" disabled :value="formatNumber(total_tagihan)" />
                </div>
                <hr>
                <div class="note alert-success mb-2">
                    <div class="note-content">
                        <h4>Pembayaran</h4>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Metode Bayar</label>
                            <select class="form-control" wire:model="metode_bayar" x-model="metode_bayar"
                                data-width="100%">
                                <option hidden>-- Pilih Metode Bayar --</option>
                                <template x-for="item in dataMetodeBayar" :key="item.id">
                                    <option :value="item.id" x-text="item.nama"
                                        :selected="metode_bayar == item.id"></option>
                                </template>
                            </select>
                        </div>
                        <template x-if="metode_bayar == 1">
                            <div>
                                <div class="mb-3">
                                    <label class="form-label">Cash</label>
                                    <input class="form-control" type="number" wire:model="cash"
                                        x-model.number="cash" />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Uang Kembali</label>
                                    <input class="form-control text-end" type="text" disabled
                                        :value="formatNumber((parseInt(cash || 0)) - (total_tagihan || 0))" />
                                </div>

                            </div>
                        </template>
                        <div class="mb-3">
                            <label class="form-label">Keterangan Pembayaran</label>
                            <input class="form-control" type="text" wire:model="keterangan_pembayaran"
                                x-model.number="keterangan_pembayaran" />
                        </div>
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
                <x-alert />
            </div>
        </form>
    </div>
    <x-modal.cetak judul='Nota' />
</div>

@push('scripts')
    <script>
        function penjualanForm() {
            return {
                barang: @js($barang).map(row => ({
                    ...row
                })),
                dataBarang: @js($dataBarang),
                dataMetodeBayar: @js($dataMetodeBayar ?? []),
                total_harga_barang: 0,
                diskon: @js($diskon),
                total_tagihan: 0,
                cash: @js($cash),
                keterangan: @js($keterangan),
                metode_bayar: @js($metode_bayar),
                keterangan_pembayaran: @js($keterangan_pembayaran),
                formatNumber(val) {
                    if (val === null || val === undefined || isNaN(val)) return '0';
                    return (val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },
                hitungTotal() {
                    this.total_harga_barang = this.barang.reduce((total, row) => {
                        let harga = parseInt(row.harga || 0);
                        let qty = parseInt(row.qty || 0);
                        return total + (harga * qty);
                    }, 0);
                    this.total_tagihan = this.total_harga_barang - (parseInt(this.diskon) || 0);
                },
                addBarang() {
                    this.barang.push({
                        id: '',
                        qty: 1,
                        harga: 0,
                        subtotal: 0,
                        kode_akun_id: '',
                        kode_akun_penjualan_id: '',
                    });
                    this.hitungTotal();
                },
                hapusBarang(index) {
                    this.barang.splice(index, 1);
                    this.hitungTotal();
                },
                updateBarang(index) {
                    let row = this.barang[index];
                    let selected = this.dataBarang.find(b => b.id == row.id);
                    if (selected) {
                        row.harga = selected.harga;
                        row.kode_akun_id = selected.kode_akun_id;
                        row.kode_akun_penjualan_id = selected.kode_akun_penjualan_id;
                    } else {
                        row.harga = 0;
                        row.kode_akun_id = '';
                        row.kode_akun_penjualan_id = '';
                    }
                    this.calculateBarang(index);
                },
                calculateBarang(index) {
                    let row = this.barang[index];
                    row.subtotal = (parseFloat(row.qty) || 0) * (parseFloat(row.harga) || 0);
                    this.hitungTotal();
                },
                syncToLivewire() {
                    // sinkronkan data ke livewire
                    if (window.Livewire && window.Livewire.find) {
                        let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            let $wire = window.Livewire.find(componentId);
                            if ($wire && typeof $wire.set === 'function') {
                                $wire.set('barang', JSON.parse(JSON.stringify(this.barang)), true);
                                $wire.set('diskon', this.diskon, true);
                                $wire.set('keterangan', this.keterangan, true);
                                $wire.set('cash', this.cash, true);
                                $wire.set('metode_bayar', this.metode_bayar, true);
                                $wire.set('total_tagihan', this.total_tagihan, true);
                            }
                        }
                    }
                },
                init() {
                    this.hitungTotal();
                    // perhatikan perubahan barang, update total jika perlu
                    this.$watch('barang', () => {
                        this.hitungTotal();
                    }, {
                        deep: true
                    });
                }
            }
        }
    </script>
@endpush
