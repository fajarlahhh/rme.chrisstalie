<div x-data="form()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Pemesanan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Pemesanan</li>
        <li class="breadcrumb-item active">Form</li>
    @endsection

    <h1 class="page-header">Pemesanan <small>Pengadaan Barang Dagang</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
            <div class="panel-body">
                <div class="alert alert-info">
                    <h4 class="alert-heading">Data Permintaan</h4>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" disabled>{{ $data->deskripsi }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input class="form-control" type="text" value="{{ $data->created_at }}" disabled />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Operator</label>
                        <input class="form-control" type="text" value="{{ $data->pengguna?->nama }}" disabled />
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Estimasi Kedatangan</label>
                    <input class="form-control" type="date" x-model="tanggal_estimasi_kedatangan"
                        min="{{ now()->format('Y-m-d') }}" required />
                    @error('tanggal_estimasi_kedatangan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Supplier</label>
                    <div wire:ignore>
                        <select class="form-control" x-init="$($el).select2({
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                        $($el).on('change', function(e) {
                            supplier_id = e.target.value;
                        });" x-model="supplier_id" required>
                            <option value="">-- Pilih Supplier --</option>
                            <template x-for="item in dataSupplier" :key="item.id">
                                <option :value="item.id" :selected="supplier_id == item.id"
                                    x-text="`${item.nama} - ${item.alamat}`">
                                </option>
                            </template>
                        </select>
                    </div>
                    @error('supplier_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Penanggung Jawab</label>
                    <div wire:ignore>
                        <select class="form-control" x-init="$($el).select2({
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                        $($el).on('change', function(e) {
                            penanggung_jawab_id = e.target.value;
                        });" x-model="penanggung_jawab_id" required>
                            <option value="">-- Pilih Penanggung Jawab --</option>
                            <template x-for="item in dataPengguna" :key="item.id">
                                <option :value="item.id" :selected="penanggung_jawab_id == item.id"
                                    x-text="item.kepegawaian_pegawai?.sipa ? `${item.nama}, SIPA : ${item.kepegawaian_pegawai.sipa}` : item.nama">
                                </option>
                            </template>
                        </select>
                    </div>
                    @error('penanggung_jawab_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="note alert-secondary mb-3">
                    <div class="note-content table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th class="w-150px">Satuan</th>
                                    <th class="w-150px">Qty Permintaan</th>
                                    <th class="w-150px">Qty Sudah Dipesan</th>
                                    <th class="w-150px">Qty Dipesan Skrg</th>
                                    <th class="w-200px">Harga Satuan</th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in barang" :key="index">
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" :value="row.nama"
                                                autocomplete="off" disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" :value="row.satuan"
                                                autocomplete="off" disabled>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" step="1" min="0"
                                                max="100" :value="row.qty_permintaan" autocomplete="off" disabled>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" step="1" min="0"
                                                max="100" :value="row.qty_sudah_dipesan" autocomplete="off"
                                                disabled>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" step="1" min="0"
                                                max="100" x-model="row.qty"
                                                :max="row.qty_permintaan - row.qty_sudah_dipesan"
                                                @input="hitungTotal()" autocomplete="off" required>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" step="1"
                                                x-model="row.harga_beli" @input="hitungTotal()">
                                        </td>
                                    </tr>
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
                            </tfoot>
                        </table>
                        @error('barang')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" x-model="catatan"></textarea>
                    @error('catatan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor')
                    <button type="button" x-init="$($el).on('click', function() {
                        $('#modal-konfirmasi').modal('show');
                    })" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Submit
                    </button>
                @endrole
                <button type="button" onclick="window.location.href='/manajemenstok/pengadaanbrgdagang/pemesanan'"
                    class="btn btn-danger" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
                <x-alert />
            </div>

            <x-modal.konfirmasi />
        </form>
    </div>

    <x-modal.cetak judul='Nota' />

    <div wire:loading>
        <x-loading />
    </div>
</div>


<script>
    function form() {
        return {
            tanggal_estimasi_kedatangan: @js($tanggal_estimasi_kedatangan ?? ''),
            dataSupplier: @json($dataSupplier ?? []),
            dataPengguna: @json($dataPengguna ?? []),
            supplier_id: @js($supplier_id ?? ''),
            penanggung_jawab_id: @js($penanggung_jawab_id ?? ''),
            barang: @json($barang ?? []),
            catatan: @js($catatan ?? ''),
            totalHargaBeli: @js($totalHargaBeli ?? 0),
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
            updateSupplier() {
                this.supplier_id = this.supplier_id.trim();
            },
            updateHargaBeli(index) {
                this.hitungTotal();
            },
            syncToLivewire() {
                console.log(this.supplier_id);
                if (window.Livewire && window.Livewire.find) {
                    let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        let $wire = window.Livewire.find(componentId);
                        if ($wire && typeof $wire.set === 'function') {
                            $wire.set('supplier_id', this.supplier_id, false);
                            $wire.set('barang', JSON.parse(JSON.stringify(this.barang)), false);
                            $wire.set('catatan', this.catatan, false);
                            $wire.set('tanggal_estimasi_kedatangan', this.tanggal_estimasi_kedatangan, false);
                            $wire.set('penanggung_jawab_id', this.penanggung_jawab_id, false);
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
