<div>
    @section('title', 'Konsinyasi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Konsinyasi</li>
    @endsection

    <h1 class="page-header">Konsinyasi</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <a href="javascript:;" wire:click="export" class="btn btn-warning">
                Export</a>&nbsp;
                    <input class="form-control" type="date" wire:model.lazy="date1" />
                    &nbsp;s/d&nbsp;
                    <input class="form-control" type="date" wire:model.lazy="date2" />
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.konsinyasi.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="" />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
