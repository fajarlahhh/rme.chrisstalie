<div>
    @section('title', 'Laporan Laba Rugi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Laba Rugi</li>
    @endsection

    <!-- BEGIN page-header -->
    <h1 class="page-header">Laba Rugi</h1>
    <!-- END page-header -->

    <div class="panel panel-inverse" data-sortable-id="table-basic-2">
        <!-- BEGIN panel-heading -->
        <div class="panel-heading">
            <a href="javascript:;" wire:click="cetak" x-init="$($el).on('click', function() {
                setTimeout(() => {
                    $('#modal-cetak').modal('show')
                }, 1000)
            })" wire:loading.remove class="btn btn-indigo">
                Cetak</a>
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input type="month" autocomplete="off" wire:model.lazy="bulan" min="2025-09"
                        max="{{ date('Y-m', strtotime('-1 month')) }}" class="form-control w-auto">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.keuanganbulanan.labarugi.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="Laba Rugi" />

    <div wire:loading>
        <x-loading />
    </div>
</div>
