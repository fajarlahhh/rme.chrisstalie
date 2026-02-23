<div>
    @section('title', 'Pengadaan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Pengadaan</li>
    @endsection

    <h1 class="page-header"> Pengadaan</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <a href="javascript:;" wire:click="print" x-init="$($el).on('click', function() {
                setTimeout(() => {
                    $('#modal-cetak').modal('show')
                }, 1000)
            })" class="btn btn-warning">
                Cetak</a>&nbsp;
                    <input class="form-control" type="date" min="2025-11-29" max="{{ date('Y-m-d') }}" wire:model.lazy="date1" />
                    &nbsp;s/d&nbsp;
                    <input class="form-control" type="date" min="2025-11-29" max="{{ date('Y-m-d') }}" wire:model.lazy="date2" />
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.pengadaanbrgdagang.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="" />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
