<div>
    @section('title', 'Pegawai')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item active">Pegawai</li>
    @endsection

    <h1 class="page-header">Pegawai</h1>
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
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.kepegawaian.pegawai.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="Pegawai" />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
