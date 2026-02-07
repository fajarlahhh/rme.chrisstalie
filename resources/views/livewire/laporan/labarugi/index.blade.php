<div>
    @section('title', 'Laba Rugi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Laba Rugi</li>
    @endsection

    <h1 class="page-header">Laba Rugi</h1>

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
                    <input class="form-control" type="month" wire:model.lazy="month" min="2025-11" max="{{ date('Y-m') }}" />
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.labarugi.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="" />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
