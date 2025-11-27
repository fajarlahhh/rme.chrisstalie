<div>
    @section('title', 'Rekap Transaksi Barang')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Rekap Transaksi Barang</li>
    @endsection

    <h1 class="page-header">Rekap Transaksi Barang</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <a href="javascript:;" wire:click="print" x-init="$($el).on('click', function() {
                setTimeout(() => {
                    $('#modal-cetak').modal('show')
                }, 1000)
            })" class="btn btn-warning">
                Cetak</a>
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input type="month" class="form-control w-auto" wire:model.lazy="bulan">
                    &nbsp;
                    <select class="form-control w-auto" wire:model.lazy="persediaan">
                        <option value="">Semua Persediaan</option>
                        <option value="Apotek">Persediaan Apotek</option>
                        <option value="Klinik">Persediaan Klinik</option>
                    </select>&nbsp;
                    <select class="form-control w-auto" wire:model.lazy="kode_akun_id">
                        <option value="">Semua Kategori</option>
                        @foreach ($dataKodeAkun as $item)
                            <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                        @endforeach
                    </select>&nbsp;
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">            
            @include('livewire.laporan.barangdagang.rekaptransaksi.cetak', ['cetak' => false])
        </div>
    </div>
    <x-alert />
    <x-modal.cetak judul="Laporan Rekap Transaksi Barang Dagang" />
</div>
