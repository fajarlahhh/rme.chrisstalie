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
        <div class="panel-heading">
            <a href="javascript:;" wire:click="export" class="btn btn-success">
                Export</a>
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input type="month" autocomplete="off" wire:model.lazy="bulan" class="form-control w-auto">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            @include('livewire.laporan.keuanganbulanan.neracalajur.cetak', ['cetak' => false])
        </div>
    </div>
    <x-alert />
    <x-modal.cetak judul="Neraca Lajur" />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
