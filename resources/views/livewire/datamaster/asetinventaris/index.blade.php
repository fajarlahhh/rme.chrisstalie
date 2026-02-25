<div>
    @section('title', 'Data Aset/Inventaris')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Aset/Inventaris</li>
    @endsection

    <h1 class="page-header">Aset/Inventaris</h1>
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
                <select class="form-control w-auto" wire:model.lazy="kode_akun_id">
                    <option value="">-- Semua Kategori --</option>
                    @foreach ($dataKodeAkun as $item)
                        <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                    @endforeach
                </select>&nbsp;
                <input type="month" class="form-control w-auto" wire:model.lazy="bulanPerolehan"
                    max="{{ date('Y-m') }}">
                &nbsp;
                <input type="text" class="form-control w-auto" placeholder="Cari" autocomplete="off"
                    wire:model.lazy="cari">
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.datamaster.asetinventaris.tabel', ['cetak' => false])
        </div>
        <div class="panel-footer">
            {{ $data->links() }}
        </div>
    </div>
    <x-modal.cetak judul='QR' />

    <div wire:loading>
        <x-loading />
    </div>
</div>
