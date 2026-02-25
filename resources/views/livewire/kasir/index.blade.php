<div x-data="form()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Kasir')

    @section('breadcrumb')
        <li class="breadcrumb-item active">Kasir</li>
    @endsection

    <h1 class="page-header">Kasir</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
            <div class="panel-body">
                @if (!$registrasi)
                    <div class="row">
                        <div class="col-md-6 col-lg-8">
                            <div class="mb-3">
                                <label class="form-label">Cari Pasien <small>(Penjualan Bebas)</small></label>
                                <div wire:ignore wire:loading.attr="hidden">
                                    <select class="form-control" x-init="$($el).select2({
                                        width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                                        dropdownAutoWidth: true,
                                        templateResult: format,
                                        minimumInputLength: 3,
                                        dataType: 'json',
                                        ajax: {
                                            url: '/cari/pasien',
                                            data: function(params) {
                                                var query = {
                                                    cari: params.term
                                                }
                                                return query;
                                            },
                                            processResults: function(data, params) {
                                                return {
                                                    results: data,
                                                };
                                            },
                                            cache: true
                                        }
                                    });
                                    
                                    $($el).on('change', function(element) {
                                        $wire.set('pasien_id', $($el).val());
                                    });
                                    
                                    function format(data) {
                                        if (!data.id) {
                                            return data.text;
                                        }
                                        var $data = $('<table><tr><th>No. RM</th><th>:</th><th>' + data.rm + '</th></tr>' +
                                            '<tr><th>No. KTP</th><th>:</th><th>' + data.nik + '</th></tr>' +
                                            '<tr><th>Nama</th><th>:</th><th>' + data.nama + '</th></tr>' +
                                            '<tr><th>Alamat</th><th>:</th><th>' + data.alamat + '</th></tr></table>');
                                        return $data;
                                    }">
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6 col-lg-4">
                            <div class="mt-md-4 mb-3">
                                <button type="button" wire:loading.attr="disabled"
                                    wire:click="getDataPasienTindakanResepObat" class="btn btn-primary w-100">
                                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                                    Pasien Tindakan/Resep Obat
                                </button>
                            </div>
                        </div>
                    </div>
                @else
                    @include('livewire.klinik.informasipasien', ['data' => $registrasi])
                @endif
                <div class="alert alert-light table-responsive border">
                    <div class="table-responsive">
                        <table class="table table-hover table-bordered">
                            <tbody>
                                @include('livewire.kasir.tindakan')
                                @include('livewire.kasir.resep')
                                @include('livewire.kasir.barang')
                            </tbody>
                        </table>
                    </div>
                </div>
                @include('livewire.kasir.pembayaran')
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
                <button type="button" onclick="window.location.href='/kasir/data'" class="btn btn-warning"
                    wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Data
                </button>
                <button type="button" onclick="window.location.href='/kasir'" class="btn btn-indigo"
                    wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Reset
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
    @include('livewire.kasir.pending')
</div>

@push('scripts')
    <script>
        document.addEventListener('livewire:initialized', () => {
            window.Livewire.on('pasien-tindakan-resep-obat', (data) => {
                $('#modal-pasien').modal('show');
            });
            window.Livewire.on('pasien-tindakan', (data) => {
                this.tindakan = data;
            });
            window.Livewire.on('pasien-resep', (data) => {
                this.resep = data;
            });
        });

        document.addEventListener('set-tindakan', function(event) {
            const data = event?.detail?.data ?? [];
            let root = document.querySelector('[x-data]');
            if (root) {
                let alpineData = Alpine?.closestDataStack ? Alpine.closestDataStack(root)?.[0] : root.__x?.$data;
                if (alpineData && 'tindakan' in alpineData) {
                    alpineData.tindakan = data;
                    alpineData.hitungTotalTindakan();
                }
            }
        });

        document.addEventListener('set-resep', function(event) {
            const data = event?.detail?.data ?? [];
            let root = document.querySelector('[x-data]');
            if (root) {
                let alpineData = Alpine?.closestDataStack ? Alpine.closestDataStack(root)?.[0] : root.__x?.$data;
                if (alpineData && 'resep' in alpineData) {
                    alpineData.resep = data;
                    alpineData.hitungTotalResep();
                }
            }
        });

        function form() {
            return {
                barang: @js($barang).map(row => ({
                    ...row
                })),
                tanggal: @js($tanggal ?? ''),
                dataBarangApotek: Array.isArray(@js($dataBarangApotek)) ? @js($dataBarangApotek) : Object
                    .values(@js($dataBarangApotek)),
                dataNakes: @js($dataNakes),
                dataMetodeBayar: @js($dataMetodeBayar ?? []),
                total_diskon_tindakan: @js($total_diskon_tindakan),
                total_diskon_barang: @js($total_diskon_barang),
                total_barang: @js($total_barang),
                total_tindakan: @js($total_tindakan),
                total_resep: @js($total_resep),
                total_tagihan: @js($total_tagihan),
                tindakan: @js($tindakan),
                resep: @js($resep),
                cash: @js($cash),
                cash_2: @js($cash_2),
                keterangan: @js($keterangan),
                metode_bayar: @js($metode_bayar),
                metode_bayar_2: @js($metode_bayar_2),
                formatNumber(val) {
                    if (val === null || val === undefined || isNaN(val)) return '0';
                    return `${new Intl.NumberFormat().format(val)}`;
                },
                hitungTotalTindakan() {
                    this.total_tindakan = this.tindakan.reduce((sum, row) => {
                        return sum + (row.biaya * row.qty - row.diskon);
                    }, 0);
                    this.total_diskon_tindakan = this.tindakan.reduce((sum, row) => {
                        return sum + row.diskon;
                    }, 0);
                    this.hitungTotalTagihan();
                },
                hapusResep(index) {
                    this.resep.splice(index, 1);
                    hitungTotalResep();
                    this.hitungTotalTagihan();
                },
                hitungTotalResep() {
                    this.total_resep = this.resep.reduce((sum, row) => {
                        return sum + (row.barang.reduce((sum, b) => sum + (b.harga * b.qty), 0));
                    }, 0);
                    this.hitungTotalTagihan();
                },
                hitungTotalBarang(index) {
                    this.total_barang = this.barang.reduce((sum, row) => {
                        return sum + (row.harga * row.qty - row.diskon);
                    }, 0);
                    this.total_diskon_barang = this.barang.reduce((sum, row) => {
                        return sum + row.diskon;
                    }, 0);
                    this.hitungTotalTagihan();
                },
                hitungTotalTagihan() {
                    this.total_tagihan = this.total_barang + this.total_tindakan + this.total_resep;
                },
                tambahBarang() {
                    this.barang.push({
                        id: '',
                        qty: 1,
                        harga: 0,
                        diskon: 0,
                        subtotal: 0,
                        kode_akun_id: '',
                        kode_akun_penjualan_id: '',
                        kode_akun_modal_id: '',
                    });
                    this.hitungTotalTagihan();
                },
                hapusBarang(index) {
                    this.barang.splice(index, 1);
                    this.hitungTotalBarang();
                    this.hitungTotalTagihan();
                    this.$nextTick(() => {
                        this.refreshSelect2();
                    });
                },
                updateBarang(index) {
                    let row = this.barang[index];
                    let apotekArr = Array.isArray(this.dataBarangApotek) ? this.dataBarangApotek : Object.values(this
                        .dataBarangApotek);
                    let selected = apotekArr.find(b => b.id == row.id);
                    if (selected) {
                        row.harga = selected.harga;
                        row.kode_akun_id = selected.kode_akun_id;
                        row.kode_akun_penjualan_id = selected.kode_akun_penjualan_id;
                        row.kode_akun_modal_id = selected.kode_akun_modal_id;
                    } else {
                        row.harga = 0;
                        row.kode_akun_id = '';
                        row.kode_akun_penjualan_id = '';
                        row.kode_akun_modal_id = '';
                    }
                    this.hitungTotalBarang(index);
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
                syncToLivewire() {
                    // sinkronkan data ke livewire
                    if (window.Livewire && window.Livewire.find) {
                        let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            let $wire = window.Livewire.find(componentId);
                            if ($wire && typeof $wire.set === 'function') {
                                $wire.set('barang', JSON.parse(JSON.stringify(this.barang)), true);
                                $wire.set('tindakan', JSON.parse(JSON.stringify(this.tindakan)), false);
                                $wire.set('resep', JSON.parse(JSON.stringify(this.resep)), false);
                                $wire.set('total_tagihan', this.total_tagihan, true);
                                $wire.set('total_tindakan', this.total_tindakan, true);
                                $wire.set('total_resep', this.total_resep, true);
                                $wire.set('total_barang', this.total_barang, true);
                                $wire.set('total_diskon_tindakan', this.total_diskon_tindakan, true);
                                $wire.set('total_diskon_barang', this.total_diskon_barang, true);
                                $wire.set('tanggal', this.tanggal, true);
                                $wire.set('keterangan', this.keterangan, true);
                                $wire.set('cash', this.cash, true);
                                $wire.set('cash_2', this.cash_2, true);
                                $wire.set('metode_bayar', this.metode_bayar, true);
                                $wire.set('metode_bayar_2', this.metode_bayar_2, true);
                            }
                        }
                    }
                },
                init() {
                    this.hitungTotalTagihan();
                    // perhatikan perubahan barang, update total jika perlu
                    this.$watch('barang', () => {
                        this.hitungTotalTagihan();
                    }, {
                        deep: true
                    });
                }
            }
        }
    </script>
@endpush
