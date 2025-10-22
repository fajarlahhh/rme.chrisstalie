<div x-data="permintaanForm()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Permintaan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Permintaan</li>
        <li class="breadcrumb-item active">Form</li>
    @endsection

    <h1 class="page-header">Permintaan</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
            <div class="panel-body">
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
                                        <td wire:ignore>
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
                    <label class="form-label">Kirim Ke Verifikator</label>
                    <div wire:ignore>
                        <select class="form-control" x-model="verifikator_id" x-init="$($el).select2({
                            width: '100%',
                            dropdownAutoWidth: true
                        });
                        $($el).on('change', function(e) {
                            verifikator_id = e.target.value;
                        });
                        $watch('verifikator_id', (value) => {
                            if (value !== $($el).val()) {
                                $($el).val(value).trigger('change');
                            }
                        });"
                            wire:model="verifikator_id">
                            <option value="">-- Tidak Ada Verifikator --</option>
                            @foreach ($dataPengguna as $subRow)
                                <option value="{{ $subRow['id'] }}">
                                    {{ $subRow['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('verifikator_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <a href="{{ $previous }}" class="btn btn-danger" wire:ignore>Batal</a>
            </div>
        </form>
    </div>
    <x-alert />
    <x-modal.cetak judul="Nota" />
</div>

<script>
    function permintaanForm() {
        return {
            deskripsi: @js($deskripsi),
            verifikator_id: @js($verifikator_id),
            barang: @js($barang).map(row => ({
                ...row
            })),
            dataBarang: @json($dataBarang),
            tambahBarang() {
                this.barang.push({
                    id: '',
                    satuan: '',
                    qty: 1,
                    barangSatuan: [],
                });
            },
            hapusBarang(index) {
                this.barang.splice(index, 1);
            },
            updateBarang(index) {
                let id = this.barang[index].id;
                let selectedBarang = this.dataBarang.find(i => i.id == id);
                this.barang[index].satuan = '';
                this.barang[index].barangSatuan = selectedBarang ? selectedBarang.barangSatuan : [];
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
                            $wire.set('verifikator_id', this.verifikator_id, false);
                            $wire.set('deskripsi', this.deskripsi, false);
                        }
                    }
                }
            },
        }
    }
</script>
