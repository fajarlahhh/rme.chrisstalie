<div>
    @section('title', 'Jadwal Shift')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item active">Jadwal Shift</li>
    @endsection

    <h1 class="page-header">Jadwal Shift</h1>
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
                    <input type="month" class="form-control w-auto" wire:model.lazy="bulan">&nbsp;
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            @include('livewire.laporan.kepegawaian.jadwalshift.cetak', ['cetak' => false])
        </div>
    </div>
    <x-alert />
    <x-modal.cetak judul="Jadwal Shift" />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
