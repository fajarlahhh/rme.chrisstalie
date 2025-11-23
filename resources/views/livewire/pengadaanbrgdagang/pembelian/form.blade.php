<div x-data="pembelianForm()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Pembelian')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Pembelian</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Pembelian <small>Tambah</small></h1>

    <div class="note alert-primary mb-2">
        <div class="note-content">
            <h5>Detail Permintaan</h5>
            <hr>
            <table class="w-100">
                <tr>
                    <td class="w-150px">Deskripsi</td>
                    <td class="w-10px">:</td>
                    <td>{{ $data->deskripsi }}</td>
                </tr>
                <tr>
                    <td class="w-150px">Tanggal</td>
                    <td class="w-10px">:</td>
                    <td>{{ $data->created_at }}</td>
                </tr>
                <tr>
                    <td class="w-150px">Pengguna</td>
                    <td class="w-10px">:</td>
                    <td>{{ $data->pengguna?->nama }}</td>
                </tr>
            </table>
        </div>
    </div>

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
                    <label class="form-label">Uraian/No. Faktur</label>
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
                                        <th class="w-200px">Satuan</th>
                                        <th class="w-150px">Qty</th>
                                        <th class="w-150px">Harga Beli</th>
                                        <th class="w-150px">Sub Total</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <template x-for="(row, index) in barang" :key="index">
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control" min="0" step="1"
                                                    :value="row.nama" disabled autocomplete="off">
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" min="0" step="1"
                                                    :value="row.satuan" disabled autocomplete="off">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" min="0" step="1"
                                                    :value="row.qty" disabled autocomplete="off">
                                            </td>
                                            <td>
                                                <input type="number" class="form-control" min="0" step="1"
                                                    x-model="row.harga_beli" @input="hitungTotal()">
                                                <template x-if="errors && errors[`barang.${index}.harga_beli`]">
                                                    <span class="text-danger"
                                                        x-text="errors[`barang.${index}.harga_beli`]"></span>
                                                </template>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control text-end" min="0"
                                                    step="1" :value="formatNumber(row.qty * row.harga_beli)"
                                                    disabled autocomplete="off">
                                            </td>
                                        </tr>
                                    </template>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="4" class="text-end">Total Harga Barang</th>
                                        <td>
                                            <input type="text" class="form-control text-end" min="0"
                                                step="1" :value="formatNumber(totalHargaBeli)" disabled
                                                autocomplete="off">
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
                <button type="button" onclick="window.location.href='/pengadaanbrgdagang/pembelian'"
                    class="btn btn-danger" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
            </div>
        </form>
    </div>
    <x-alert />
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
