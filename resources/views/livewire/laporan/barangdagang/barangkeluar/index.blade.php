<div>
    @section('title', 'Barang Keluar')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Barang Keluar</li>
    @endsection

    <h1 class="page-header">Barang Keluar</h1>
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
                    <select class="form-control w-auto" wire:model.lazy="jenis">
                        <option value="pertanggalkeluar">Per Tanggal Keluar</option>
                        <option value="pertanggalkedaluarsa">Per Tanggal Kedaluarsa</option>
                        <option value="perbarang">Per Barang</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">            
            @include('livewire.laporan.barangdagang.barangkeluar.cetak', ['cetak' => false])
        </div>
    </div>
    <x-alert />
    <x-modal.cetak judul="Laporan Barang Keluar" />
</div>
