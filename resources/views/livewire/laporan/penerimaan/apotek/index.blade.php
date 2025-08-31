<div>
    @section('title', 'Penerimaan Apotek')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item">Penerimaan</li>
        <li class="breadcrumb-item active">Apotek</li>
    @endsection

    <h1 class="page-header">Penerimaan Apotek</h1>

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
                    <input class="form-control" type="date" wire:model.lazy="date1" />
                    &nbsp;s/d&nbsp;
                    <input class="form-control" type="date" wire:model.lazy="date2" />&nbsp;
                    <input class="form-control" type="text" wire:model.lazy="cari" />
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            @include('livewire.laporan.penerimaan.apotek.cetak', ['cetak' => false])
        </div>
    </div>
    <x-alert />
    <x-modal.cetak judul="" />
</div>
