<div>
    @section('title', 'Barang Dagang')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Barang Dagang</li>
    @endsection

    <h1 class="page-header">Barang Dagang</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            @role('administrator|supervisor|operator')
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                    class="btn btn-outline-secondary btn-block">Tambah</a>&nbsp;
            @endrole
            <a href="javascript:;" wire:click="export" class="btn btn-outline-success btn-block">
                Export</a>&nbsp;
            <div class="ms-auto d-flex align-items-center">
                <select class="form-control w-auto" wire:model.lazy="persediaan">
                    <option value="">Semua Persediaan</option>
                    <option value="Apotek">Apotek</option>
                    <option value="Klinik">Klinik</option>
                </select>&nbsp;
                <select class="form-control w-auto" wire:model.lazy="kode_akun_id">
                    <option value="">Semua Kategori</option>
                    @foreach ($dataKodeAkun as $item)
                        <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                    @endforeach
                </select>&nbsp;
                <input type="text" class="form-control w-auto" placeholder="Cari" autocomplete="off"
                    wire:model.lazy="cari">
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.datamaster.barangdagang.tabel', ['cetak' => false])
        </div>
        <div class="panel-footer">
            {{ $data->links() }}
        </div>
    </div>

    <div wire:loading>
        <x-loading />
    </div>
</div>
