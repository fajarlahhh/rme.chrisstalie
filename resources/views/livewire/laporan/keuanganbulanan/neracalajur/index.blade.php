<div>
    @section('title', 'Neraca Lajur')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Neraca Lajur</li>
    @endsection

    <!-- BEGIN page-header -->
    <h1 class="page-header">Neraca Lajur</h1>
    <!-- END page-header -->

    <div class="panel panel-inverse" data-sortable-id="table-basic-2">
        <!-- BEGIN panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <a href="javascript:;" wire:click="export" class="btn btn-outline-success btn-block">
                Export</a>&nbsp;
            <div class="ms-auto d-flex align-items-center">
                <input type="month" autocomplete="off" wire:model.lazy="bulan" min="2025-09"
                    max="{{ date('Y-m', strtotime('-1 month')) }}" class="form-control w-auto">
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.keuanganbulanan.neracalajur.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="Neraca Lajur" />

    <div wire:loading>
        <x-loading />
    </div>
</div>
