<div>
    @section('title', 'Kunjungan Pasien')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item">Statistik</li>
        <li class="breadcrumb-item active">Kunjungan Pasien</li>
    @endsection

    <!-- BEGIN page-header -->
    <h1 class="page-header">Kunjungan Pasien</h1>
    <!-- END page-header -->

    <div class="panel panel-inverse" data-sortable-id="table-basic-2">
        <!-- BEGIN panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <a href="javascript:;" wire:click="print" x-init="$($el).on('click', function() {
                setTimeout(() => {
                    $('#modal-cetak').modal('show')
                }, 1000)
            })" class="btn btn-outline-info btn-block">
                Cetak</a>&nbsp;
            <div class="ms-auto d-flex align-items-center">
                <select class="form-control w-auto" wire:model.lazy="sort">
                    <option value="qty">Desc Qty</option>
                    <option value="biaya">Desc Biaya</option>
                </select>&nbsp;
                <input type="date" autocomplete="off" min="2025-11-29" max="{{ date('Y-m-d') }}"
                    wire:model.lazy="tanggal1" class="form-control w-auto">&nbsp;s/d&nbsp;
                <input type="date" autocomplete="off" min="2025-11-29" max="{{ date('Y-m-d') }}"
                    wire:model.lazy="tanggal2" class="form-control w-auto">
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.statistik.kunjunganpasien.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="Kunjungan Pasien" />

    <div wire:loading>
        <x-loading />
    </div>
</div>
