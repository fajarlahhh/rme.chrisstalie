<div>
    @section('title', 'Barang Keluar')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Barang Keluar</li>
    @endsection

    <h1 class="page-header">Barang Keluar</h1>
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
                <select class="form-control w-auto" wire:model.lazy="persediaan">
                    <option value="">Semua Persediaan</option>
                    <option value="Apotek">Apotek</option>
                    <option value="Klinik">Klinik</option>
                </select>&nbsp;
                <select class="form-control w-auto" wire:model.lazy="jenis">
                    <option value="perbarang">Per Barang</option>
                    <option value="perhargajual">Per Harga Jual</option>
                    <option value="pertanggalkedaluarsa">Per Tanggal Kedaluarsa</option>
                </select>&nbsp;
                <input type="date" min="2025-11-29" max="{{ date('Y-m-d') }}" wire:model.lazy="tanggal1"
                    class="form-control w-auto">&nbsp;s/d&nbsp;
                <input type="date" min="2025-11-29" max="{{ date('Y-m-d') }}" wire:model.lazy="tanggal2"
                    class="form-control w-auto">
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.barangdagang.barangkeluar.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="Laporan Barang Keluar" />

    <div wire:loading>
        <x-loading />
    </div>
</div>
