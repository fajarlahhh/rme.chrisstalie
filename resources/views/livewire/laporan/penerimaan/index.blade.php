<div>
    @section('title', 'Penerimaan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Penerimaan</li>
    @endsection

    <!-- BEGIN page-header -->
    <h1 class="page-header">Penerimaan</h1>
    <!-- END page-header -->

    <div class="panel panel-inverse" data-sortable-id="table-basic-2">
        <!-- BEGIN panel-heading -->
        <div class="panel-heading">
            <a href="javascript:;" wire:click="export" class="btn btn-success">
                Export</a>
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input type="date" autocomplete="off" wire:model.lazy="tanggal1" id="tanggal"
                        class="form-control w-auto">&nbsp;s/d&nbsp;
                    <input type="date" autocomplete="off" wire:model.lazy="tanggal2" id="tanggal"
                        class="form-control w-auto">&nbsp;
                    <select class="form-control w-auto" wire:model.lazy="pengguna_id">
                        @role('administrator|supervisor')
                            <option value="">Semua Pengguna</option>
                        @endrole
                        @foreach ($dataPengguna as $item)
                            <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                    &nbsp;
                    <select class="form-control w-auto" wire:model.lazy="metode_bayar">
                        <option value="">Semua Metode Bayar</option>
                        @foreach ($dataMetodeBayar as $item)
                            <option value="{{ $item['nama'] }}">{{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            @include('livewire.laporan.penerimaan.cetak', ['cetak' => false])
        </div>
    </div>
    <x-alert />
    <x-modal.cetak judul="Penerimaan" />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
