<div>
    @section('title', 'Data ICD 10')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">ICD 10</li>
    @endsection

    <h1 class="page-header">ICD 10</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            @role('administrator|supervisor|operator')
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                    class="btn btn-primary">
                    Tambah</a>&nbsp;
                <a href="javascript:;" wire:click="export" class="btn btn-success">
                    Export</a>&nbsp;
            @endrole
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari">
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.datamaster.icd10.table', ['cetak' => false])
        </div>
        <div class="panel-footer">
            {{ $data->links() }}
        </div>
    </div>
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
