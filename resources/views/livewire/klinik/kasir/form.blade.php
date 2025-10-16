<div x-data="kasirForm()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Kasir')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item">Kasir</li>
        <li class="breadcrumb-item active">Input</li>
    @endsection


    <h1 class="page-header">Kasir <small>Input</small></h1>

    @include('livewire.klinik.informasipasien', ['data' => $data])

    <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
        <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
            <div class="panel-heading ui-sortable-handle">
                <h4 class="panel-title">Form</h4>
            </div>
            <div class="panel-body">
                <table class="table p-0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Jenis</th>
                            <th>Item</th>
                            <th class=" w-150px">Diskon <small class="text-muted">(Rp.)</small></th>
                            <th class="bg-info-subtle text-end">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, index) in tindakan" :key="index">
                            <tr>
                                <td x-text="index + 1"></td>
                                <td>Tindakan</td>
                                <td>
                                    <span x-text="row.nama"></span> <br>
                                    <b>
                                        <small class="text-muted">&nbsp;&nbsp;&nbsp;- Rp.
                                            <span x-text="new Intl.NumberFormat('id-ID').format(row.biaya)"></span> x
                                            <span x-text="row.qty"></span>
                                        </small>
                                    </b><br>
                                    &nbsp;&nbsp;&nbsp;- Catatan : <span x-text="row.catatan"></span>
                                    <br>
                                    &nbsp;&nbsp;&nbsp;- Dokter : <span
                                        x-text="row.dokter_id ? dataNakes.find(n => n.id == row.dokter_id)?.nama : 'Tidak Ada Dokter'"></span>
                                    <br>
                                    <div class="row">
                                        <div class="col-md-2"> &nbsp;&nbsp;&nbsp;- Perawat :
                                        </div>
                                        <div class="col-md-10" wire:ignore>
                                            <select class="form-control" x-model="row.perawat_id"
                                                x-init="$($el).select2({
                                                    width: '100%',
                                                    dropdownAutoWidth: true
                                                });
                                                $($el).on('change', function(e) {
                                                    row.perawat_id = e.target.value;
                                                });
                                                $watch('row.perawat_id', (value) => {
                                                    if (value !== $($el).val()) {
                                                        $($el).val(value).trigger('change');
                                                    }
                                                });">
                                                <option value="">-- Tidak Ada Perawat --</option>
                                                <template x-for="nakes in dataNakes" :key="nakes.id">
                                                    <option :value="nakes.id"
                                                        :selected="row.perawat_id == nakes.id" x-text="nakes.nama">
                                                    </option>
                                                </template>
                                            </select>
                                        </div>
                                </td>
                                <td class="align-middle">
                                    <input type="number" class="form-control" @keyup="hitungTotalTagihan()"
                                        x-model.number="row.diskon">
                                </td>
                                <th class="align-middle bg-info-subtle text-end">
                                    <span
                                        x-text="new Intl.NumberFormat('id-ID').format(row.biaya * row.qty - row.diskon)"></span>
                                </th>
                            </tr>
                        </template>
                        <tr class="bg-light">
                            <th colspan="4" class="text-end">
                                BIAYA TINDAKAN
                            </th>
                            <th class="bg-info-subtle text-end">
                                <span x-text="new Intl.NumberFormat('id-ID').format(total_tindakan)"></span>
                            </th>
                        </tr>
                        <template x-for="(row, index) in resep" :key="`resep-${index}`">
                            <tr>
                                <td x-text="tindakan.length + index + 1"></td>
                                <td>Resep Obat <span x-text="row.resep"></span><br>&nbsp;&nbsp;&nbsp;<small
                                        class="text-muted" x-text="row.nama"></small></td>
                                <td>
                                    <div class="border bg-white rounded">
                                        <table class="table bg-white mb-0">
                                            <template x-for="(barang, barangIndex) in row.barang"
                                                :key="barangIndex">
                                                <tr>
                                                    <td>
                                                        <select class="form-control" x-model="barang.id"
                                                            x-init="$($el).select2({
                                                                width: 'auto',
                                                                dropdownAutoWidth: true
                                                            });
                                                            $($el).on('change', function(e) {
                                                                barang.id = e.target.value;
                                                                updateBarang(resepIndex, barangIndex);
                                                            });
                                                            $watch('barang.id', (value) => {
                                                                if (value !== $($el).val()) {
                                                                    $($el).val(value).trigger('change');
                                                                }
                                                            });">
                                                            <option value="" selected>-- Tidak Ada Barang --
                                                            </option>
                                                            <template x-for="item in dataBarang" :key="item.id">
                                                                <option :value="item.id"
                                                                    :selected="barang.id == item.id"
                                                                    x-text="`${item.nama} (Rp. ${new Intl.NumberFormat('id-ID').format(item.harga)} / ${item.satuan})`">
                                                                </option>
                                                            </template>
                                                        </select>
                                                    </td>
                                                    <td class="w-100px pe-2">
                                                        <input type="number" class="form-control" min="0"
                                                            placeholder="Qty" step="1"
                                                            @keyup="hitungTotalTagihan()" x-model.number="barang.qty"
                                                            autocomplete="off">
                                                    </td>
                                                </tr>
                                            </template>
                                        </table>
                                        <small class="text-muted">&nbsp;&nbsp;&nbsp;Catatan : <span
                                                x-text="row.catatan"></span></small>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    &nbsp;
                                </td>
                                <th class="align-middle bg-info-subtle text-end">
                                    <span
                                        x-text="new Intl.NumberFormat('id-ID').format(row.barang.reduce((sum, b) => sum + (b.harga * b.qty), 0))"></span>
                                </th>
                            </tr>
                        </template>
                        <tr class="bg-light">
                            <th colspan="4" class="text-end">
                                BIAYA RESEP
                            </th>
                            <th class="bg-info-subtle text-end">
                                <span x-text="new Intl.NumberFormat('id-ID').format(total_resep)"></span>
                            </th>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-light">
                            <th colspan="4" class="text-end">TOTAL TAGIHAN</th>
                            <th class="bg-info-subtle text-end">
                                <span x-text="new Intl.NumberFormat('id-ID').format(total_tagihan)"></span>
                            </th>
                        </tr>
                    </tfoot>
                </table>
                <div class="note alert-success mb-2">
                    <div class="note-content">
                        <h4>Pembayaran</h4>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Metode Bayar</label>
                            <select class="form-control" wire:model="metode_bayar" x-model="metode_bayar"
                                data-width="100%">
                                <option hidden>-- Tidak Ada Metode Bayar --</option>
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
                <button type="button" class="btn btn-warning m-r-3" wire:loading.attr="disabled"
                    onclick="window.location.href='/klinik/kasir'">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Data
                </button>
                <x-alert />
            </div>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        function kasirForm() {
            return {
                tindakan: @js($tindakan),
                resep: @js($resep),
                dataNakes: @js($dataNakes),
                dataMetodeBayar: @js($dataMetodeBayar),
                dataBarang: @js($dataBarang),
                metode_bayar: @js($metode_bayar),
                cash: @js($cash),
                total_tagihan: 0,
                total_tindakan: 0,
                total_resep: 0,
                keterangan_pembayaran: @js($keterangan_pembayaran),
                formatNumber(val) {
                    if (val === null || val === undefined || isNaN(val)) return '0';
                    return (val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },
                hitungTotalTagihan() {
                    this.total_tindakan = this.tindakan.reduce((sum, row) => {
                        return sum + (row.biaya * row.qty - row.diskon);
                    }, 0);

                    this.total_resep = this.resep.reduce((sum, row) => {
                        let subtotal = row.barang.reduce((s, b) => s + (b.harga * b.qty), 0);
                        return sum + subtotal;
                    }, 0);

                    this.total_tagihan = this.total_tindakan + this.total_resep;
                },

                syncToLivewire() {
                    // Sinkronkan data ke Livewire
                    if (window.Livewire && window.Livewire.find) {
                        let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            let $wire = window.Livewire.find(componentId);
                            if ($wire && typeof $wire.set === 'function') {
                                $wire.set('tindakan', JSON.parse(JSON.stringify(this.tindakan)), false);
                                $wire.set('resep', JSON.parse(JSON.stringify(this.resep)), false);
                                $wire.set('total_tindakan', this.total_tindakan, false);
                                $wire.set('total_resep', this.total_resep, false);
                                $wire.set('total_tagihan', this.total_tagihan, false);
                                $wire.set('metode_bayar', this.metode_bayar, false);
                                $wire.set('cash', this.cash, false);
                                $wire.set('keterangan_pembayaran', this.keterangan_pembayaran, false);
                            }
                        }
                    }
                },

                init() {
                    this.hitungTotalTagihan();
                }
            }
        }
    </script>
@endpush
