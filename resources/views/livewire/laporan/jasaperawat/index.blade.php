<div>
    @section('title', 'Jasa Perawat')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Jasa Perawat</li>
    @endsection

    <h1 class="page-header">Jasa Perawat</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <a href="javascript:;" wire:click="export" class="btn btn-outline-success btn-block">
                Export</a>&nbsp;
            <div class="ms-auto d-flex align-items-center">
                <input class="form-control w-auto" type="date" min="2025-11-29" max="{{ date('Y-m-d') }}"
                    wire:model.lazy="tanggal1" />
                &nbsp;s/d&nbsp;
                <input class="form-control w-auto" type="date" min="2025-11-29" max="{{ date('Y-m-d') }}"
                    wire:model.lazy="tanggal2" />
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.jasaperawat.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="" />

    <div wire:loading>
        <x-loading />
    </div>
</div>
