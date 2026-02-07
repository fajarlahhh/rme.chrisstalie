<div x-data="form()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Pelunasan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Pelunasan</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Pelunasan <small>Tambah</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
            <div class="panel-body">
                <x-alert />
                <div class="mb-3">
                    <label class="form-label">Supplier</label>
                    <select class="form-control" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    });" x-model="supplier" wire:model.live="supplier" >
                        <option selected value="" hidden>-- Cari Data Supplier --</option>
                        @foreach ($dataSupplier as $row)
                            <option value="{{ $row['id'] }}">
                                {{ $row['nama'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <tr>
                            <th rowspan="2" class="w-5px"></th>
                            <th rowspan="2" class="w-300px">No. Tagihan</th>
                            <th rowspan="2" class="w-150px">Tanggal</th>
                            <th rowspan="2" class="w-150px">Tgl. Jatuh Tempo</th>
                            <th rowspan="2" class="w-100px">Total Harga Barang</th>
                            <th rowspan="2" class="w-100px">PPN</th>
                            <th rowspan="2" class="w-100px">Diskon</th>
                            <th rowspan="2" class="w-100px">Total Tagihan</th>
                            <th colspan="3">Detail Barang</th>
                        </tr>
                        <tr>
                            <th>Nama Barang</th>
                            <th class="text-end">Qty</th>
                            <th class="text-end">Harga Satuan</th>
                        </tr>
                        @foreach ($pengadaanTagihan as $item)
                            <tr>
                                <td
                                    rowspan="{{ count($item['pengadaan_pemesanan']['pengadaan_pemesanan_detail']) + 1 }}">
                                    <input type="checkbox" class="form-check-input" x-model="pengadaan_tagihan_id"
                                        :value="String({{ $item['id'] ?? '' }})"
                                        @click.stop="toggleCheckedId({{ $item['id'] ?? '' }})">
                                </td>
                                <td
                                    rowspan="{{ count($item['pengadaan_pemesanan']['pengadaan_pemesanan_detail']) + 1 }}">
                                    {{ $item['no_faktur'] }}</td>
                                <td
                                    rowspan="{{ count($item['pengadaan_pemesanan']['pengadaan_pemesanan_detail']) + 1 }}">
                                    {{ $item['tanggal'] }}</td>
                                <td
                                    rowspan="{{ count($item['pengadaan_pemesanan']['pengadaan_pemesanan_detail']) + 1 }}">
                                    {{ $item['tanggal_jatuh_tempo'] }}</td>
                                <td class="text-end"
                                    rowspan="{{ count($item['pengadaan_pemesanan']['pengadaan_pemesanan_detail']) + 1 }}">
                                    {{ number_format($item['total_harga_barang'], 2) }}</td>
                                <td class="text-end"
                                    rowspan="{{ count($item['pengadaan_pemesanan']['pengadaan_pemesanan_detail']) + 1 }}">
                                    {{ number_format($item['diskon'], 2) }}</td>
                                <td class="text-end"
                                    rowspan="{{ count($item['pengadaan_pemesanan']['pengadaan_pemesanan_detail']) + 1 }}">
                                    {{ number_format($item['ppn'], 2) }}</td>
                                <td class="text-end"
                                    rowspan="{{ count($item['pengadaan_pemesanan']['pengadaan_pemesanan_detail']) + 1 }}">
                                    {{ number_format($item['total_tagihan'], 2) }}</td>
                            </tr>
                            @foreach ($item['pengadaan_pemesanan']['pengadaan_pemesanan_detail'] as $q)
                                <tr>
                                    <td>{{ $q['barang']['nama'] }}</td>
                                    <td class="text-end w-70px" nowrap>{{ $q['qty'] }}
                                        {{ $q['barang_satuan']['nama'] }}</td>
                                    <td class="text-end w-70px" nowrap>
                                        {{ number_format($q['harga_beli'], 2) }}</td>
                                </tr>
                            @endforeach
                        @endforeach
                    </table>
                </div>
                <div class="mb-3">
                    <label class="form-label">Total Tagihan</label>
                    <input class="form-control text-end" type="text" :value="formatNumber(totalTagihan)" disabled />
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal Pelunasan</label>
                    <input class="form-control" type="date" x-model="tanggal" max="{{ now()->format('Y-m-d') }}"
                        required />
                    @error('tanggal')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Metode Pembayaran</label>
                    <select class="form-control" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    });" x-model="kode_akun_pembayaran_id" wire:model="kode_akun_pembayaran_id" data-width="100%">
                        <option hidden selected>-- Pilih Metode Pembayaran --</option>
                        @foreach ($dataKodePembayaran as $item)
                            <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('kode_akun_pembayaran_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea class="form-control" x-model="catatan" required></textarea>
                    @error('catatan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="panel-footer">
                @unlessrole('guest')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endunlessrole
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
            pengadaanTagihan: @js($pengadaanTagihan),
            pengadaan_tagihan_id: @entangle('pengadaan_tagihan_id').defer,
            catatan: @entangle('catatan').defer,
            tanggal: @entangle('tanggal').defer,
            kode_akun_pembayaran_id: @entangle('kode_akun_pembayaran_id').defer,
            supplier: @entangle('supplier').defer,
            get totalTagihan() {
                let sum = 0;
                let ids = Array.isArray(this.pengadaan_tagihan_id) ? this.pengadaan_tagihan_id : (this
                    .pengadaan_tagihan_id ? [this.pengadaan_tagihan_id] : []);
                let idSet = new Set(ids.map(x => typeof x === 'string' ? Number(x) : x));
                for (const item of this.pengadaanTagihan) {
                    if (idSet.has(item.id)) {
                        sum += Number(item.total_tagihan);
                    }
                }
                return sum;
            },
            formatNumber(num) {
                return new Intl.NumberFormat('id-ID', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                }).format(num);
            },
            toggleCheckedId(id) {
                // Defensive handling for undefined, non-array, or non-initialized pengadaan_tagihan_id
                let ids = Array.isArray(this.pengadaan_tagihan_id) ? [...this.pengadaan_tagihan_id] : (this
                    .pengadaan_tagihan_id ? [this.pengadaan_tagihan_id] : []);
                id = String(id);

                const index = ids.indexOf(id);
                if (index > -1) {
                    ids.splice(index, 1);
                } else {
                    ids.push(id);
                }
                this.pengadaan_tagihan_id = ids;
            },
            init() {

            },
            syncToLivewire() {
                if (window.Livewire && window.Livewire.find) {
                    let componentId = this.$root?.closest('[wire\\:id]')?.getAttribute('wire:id');
                    if (componentId) {
                        let $wire = window.Livewire.find(componentId);
                        if ($wire && typeof $wire.set === 'function') {
                            $wire.set('pengadaan_tagihan_id', this.pengadaan_tagihan_id, false);
                            $wire.set('tanggal', this.tanggal, false);
                            $wire.set('catatan', this.catatan, false);
                            $wire.set('kode_akun_pembayaran_id', this.kode_akun_pembayaran_id, false);
                            $wire.set('supplier', this.supplier, false);
                        }
                    }
                }
            }
        }
    }

    document.addEventListener('set-total-tagihan', function(event) {
        const val = event?.detail?.value ?? 0;
        let root = document.querySelector('[x-data]');
        if (root) {
            // Mendapatkan data Alpine sesuai versi terbaru (Alpine 3)
            let alpineData = Alpine?.closestDataStack ? Alpine.closestDataStack(root)?.[0] : root.__x?.$data;
            if (alpineData && 'pengadaanTagihan' in alpineData) {
                alpineData.pengadaanTagihan = val;
            }
        }
    });
</script>
