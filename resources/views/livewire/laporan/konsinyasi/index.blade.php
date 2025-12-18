<div>
    @section('title', 'Konsinyasi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Konsinyasi</li>
    @endsection

    <h1 class="page-header">Konsinyasi</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <a href="javascript:;" wire:click="export" class="btn btn-warning">
                Export</a>
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input class="form-control" type="date" wire:model.lazy="date1" />
                    &nbsp;s/d&nbsp;
                    <input class="form-control" type="date" wire:model.lazy="date2" />
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            @include('livewire.laporan.konsinyasi.cetak', ['cetak' => false])
        </div>
    </div>
    <x-alert />
    <x-modal.cetak judul="" />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
