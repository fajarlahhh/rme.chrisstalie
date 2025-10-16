<div x-data="resepObatForm()" x-ref="alpineRoot">
    @section('title', 'Resep Obat')

    @section('breadcrumb')
        <li class="breadcrumb-item ">Klinik</li>
        <li class="breadcrumb-item active">Resep Obat</li>
    @endsection

    <h1 class="page-header">Resep Obat</h1>
    
    @include('livewire.klinik.informasipasien', ['data' => $data])

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
                                    <label class="form-label"
                                        x-text="`Resep ${resepIndex + 1}`"></label>
                                    <input type="text" class="form-control" x-model="resepItem.nama"
                                        placeholder="Nama Resep">
                                </div>
                            </div>
                            <div class="col-md-1 text-end">
                                <button type="button" class="btn btn-danger btn-xs"
                                    @click="hapusResep(resepIndex)">
                                    &nbsp;x&nbsp;
                                </button>
                            </div>
                        </div>
                        <table class="table table-bordered bg-gray-100 mb-3">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th class="w-100px">Qty</th>
                                    <th class="w-5px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(barangItem, barangIndex) in resepItem.barang" :key="barangIndex">
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
                                                <option value="" selected>-- Tidak Ada Barang --</option>
                                                <template x-for="item in dataBarang" :key="item.id">
                                                    <option :value="item.id" :selected="barangItem.id == item.id" 
                                                        x-text="`${item.nama} (Rp. ${new Intl.NumberFormat('id-ID').format(item.harga)} / ${item.satuan})`">
                                                    </option>
                                                </template>
                                            </select>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="0" step="1"
                                                x-model.number="barangItem.qty" autocomplete="off">
                                        </td>
                                        <td class="w-10px align-middle">
                                            <button type="button" class="btn btn-warning btn-sm"
                                                @click="hapusBarang(resepIndex, barangIndex)">
                                                <i class="fa fa-times"></i>
                                            </button>
                                        </td>
                                    </tr>
                                </template>
                                <tr>
                                    <td colspan="3">
                                        <div class="text-center">
                                            <button class="btn btn-secondary" type="button"
                                                @click="tambahBarang(resepIndex)">
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
                <div class="text-center">
                    <button type="button" class="btn btn-info" @click="tambahResep()">
                        Tambah Resep
                    </button>
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
        </div>
    </form>
    <x-modal.cetak judul='Nota' />
</div>

@push('scripts')
    <script>
        function resepObatForm() {
            return {
                resep: @js($resep),
                dataBarang: @js($dataBarang),

                tambahResep() {
                    this.resep.push({
                        barang: [],
                        catatan: '',
                        nama: ''
                    });
                },

                hapusResep(resepIndex) {
                    this.resep.splice(resepIndex, 1);
                },

                tambahBarang(resepIndex) {
                    this.resep[resepIndex].barang.push({
                        id: null,
                        qty: 1,
                        harga: 0,
                        subtotal: 0,
                    });
                },

                hapusBarang(resepIndex, barangIndex) {
                    this.resep[resepIndex].barang.splice(barangIndex, 1);
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
                },

                syncToLivewire() {
                    // Sync data to Livewire
                    if (window.Livewire && window.Livewire.find) {
                        let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            let $wire = window.Livewire.find(componentId);
                            if ($wire && typeof $wire.set === 'function') {
                                $wire.set('resep', JSON.parse(JSON.stringify(this.resep)), false);
                            }
                        }
                    }
                }
            }
        }
    </script>
@endpush
