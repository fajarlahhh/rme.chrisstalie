<div>
    @section('title', 'Barang')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Tarif Tindakan</li>
    @endsection

    <h1 class="page-header">Tarif Tindakan</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            @role('administrator|supervisor|operator')
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                    class="btn btn-primary">
                    Tambah</a>&nbsp;
            @endrole
            <a href="javascript:;" wire:click="export" class="btn btn-success">
                Export</a>
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <select class="form-control w-auto" wire:model.lazy="kode_akun_id">
                        <option value="">Semua Kategori</option>
                        @foreach ($dataKodeAkun as $item)
                            <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                        @endforeach
                    </select>&nbsp;
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.datamaster.tariftindakan.tabel', ['cetak' => false])
        </div>
        <div class="panel-footer">
            {{ $data->links() }}
        </div>
    </div>
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
