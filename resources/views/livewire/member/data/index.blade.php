<div>
    @section('title', 'Member')

    @section('breadcrumb')
        <li class="breadcrumb-item">Member</li>
        <li class="breadcrumb-item active">Data</li>
    @endsection

    <h1 class="page-header">Member <small>Data</small></h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'" class="btn btn-outline-secondary btn-block">
                Tambah</a>&nbsp;
            <a href="javascript:;" wire:click="export" class="btn btn-outline-success btn-block">
                Export</a>&nbsp;
            <div class="ms-auto d-flex align-items-center">
                <input type="text" class="form-control w-auto" placeholder="Cari" autocomplete="off"
                    wire:model.lazy="cari">
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.member.data.tabel', ['cetak' => false])
        </div>
        <div class="panel-footer">
            {{ $data->links() }}
        </div>
    </div>

    <div wire:loading>
        <x-loading />
    </div>
</div>
