<div x-data="resepObatForm()" x-ref="alpineRoot">
    @section('title', 'Peracikan Resep Obat')

    @section('breadcrumb')
        <li class="breadcrumb-item ">Klinik</li>
        <li class="breadcrumb-item active">Peracikan Resep Obat</li>
    @endsection

    <h1 class="page-header">Peracikan Resep Obat</h1>

    <div class="row">
        <div class="col-md-12">
            @include('livewire.klinik.informasipasien', ['data' => $data])
        </div>
        <div class="col-md-12">
            <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
                <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
                    <!-- begin panel-heading -->
                    <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title">Form</h4>
                    </div>
                    <div class="panel-body">
                        <template x-for="(resepItem, resepIndex) in resep" :key="resepIndex">
                            <div class="p-3 bg-light border rounded mb-3">
                                <div class="row">
                                    <div class="col-md-11">
                                        <div class="mb-3">
                                            <label class="form-label" x-text="`Resep ${resepIndex + 1}`"></label>
                                            <input type="text" class="form-control" x-model="resepItem.nama"
                                                placeholder="Nama Resep" disabled>
                                        </div>
                                    </div>
                                    <div class="col-md-1 text-end">
                                        <button type="button" wire:loading.attr="disabled"
                                            class="btn btn-danger btn-xs" @click="hapusResep(resepIndex)">
                                            &nbsp;x&nbsp;
                                        </button>
                                    </div>
                                </div>
                                <table class="table table-bordered bg-gray-100 mb-3">
                                    <thead>
                                        <tr>
                                            <th>Barang</th>
                                            <th class="w-150px">Harga Satuan</th>
                                            <th class="w-100px">Qty</th>
                                            <th class="w-150px">Sub Total</th>
                                            <th class="w-5px"></th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <template x-for="(barangItem, barangIndex) in resepItem.barang"
                                            :key="barangIndex">
                                            <tr>
                                                <td wire:ignore>
                                                    <select class="form-control" x-model="barangItem.id"
                                                        x-init="$($el).select2({
                                                            width: '100%',
                                                            dropdownAutoWidth: true
                                                        });
                                                        $($el).on('change', function(e) {
                                                            barangItem.id = e.target.value;
                                                            updateBarang(resepIndex, barangIndex);
                                                        });
                                                        $watch('barangItem.id', (value) => {
                                                            if (value !== $($el).val()) {
                                                                $($el).val(value).trigger('change');
                                                            }
                                                        });">
                                                        <option value="" selected>-- Tidak Ada Barang --
                                                        </option>
                                                        <template x-for="item in dataBarang" :key="item.id">
                                                            <option :value="item.id"
                                                                :selected="barangItem.id == item.id"
                                                                x-text="`${item.nama} (Rp. ${new Intl.NumberFormat('id-ID').format(item.harga)} / ${item.satuan})`">
                                                            </option>
                                                        </template>
                                                    </select>
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control text-end"
                                                        :value="formatNumber(barangItem.harga)" autocomplete="off"
                                                        disabled>
                                                </td>
                                                <td>
                                                    <input type="number" class="form-control" min="0"
                                                        step="1" x-model.number="barangItem.qty"
                                                        @input="hitungSubtotal(resepIndex, barangIndex)"
                                                        autocomplete="off">
                                                </td>
                                                <td>
                                                    <input type="text" class="form-control text-end"
                                                        :value="formatNumber(barangItem.subtotal)" autocomplete="off"
                                                        disabled>
                                                </td>
                                                <td class="w-10px align-middle">
                                                    <template x-if="barangItem.hapus">
                                                        <button type="button" wire:loading.attr="disabled"
                                                            class="btn btn-warning btn-sm"
                                                            @click="hapusBarang(resepIndex, barangIndex)">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </template>
                                                </td>
                                            </tr>
                                        </template>
                                        <tr>
                                            <th class="text-end align-middle" colspan="3">Total</th>
                                            <th>
                                                <input type="text" class="form-control text-end"
                                                    :value="formatNumber(resepTotal(resepIndex))" disabled>
                                            </th>
                                        </tr>
                                        <tr>
                                            <td colspan="4">
                                                <div class="text-center">
                                                    <button class="btn btn-secondary" wire:loading.attr="disabled"
                                                        type="button" @click="tambahBarang(resepIndex)">
                                                        Tambah Barang
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                                <textarea class="form-control" x-model="resepItem.catatan" placeholder="Catatan"></textarea>
                            </div>
                        </template>
                        <div class="mb-3">
                            <label class="form-label">Total Keseluruhan</label>
                            <input class="form-control text-end" type="text" disabled
                                :value="formatNumber(totalKeseluruhan)" />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" x-model="catatan" rows="3" placeholder="Catatan"></textarea>
                        </div>
                    </div>
                    <div class="panel-footer">
                        @role('administrator|supervisor|operator')
                            <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                                <span wire:loading class="spinner-border spinner-border-sm"></span>
                                Simpan
                            </button>
                        @endrole
                        <button type="button" class="btn btn-secondary m-r-3" wire:loading.attr="disabled"
                            onclick="window.location.href='/klinik/peracikanresepobat'">
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                            Data
                        </button>
                        <x-alert />
                    </div>
                </div>
            </form>
        </div>
    </div>
    <x-modal.cetak judul='Nota' />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>

@push('scripts')
    <script>
        function resepObatForm() {
            return {
                resep: @js($resep),
                dataBarang: @js($dataBarang),
                copying: false,
                catatan: @js($catatan),
                tambahResep() {
                    this.resep.push({
                        barang: [],
                        catatan: '',
                        nama: '',
                    });
                },
                formatNumber(val) {
                    if (val === null || val === undefined || isNaN(val)) return '0';
                    return (val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },

                hitungSubtotal(index, barangIndex) {
                    let barang = this.resep[index].barang[barangIndex];
                    barang.subtotal = (parseFloat(barang.qty) || 0) * (parseFloat(barang.harga) || 0);
                },
                hitungTotal() {
                    this.total = this.resep.reduce((total, row) => {
                        return total + (parseFloat(row.subtotal) || 0);
                    }, 0);
                },
                resepTotal(index) {
                    return this.resep[index].barang.reduce((total, barang) => total + (parseFloat(barang.subtotal) || 0),
                        0);
                },
                hapusResep(resepIndex) {
                    this.resep.splice(resepIndex, 1);
                },
                hitungTotal() {
                    this.total_harga_barang = this.barang.reduce((total, row) => {
                        let harga = parseInt(row.harga || 0);
                        let qty = parseInt(row.qty || 0);
                        return total + (harga * qty);
                    }, 0);
                    this.total_tagihan = this.total_harga_barang - (parseInt(this.diskon) || 0);
                },
                get totalKeseluruhan() {
                    let resepTotal = this.resep.reduce((total, resep) => total + this.resepTotal(this.resep
                        .indexOf(resep)), 0);
                    return resepTotal;
                },

                tambahBarang(resepIndex) {
                    this.resep[resepIndex].barang.push({
                        id: null,
                        qty: 1,
                        harga: 0,
                        subtotal: 0,
                        hapus: true,
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
                hapusBarang(resepIndex, barangIndex) {
                    this.resep[resepIndex].barang.splice(barangIndex, 1);
                    this.$nextTick(() => {
                        this.refreshSelect2();
                    });
                },

                updateBarang(resepIndex, barangIndex) {
                    let barangItem = this.resep[resepIndex].barang[barangIndex];
                    let selectedBarang = this.dataBarang.find(b => b.id == barangItem.id);

                    if (selectedBarang) {
                        barangItem.harga = selectedBarang.harga;
                        barangItem.barang_satuan_id = null; // Reset satuan selection
                    } else {
                        barangItem.harga = 0;
                        barangItem.barang_satuan_id = null;
                    }
                    this.hitungSubtotal(resepIndex, barangIndex);
                },
                copyResep(id) {
                    @this.copyResep(id).then(resep => {
                        this.resep = resep;
                    });
                },
                syncToLivewire() {
                    // Sync data to Livewire
                    if (window.Livewire && window.Livewire.find) {
                        let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            let $wire = window.Livewire.find(componentId);
                            if ($wire && typeof $wire.set === 'function') {
                                $wire.set('resep', JSON.parse(JSON.stringify(this.resep)), false);
                                $wire.set('catatan', this.catatan, false);
                            }
                        }
                    }
                }
            }
        }
    </script>
@endpush
