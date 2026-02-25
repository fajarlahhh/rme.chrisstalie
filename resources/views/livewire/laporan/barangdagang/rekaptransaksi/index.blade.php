<div>
    @section('title', 'Rekap Transaksi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Rekap Transaksi</li>
    @endsection

    <h1 class="page-header">Rekap Transaksi</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <a href="javascript:;" wire:click="print" x-init="$($el).on('click', function() {
                setTimeout(() => {
                    $('#modal-cetak').modal('show')
                }, 1000)
            })" class="btn btn-outline-info btn-block">
                Cetak</a>&nbsp;
            <div class="ms-auto d-flex align-items-center">
                <input type="month" class="form-control w-auto" wire:model.lazy="bulan" min="2025-11"
                    max="{{ date('Y-m') }}">
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
                <select class="form-control w-auto" wire:model.lazy="satuan">
                    <option value="Utama">Satuan Utama</option>
                    <option value="Terkecil">Satuan Terkecil</option>
                </select>&nbsp;
                <input type="text" class="form-control w-auto" placeholder="Cari" autocomplete="off"
                    wire:model.lazy="cari">
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.barangdagang.rekaptransaksi.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="Laporan Rekap Transaksi Barang Dagang" />

    <div wire:loading>
        <x-loading />
    </div>
</div>
