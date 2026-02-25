<div x-data="permintaanForm()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Permintaan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Permintaan</li>
        <li class="breadcrumb-item active">Form</li>
    @endsection

    <h1 class="page-header">Permintaan <small>Pengadaan Barang Dagang</small></h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label mb-2">Jenis Barang</label>
                    <br>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="jenis_barang_apotek" name="jenis_barang"
                            value="Persediaan Apotek" x-model="jenis_barang" @change="updatedJenisBarang()">
                        <label class="form-check-label" for="jenis_barang_apotek">Persediaan Apotek</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" id="jenis_barang_alat" name="jenis_barang"
                            value="Alat Dan Bahan" wire:model.live="jenis_barang" x-model="jenis_barang"
                            @change="updatedJenisBarang()">
                        <label class="form-check-label" for="jenis_barang_alat">Alat Dan Bahan</label>
                    </div>
                    @role('administrator|supervisor')
                        <div class="form-check form-check-inline">
                            <input class="form-check-input" type="radio" id="jenis_barang_khusus" name="jenis_barang"
                                value="Barang Khusus" wire:model.live="jenis_barang" x-model="jenis_barang"
                                @change="updatedJenisBarang()">
                            <label class="form-check-label" for="jenis_barang_khusus">Barang Khusus</label>
                        </div>
                    @endrole
                    @error('jenis_barang')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" wire:model="deskripsi" x-model="deskripsi"></textarea>
                    @error('deskripsi')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="note alert-secondary mb-3">
                    <div class="note-content table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th class="w-200px">Qty</th>
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
                                                        updateBarang(index);
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
                                                x-model.number="row.qty" autocomplete="off">
                                            @error('barang.' . $index . '.qty')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <a href="javascript:;" class="btn btn-danger" @click="hapusBarang(index)">
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
                                    <td colspan="4">
                                        <div class="text-center">
                                            <a class="btn btn-secondary" href="javascript:;" @click="tambahBarang">
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
                <div class="mb-3">
                    <input type="checkbox" class="form-check-input" id="kirim" x-model="kirim" value="1"
                        wire:model="kirim">
                    <label class="form-label" for="kirim">&nbsp;Kirim Ke Verifikator</label>
                    @error('kirim')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="button" x-init="$($el).on('click', function() {
                        $('#modal-konfirmasi').modal('show');
                    })" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Submit
                    </button>
                @endrole
                <button type="button" onclick="window.location.href='/manajemenstok/pengadaanbrgdagang/permintaan'"
                    class="btn btn-danger" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
                <x-alert />
            </div>

            <x-modal.konfirmasi />
        </form>
    </div>
    <x-modal.cetak judul="Nota" />

    <div wire:loading>
        <x-loading />
    </div>
</div>

<script>
    function permintaanForm() {
        return {
            deskripsi: @js($deskripsi),
            jenis_barang: @js($jenis_barang),
            barang: @js($barang),
            kirim: @js($kirim),
            dataBarang: @json($dataBarang),
            tambahBarang() {
                this.barang.push({
                    id: '',
                    satuan: '',
                    qty: 1,
                    barangSatuan: [],
                });
            },
            updatedJenisBarang() {
                if (window.Livewire && window.Livewire.find) {
                    let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        let $wire = window.Livewire.find(componentId);
                        if ($wire && typeof $wire.call === 'function') {
                            $wire.call('getBarang', this.jenis_barang)
                                .then(result => {
                                    this.dataBarang = result;
                                });
                        }
                    }
                } else {
                    this.dataBarang = @json($dataBarang);
                }
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
            updateBarang(index) {
                let id = this.barang[index].id;
                let selectedBarang = this.dataBarang.find(i => i.id == id);
                this.barang[index].satuan = '';
            },
            init() {},
            syncToLivewire() {
                // sinkronkan data ke livewire
                if (window.Livewire && window.Livewire.find) {
                    let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        let $wire = window.Livewire.find(componentId);
                        if ($wire && typeof $wire.set === 'function') {
                            $wire.set('barang', JSON.parse(JSON.stringify(this.barang)), false);
                            $wire.set('kirim', this.kirim, false);
                            $wire.set('jenis_barang', this.jenis_barang, false);
                            $wire.set('dataBarang', JSON.parse(JSON.stringify(this.dataBarang)), false);
                            $wire.set('deskripsi', this.deskripsi, false);
                        }
                    }
                }
            },
        }
    }
</script>
