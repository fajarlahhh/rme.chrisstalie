<div>
    @section('title', 'Pasien')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Pasien</li>
    @endsection

    <h1 class="page-header">Pasien</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <a href="javascript:;" wire:click="export" class="btn btn-outline-success btn-block">
                Export</a>&nbsp;
            <div class="ms-auto d-flex align-items-center">
                <input type="text" class="form-control w-auto" placeholder="Cari" autocomplete="off"
                    wire:model.lazy="cari">
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.datamaster.pasien.tabel', ['cetak' => false])
        </div>
        <div class="panel-footer">
            {{ $data->links() }}
        </div>
    </div>

    <div wire:loading>
        <x-loading />
    </div>
</div>
