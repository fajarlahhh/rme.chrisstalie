<div x-data="tarifTindakanForm()" x-init="init()" x-ref="alpineRoot">
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Tarif Tindakan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item">Tarif Tindakan</li>
        <li class="breadcrumb-item active">{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Tarif Tindakan <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
            <div class="panel-body">
                <div class="row">
                    <div class="col-lg-4">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input class="form-control" type="text" wire:model="nama" x-model="nama" />
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ICD 9 CM</label>
                            <input class="form-control" type="text" wire:model="icd_9_cm" x-model="icd_9_cm" />
                            @error('icd_9_cm')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tarif</label>
                            <input class="form-control" type="number" step="1" min="0" wire:model="tarif"
                                x-model.number="tarif" @keyup="hitungKeuntungan()" />
                            @error('tarif')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select class="form-control" wire:model="kode_akun_id" x-model="kode_akun_id"
                                x-init="$($el).selectpicker({
                                    liveSearch: true,
                                    width: 'auto',
                                    size: 10,
                                    container: 'body',
                                    style: '',
                                    showSubtext: true,
                                    styleBase: 'form-control'
                                })" data-width="100%">
                                <option hidden selected>-- Tidak Ada Kode Akun --</option>
                                @foreach ($dataKodeAkun as $item)
                                    <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kode_akun_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Catatan</label>
                            <textarea class="form-control" wire:model="catatan" x-model="catatan"></textarea>
                            @error('catatan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-lg-8">
                        <!-- TABEL ALAT -->
                        <div class="alert alert-secondary table-responsive" x-data="{
                            addAlat() {
                                    this.alat.push({
                                        id: '',
                                        qty: 1,
                                        biaya: 0,
                                        subtotal: 0,
                                    });
                                    this.hitungKeuntungan();
                                },
                                hapusAlat(index) {
                                    this.alat.splice(index, 1);
                                    this.hitungKeuntungan();
                                },
                                updateAlat(index) {
                                    let row = this.alat[index];
                                    let selectedAlat = this.dataAlat.find(g => g.id == row.id);
                                    if (selectedAlat) {
                                        row.biaya = (selectedAlat.metode_penyusutan == 'Satuan Hasil Produksi') ?
                                            Math.round((selectedAlat.harga_perolehan || 0) / (selectedAlat.masa_manfaat || 1)) :
                                            0;
                                    } else {
                                        row.biaya = 0;
                                    }
                                    this.calculateAlat(index);
                                    this.hitungKeuntungan();
                                },
                                calculateAlat(index) {
                                    let row = this.alat[index];
                                    row.subtotal = (parseFloat(row.qty) || 0) * (parseFloat(row.biaya) || 0) || 0;
                                    this.total_biaya_alat = this.alat.reduce((total, row) => total + (parseFloat(row.subtotal) || 0), 0);
                                    this.hitungKeuntungan();
                                },
                        }">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Alat</th>
                                        <th class="w-100px">Qty</th>
                                        <th class="w-150px">Sub Total</th>
                                        <th class="w-5px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in alat" :key="index">
                                        <tr>
                                            <td>
                                                <div wire:ignore>
                                                    <select class="form-control" x-model="row.id"
                                                        x-init="$($el).select2({
                                                            width: '100%',
                                                            dropdownAutoWidth: true
                                                        });
                                                        $($el).on('change', function(e) {
                                                            row.id = e.target.value;
                                                            updateAlat(index);
                                                        });
                                                        $watch('row.id', (value) => {
                                                            if (value !== $($el).val()) {
                                                                $($el).val(value).trigger('change');
                                                            }
                                                        });">
                                                        <option value="" selected>-- Tidak Ada Alat --
                                                        </option>
                                                        <template x-for="alat in dataAlat" :key="alat.id">
                                                            <option :value="alat.id" :selected="row.id == alat.id"
                                                                x-text="`${alat.nama} ${alat.metode_penyusutan == 'Satuan Hasil Produksi' ? '(Rp. ' + new Intl.NumberFormat('id-ID').format(Math.round(alat.harga_perolehan / alat.masa_manfaat)) + ')' : ''}`">
                                                            </option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control w-100px" min="1"
                                                    step="any" x-model.number="row.qty"
                                                    @input="calculateAlat(index)">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control text-end w-150px"
                                                    :value="formatNumber(row.subtotal)" disabled>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger" @click="hapusAlat(index)">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr>
                                        <th colspan="2" class="text-end align-middle">Total Biaya Alat
                                        </th>
                                        <th>
                                            <input type="text" class="form-control text-end"
                                                :value="formatNumber(total_biaya_alat)" disabled>
                                        </th>
                                        <th></th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center">
                                                <button type="button" class="btn btn-secondary" @click="addAlat">
                                                    Tambah Alat
                                                </button>
                                                <template x-if="$store.wireErrors?.alat">
                                                    <span class="text-danger" x-text="$store.wireErrors.alat"></span>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- TABEL BAHAN -->
                        <div class="alert alert-secondary table-responsive" x-data="{
                            addBarang() {
                                    this.barang.push({
                                        id: '',
                                        qty: 1,
                                        biaya: 0,
                                        subtotal: 0,
                                    });
                                    this.hitungKeuntungan();
                                },
                                hapusBarang(index) {
                                    this.barang.splice(index, 1);
                                    this.hitungKeuntungan();
                                    this.$nextTick(() => {
                                        this.refreshSelect2();
                                    });
                                },
                                updateBarang(index) {
                                    let row = this.barang[index];
                                    let selectedBarang = this.dataBarang.find(g => g.id == row.id);
                                    if (selectedBarang) {
                                        row.biaya = selectedBarang.biaya;
                                    } else {
                                        row.biaya = 0;
                                    }
                                    this.calculateBarang(index);
                                    this.hitungKeuntungan();
                                },
                                calculateBarang(index) {
                                    let row = this.barang[index];
                                    row.subtotal = (parseFloat(row.qty) || 0) * (parseFloat(row.biaya) || 0) || 0;
                                    this.total_biaya_barang = this.barang.reduce((total, row) => total + (parseFloat(row.subtotal) || 0), 0);
                                    this.hitungKeuntungan();
                                },
                        }">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Bahan</th>
                                        <th class="w-100px">Qty</th>
                                        <th class="w-150px">Sub Total</th>
                                        <th class="w-5px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in barang" :key="index">
                                        <tr>
                                            <td>
                                                <div wire:ignore>
                                                    <select class="form-control" x-model="row.id"
                                                        x-init="$($el).select2({
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
                                                        <option value="" selected>-- Tidak Ada Barang --
                                                        </option>
                                                        <template x-for="barang in dataBarang" :key="barang.id">
                                                            <option :value="barang.id"
                                                                :selected="row.id == barang.id"
                                                                x-text="`${barang.nama} (Rp. ${new Intl.NumberFormat('id-ID').format(barang.biaya)} / ${barang.satuan})`">
                                                            </option>
                                                        </template>
                                                    </select>
                                                </div>
                                            </td>
                                            <td>
                                                <input type="number" class="form-control w-100px" min="1"
                                                    step="any" x-model.number="row.qty"
                                                    @input="calculateBarang(index)">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control text-end w-150px"
                                                    :value="formatNumber(row.subtotal)" disabled>
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger"
                                                    @click="hapusBarang(index)">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr>
                                        <th colspan="2" class="text-end align-middle">Total Biaya Barang
                                        </th>
                                        <th>
                                            <input type="text" class="form-control text-end"
                                                :value="formatNumber(total_biaya_barang)" disabled autocomplete="off">
                                        </th>
                                        <th></th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center">
                                                <button type="button" class="btn btn-secondary" @click="addBarang">
                                                    Tambah Barang
                                                </button>
                                                <br>
                                                <template x-if="$store.wireErrors?.barang">
                                                    <span class="text-danger"
                                                        x-text="$store.wireErrors.barang"></span>
                                                </template>
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="alert alert-info">
                            <div class="mb-3">
                                <label class="form-label">Biaya Jasa Dokter</label>
                                <input class="form-control" type="number" step="1" min="0"
                                    wire:model="biaya_jasa_dokter" x-model.number="biaya_jasa_dokter"
                                    @keyup="hitungKeuntungan()" />
                                @error('biaya_jasa_dokter')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Biaya Jasa Perawat</label>
                                <input class="form-control" type="number" step="1" min="0"
                                    wire:model="biaya_jasa_perawat" x-model.number="biaya_jasa_perawat"
                                    @keyup="hitungKeuntungan()" />
                                @error('biaya_jasa_perawat')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Keuntungan</label>
                                <input class="form-control" type="text" :value="formatNumber(keuntungan)"
                                    disabled />
                            </div>
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
                <button type="button" onclick="window.location.href='/datamaster/tariftindakan'"
                    class="btn btn-danger" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
                <x-alert />
            </div>
        </form>
    </div>
</div>

@push('scripts')
    <script>
        function tarifTindakanForm() {
            return {
                alat: @js($alat).map(row => ({
                    ...row,
                    subtotal: row.subtotal ?? ((parseFloat(row.qty) || 0) * (parseFloat(row.biaya) || 0) || 0)
                })),
                dataAlat: @js($dataAlat),
                barang: @js($barang).map(row => ({
                    ...row,
                    subtotal: row.subtotal ?? ((parseFloat(row.qty) || 0) * (parseFloat(row.biaya) || 0) || 0)
                })),
                catatan: @js($catatan),
                dataBarang: @js($dataBarang),
                biaya_jasa_dokter: @js($biaya_jasa_dokter),
                biaya_jasa_perawat: @js($biaya_jasa_perawat),
                total_biaya_alat: @js($biaya_alat),
                total_biaya_barang: @js($biaya_barang),
                tarif: @js($tarif),
                nama: @js($nama),
                kode_akun_id: @js($kode_akun_id),
                icd_9_cm: @js($icd_9_cm),
                keuntungan: 0,
                formatNumber(val) {
                    if (val === null || val === undefined || isNaN(val)) return '0';
                    return (val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },
                hitungKeuntungan() {
                    this.total_biaya_alat = this.alat.reduce((total, row) => total + (parseFloat(row.subtotal) || 0), 0);
                    this.total_biaya_barang = this.barang.reduce((total, row) => total + (parseFloat(row.subtotal) || 0),
                        0);
                    this.keuntungan =
                        (parseFloat(this.tarif) || 0) -
                        (parseFloat(this.total_biaya_alat) || 0) -
                        (parseFloat(this.total_biaya_barang) || 0) -
                        (parseFloat(this.biaya_jasa_dokter) || 0) -
                        (parseFloat(this.biaya_jasa_perawat) || 0);
                },
                syncToLivewire() {
                    if (window.Livewire && window.Livewire.find) {
                        let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            let $wire = window.Livewire.find(componentId);
                            if ($wire && typeof $wire.set === 'function') {
                                $wire.set('alat', JSON.parse(JSON.stringify(this.alat)), false);
                                $wire.set('barang', JSON.parse(JSON.stringify(this.barang)), false);
                                $wire.set('nama', this.nama, false);
                                $wire.set('kode_akun_id', this.kode_akun_id, false);
                                $wire.set('icd_9_cm', this.icd_9_cm, false);
                                $wire.set('tarif', this.tarif, false);
                                $wire.set('biaya_jasa_dokter', this.biaya_jasa_dokter, false);
                                $wire.set('biaya_jasa_perawat', this.biaya_jasa_perawat, false);
                                $wire.set('catatan', this.catatan, false);
                            }
                        }
                    }
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
                init() {
                    this.total_biaya_alat = this.alat.reduce((total, row) => total + (parseFloat(row.subtotal) || 0), 0);
                    this.total_biaya_barang = this.barang.reduce((total, row) => total + (parseFloat(row.subtotal) || 0),
                        0);
                    this.hitungKeuntungan();

                    this.$watch('biaya_jasa_dokter', () => {
                        this.hitungKeuntungan();
                    });
                    this.$watch('biaya_jasa_perawat', () => {
                        this.hitungKeuntungan();
                    });
                    this.$watch('tarif', () => {
                        this.hitungKeuntungan();
                    });
                    this.$watch('alat', () => {
                        this.hitungKeuntungan();
                    }, {
                        deep: true
                    });
                    this.$watch('barang', () => {
                        this.hitungKeuntungan();
                    }, {
                        deep: true
                    });
                }
            }
        }
    </script>
@endpush
