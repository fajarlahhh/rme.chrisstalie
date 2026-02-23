<div>
    @section('title', 'Laporan Neraca')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Neraca</li>
    @endsection

    <!-- BEGIN page-header -->
    <h1 class="page-header">Neraca</h1>
    <!-- END page-header -->

    <div class="panel panel-inverse" data-sortable-id="table-basic-2">
        <!-- BEGIN panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <a href="javascript:;" wire:click="cetak" x-init="$($el).on('click', function() {
                setTimeout(() => {
                    $('#modal-cetak').modal('show')
                }, 1000)
            })" wire:loading.remove class="btn btn-indigo">
                Cetak</a>&nbsp;
                    <input type="month" autocomplete="off" wire:model.lazy="bulan" min="2025-09"
                        max="{{ date('Y-m', strtotime('-1 month')) }}" class="form-control w-auto">
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.keuanganbulanan.neraca.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="Neraca" />

    <div wire:loading>
        <x-loading />
    </div>
</div>
