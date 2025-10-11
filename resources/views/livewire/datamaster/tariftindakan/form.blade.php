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
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input class="form-control" type="text" wire:model="nama" />
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ICD 9 CM</label>
                            <input class="form-control" type="text" wire:model="icd_9_cm" />
                            @error('icd_9_cm')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tarif</label>
                            <input class="form-control" type="number" step="1" min="0"
                                wire:model.lazy="tarif" />
                            @error('tarif')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select class="form-control" wire:model="kode_akun_id" data-width="100%">
                                <option hidden selected>-- Pilih Kode Akun --</option>
                                @foreach ($dataKodeAkun as $item)
                                    <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kode_akun_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <!-- TABEL ALAT -->
                        <div class="alert alert-secondary">
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
                                    <template x-for="(row, index) in alat" :key="row.id || index">
                                        <tr>
                                            <td class="with-btn" wire:ignore>
                                                <select class="form-control" x-model.lazy="row.id"
                                                    x-init="$($el).select2({ width: '100%' });
                                                    $($el).on('change', function(e) {
                                                        row.id = e.target.value;
                                                        updateAlat(index);
                                                    });
                                                    $watch('row.id', (value) => {
                                                        if (value !== $($el).val()) {
                                                            $($el).val(value).trigger('change');
                                                        }
                                                    });">
                                                    <option value="">-- Pilih Alat --</option>
                                                    <template x-for="alatItem in dataAset" :key="alatItem.id">
                                                        <option :value="alatItem.id"
                                                            x-text="`${alatItem.nama} ${alatItem.metode_penyusutan == 'Satuan Hasil Produksi' ? '(Rp. ' + new Intl.NumberFormat('id-ID').format(Math.round(alatItem.harga_perolehan / alatItem.masa_manfaat)) + ')' : ''}`">
                                                        </option>
                                                    </template>
                                                </select>
                                            </td>
                                            <td class="with-btn">
                                                <input type="number" class="form-control" min="1" step="any"
                                                    x-model.number="row.qty" @input="calculateAlat(index)">
                                            </td>
                                            <td class="with-btn">
                                                <input type="text" class="form-control text-end"
                                                    :value="new Intl.NumberFormat('id-ID').format((row.biaya ?? 0) * (row.qty ??
                                                        0))"
                                                    disabled autocomplete="off">
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
                                                :value="new Intl.NumberFormat('id-ID').format(alat.reduce((total, row) =>
                                                    total + (row.biaya ?? 0) * (row.qty ?? 0), 0))"
                                                disabled autocomplete="off">
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
                                                @error('alat')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <!-- TABEL BAHAN -->
                        <div class="alert alert-secondary" x-data="{
                            bahan: @js(collect($alatBarang)->where('jenis', 'Barang')->values()),
                            dataBarang: @js($dataBarang),
                            calculateBahan(index) {
                                let row = this.bahan[index];
                                let selectedBarang = this.dataBarang.find(b => b.id == row.barang_id);
                                if (selectedBarang) {
                                    let satuans = selectedBarang.satuan ? [selectedBarang] : (selectedBarang.barangSatuan || []);
                                    let satuan = satuans.find(s => s.id == row.barang_satuan_id);
                                    row.biaya = satuan ? satuan.harga_jual : 0;
                                } else {
                                    row.biaya = 0;
                                }
                                this.$wire.set('alatBarang.' + index + '.qty', row.qty);
                                this.$wire.set('alatBarang.' + index + '.barang_id', row.barang_id);
                                this.$wire.set('alatBarang.' + index + '.barang_satuan_id', row.barang_satuan_id);
                            },
                            addBahan() {
                                this.bahan.push({
                                    barang_id: '',
                                    barang_satuan_id: '',
                                    qty: 1,
                                    biaya: 0
                                });
                                this.$wire.call('tambahAlatBarang', 'Barang');
                            },
                            hapusBahan(index) {
                                this.bahan.splice(index, 1);
                                this.$wire.call('hapusAlatBarang', index);
                            }
                        }">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th> Bahan</th>
                                        <th class="w-150px">Satuan</th>
                                        <th class="w-100px">Qty</th>
                                        <th class="w-150px">Sub Total</th>
                                        <th class="w-5px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in bahan" :key="row.barang_id || index">
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
                                                    x-model="row.id"
                                                    @change="row.barang_satuan_id=''; calculateBahan(index)">
                                                    <option value="">-- Pilih Barang --</option>
                                                    <template x-for="barang in dataBarang" :key="barang.id">
                                                        <option :value="barang.id"
                                                            x-text="`${barang.nama} ${barang.satuan}`">
                                                        </option>
                                                    </template>
                                                </select>
                                                <template x-if="$store.wireErrors?.[`barang.${index}.id`]">
                                                    <span class="text-danger"
                                                        x-text="$store.wireErrors[`barang.${index}.id`]"></span>
                                                </template>
                                            </td>
                                            <td class="with-btn">
                                                <input type="number" class="form-control" min="1"
                                                    step="any" x-model.number="row.qty"
                                                    @input="calculateBahan(index)">
                                                <template x-if="$store.wireErrors?.[`barang.${index}.qty`]">
                                                    <span class="text-danger"
                                                        x-text="$store.wireErrors[`barang.${index}.qty`]"></span>
                                                </template>
                                            </td>
                                            <td class="with-btn">
                                                <input type="text" class="form-control text-end"
                                                    :value="new Intl.NumberFormat('id-ID').format((row.biaya ?? 0) * (row.qty ??
                                                        0))"
                                                    disabled autocomplete="off">
                                            </td>
                                            <td>
                                                <button type="button" class="btn btn-danger"
                                                    @click="hapusBahan(index)">
                                                    <i class="fa fa-times"></i>
                                                </button>
                                            </td>
                                        </tr>
                                    </template>
                                    <tr>
                                        <th colspan="3" class="text-end align-middle">Total Biaya Bahan
                                        </th>
                                        <th>
                                            <input type="text" class="form-control text-end"
                                                :value="new Intl.NumberFormat('id-ID').format(bahan.reduce((total, row) =>
                                                    total + (row.biaya ?? 0) * (row.qty ?? 0), 0))"
                                                disabled autocomplete="off">
                                        </th>
                                        <th></th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center">
                                                <button type="button" class="btn btn-secondary" @click="addBahan">
                                                    Tambah Bahan
                                                </button>
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
                                    wire:model.live="biaya_jasa_dokter" />
                                @error('biaya_jasa_dokter')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Biaya Jasa Perawat</label>
                                <input class="form-control" type="number" step="1" min="0"
                                    wire:model.live="biaya_jasa_perawat" />
                                @error('biaya_jasa_perawat')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Keuntungan</label>
                                <input class="form-control" type="text"
                                    value="{{ number_format(
                                        ($tarif === '' ? 0 : $tarif ?? 0) -
                                            ($biaya_jasa_dokter === '' ? 0 : $biaya_jasa_dokter ?? 0) -
                                            ($biaya_jasa_perawat === '' ? 0 : $biaya_jasa_perawat ?? 0) -
                                            ($biaya_bahan === '' ? 0 : $biaya_bahan ?? 0) -
                                            ($biaya_alat === '' ? 0 : $biaya_alat ?? 0),
                                    ) }}"
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
                <button type="button" onclick="window.location.href='{{ $previous }}'" class="btn btn-danger"
                    wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
            </div>
        </form>
    </div>

    <x-alert />

</div>

@push('scripts')
    <script>
        function tarifTindakanForm() {
            return {
                alat: Array.isArray(@js($alat)) ? @js($alat) : [],
                dataAset: @js($dataAset),
                componentId: null,
                get $wire() {
                    if (!this.componentId && this.$root) {
                        this.componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                    }
                    if (this.componentId) {
                        return window.Livewire.find(this.componentId);
                    }
                    return null;
                },
                addAlat() {
                    this.alat.push({
                        id: '',
                        qty: 1,
                        biaya: 0,
                    });
                },
                hapusAlat(index) {
                    if (index > -1 && index < this.alat.length) {
                        this.alat.splice(index, 1);
                        this.syncToLivewire();
                    }
                },
                updateAlat(index) {
                    let row = this.alat[index];
                    let selectedAlat = this.dataAset.find(g => g.id == row.id);
                    if (selectedAlat) {
                        row.biaya = (selectedAlat.metode_penyusutan == 'Satuan Hasil Produksi') ?
                            Math.round((selectedAlat.harga_perolehan || 0) / (selectedAlat.masa_manfaat || 1)) :
                            0;
                    } else {
                        row.biaya = 0;
                    }
                    this.calculateAlat(index);
                },
                calculateAlat(index) {
                    let row = this.alat[index];
                    if (row && typeof row.qty !== "undefined" && typeof row.biaya !== "undefined") {
                        row.subtotal = (parseFloat(row.biaya) || 0) * (parseFloat(row.qty) || 0);
                    }
                },
                syncToLivewire() {
                    if (this.$wire) {
                        this.$wire.set('alat', JSON.parse(JSON.stringify(this.alat)));
                    }
                },
                init() {}
            }
        }
    </script>
@endpush
