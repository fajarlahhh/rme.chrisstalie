<div x-data="form()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Stok Masuk')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Stok Masuk</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Stok Masuk <small>Tambah</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <x-alert />
                <div class="alert alert-warning">
                    Pesanan yang bisa dipilih adalah pesanan yang sudah lengkap stok masuk dan belum dibuat tagihannya.
                </div>
                <div class="mb-3" wire:ignore>
                    <label class="form-label">No. Pemesanan</label>
                    <select class="form-control" x-init="window.initSelect2 = (el) => {
                        $(el).select2({
                            templateResult: state => !state.id ? state.text : state.text,
                            width: '100%',
                            dropdownAutoWidth: true,
                            placeholder: '-- Cari Pemesanan --'
                        });
                        $(el).on('change', function(e) {
                            $wire.set('pengadaan_pemesanan_id', e.target.value);
                        });
                    };
                    initSelect2($el);" wire:model="pengadaan_pemesanan_id">
                        <option selected value="" hidden>-- Cari Data Pembelian --</option>
                        @foreach ($dataPemesanan as $row)
                            <option value="{{ $row['id'] }}">
                                {{ $row['nomor'] }}, {{ $row['supplier']['nama'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">No. Invoice/Faktur</label>
                    <input class="form-control" type="text" wire:model="no_faktur" x-model="no_faktur" required />
                    @error('no_faktur')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Tagihan</label>
                    <input class="form-control" type="date" wire:model="tanggal" x-model="tanggal"
                        max="{{ date('Y-m-d') }}" required />
                    @error('tanggal')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Jatuh Tempo</label>
                    <input class="form-control" type="date" wire:model="jatuh_tempo" x-model="jatuh_tempo"
                        min="{{ date('Y-m-d') }}" required />
                    @error('jatuh_tempo')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="note alert-secondary mb-0">
                    <div class="note-content table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Barang/Item</th>
                                    <th class="w-150px">Satuan</th>
                                    <th class="w-100px">Qty</th>
                                    <th class="w-150px">Harga Beli</th>
                                    <th class="w-150px">Sub Total</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barang as $index => $brg)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $brg['nama'] }}"
                                                disabled autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $brg['satuan'] }}"
                                                disabled autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $brg['qty'] }}"
                                                disabled autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-end"
                                                value="{{ number_format($brg['harga_beli']) }}" disabled
                                                autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control text-end"
                                                value="{{ number_format($brg['qty'] * $brg['harga_beli']) }}" disabled>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total Harga Barang</th>
                                    <td>
                                        <input type="text" class="form-control text-end"
                                            :value="formatNumber(totalHargaBarang)" disabled>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="form-label">Diskon <small>(Rp.)</small></label>
                    <input class="form-control" type="number" wire:model="diskon" x-model="diskon">
                    @error('diskon')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">PPN <small>(Rp.)</small></label>
                    <input class="form-control" type="number" wire:model="ppn" x-model="ppn">
                    @error('ppn')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Total Tagihan <small>(Rp.)</small></label>
                    <input class="form-control" type="text"
                        :value="formatNumber((+totalHargaBarang) - (+diskon) + (+ppn))" disabled>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" wire:model="catatan" x-model="catatan"></textarea>
                    @error('catatan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Submit
                    </button>
                @endrole
                <button type="button" onclick="window.location.href='/manajemenstok/pengadaanbrgdagang/stokmasuk'"
                    class="btn btn-danger" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Kembali
                </button>
            </div>
        </form>
    </div>

    <x-alert />

    <div wire:loading>
        <x-loading />
    </div>
</div>
<script>
    function form() {
        return {
            tanggal: @js($tanggal ?? ''),
            no_faktur: @js($no_faktur ?? ''),
            jatuh_tempo: @js($jatuh_tempo ?? ''),
            diskon: @js($diskon ?? 0),
            ppn: @js($ppn ?? 0),
            catatan: @js($catatan ?? ''),
            barang: @json($barang ?? []),
            errors: {},
            totalHargaBarang: @js($totalHargaBarang ?? 0),
            formatNumber(num) {
                if (isNaN(num)) return '0';
                return Number(num).toLocaleString();
            },
            updateHargaBeli(index) {
                this.hitungTotal();
            },
            syncToLivewire() {
                if (window.Livewire && window.Livewire.find) {
                    let componentId = this.$root?.closest('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        let $wire = window.Livewire.find(componentId);
                        if ($wire && typeof $wire.set === 'function') {
                            $wire.set('tanggal', this.tanggal, false);
                            $wire.set('no_faktur', this.no_faktur, false);
                            $wire.set('jatuh_tempo', this.jatuh_tempo, false);
                            $wire.set('diskon', +this.diskon || 0, false);
                            $wire.set('ppn', +this.ppn || 0, false);
                            $wire.set('catatan', this.catatan, false);
                            $wire.set('barang', JSON.parse(JSON.stringify(this.barang)), false);
                        }
                    }
                }
            },
            init() {}
        }
    }

    // Blok di bawah ini dulunya dinonaktifkan, sekarang SUDAH diperbaiki agar bekerja di Alpine 3:
    document.addEventListener('set-total-harga-barang', function(event) {
        const val = event?.detail?.value ?? 0;
        let root = document.querySelector('[x-data]');
        if (root) {
            // Mendapatkan data Alpine sesuai versi terbaru (Alpine 3)
            let alpineData = Alpine?.closestDataStack ? Alpine.closestDataStack(root)?.[0] : root.__x?.$data;
            if (alpineData && 'totalHargaBarang' in alpineData) {
                alpineData.totalHargaBarang = val;
            }
        }
    });
</script>
