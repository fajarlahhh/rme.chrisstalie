<div>
    @section('title', 'Buku Besar')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">Buku Besar</li>
    @endsection

    <!-- BEGIN page-header -->
    <h1 class="page-header">Buku Besar</h1>
    <!-- END page-header -->

    <div class="panel panel-inverse" data-sortable-id="table-basic-2">
        <!-- BEGIN panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <a href="javascript:;" wire:click="export" class="btn btn-outline-success btn-block">
                Export</a>&nbsp;
            <div class="ms-auto d-flex align-items-center">
                <select class="form-control w-auto" x-init="$($el).selectpicker({
                    liveSearch: true,
                    width: 'auto',
                    size: 10,
                    container: 'body',
                    style: '',
                    showSubtext: true,
                    styleBase: 'form-control'
                })" wire:model.lazy="kodeAkunId">
                    <option value="">-- Pilih Kode Akun --</option>
                    @foreach ($dataKodeAkun as $item)
                        <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                    @endforeach
                </select>&nbsp;
                <input type="month" autocomplete="off" wire:model.lazy="bulan" min="2025-09"
                    max="{{ date('Y-m', strtotime('-1 month')) }}" class="form-control w-auto">
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.keuanganbulanan.bukubesar.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="Buku Besar" />

    <div wire:loading>
        <x-loading />
    </div>
</div>
