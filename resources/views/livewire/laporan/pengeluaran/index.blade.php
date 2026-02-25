<div>
    @section('title', 'Pengeluaran')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Pengeluaran</li>
    @endsection

    <!-- BEGIN page-header -->
    <h1 class="page-header">Pengeluaran</h1>
    <!-- END page-header -->

    <div class="panel panel-inverse" data-sortable-id="table-basic-2">
        <!-- BEGIN panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <a href="javascript:;" wire:click="export" class="btn btn-outline-success btn-block">
                Export</a>&nbsp;
            <div class="ms-auto d-flex align-items-center">
                <input type="date" autocomplete="off" min="2025-11-29" max="{{ date('Y-m-d') }}"
                    wire:model.lazy="tanggal1" class="form-control w-auto">&nbsp;s/d&nbsp;
                <input type="date" autocomplete="off" min="2025-11-29" max="{{ date('Y-m-d') }}"
                    wire:model.lazy="tanggal2" class="form-control w-auto">&nbsp;
                <select class="form-control w-auto" wire:model.lazy="pengguna_id">
                    @role('administrator|supervisor')
                        <option value="">Semua Pengguna</option>
                    @endrole
                    @foreach ($dataPengguna as $item)
                        <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                    @endforeach
                </select>
                &nbsp;
                <select class="form-control w-auto" wire:model.live="metode_bayar">
                    <option value="">Semua Metode Bayar</option>
                    @foreach ($dataKodeAkun as $item)
                        <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.pengeluaran.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="Pengeluaran" />

    <div wire:loading>
        <x-loading />
    </div>
</div>
