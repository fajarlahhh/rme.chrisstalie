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
        <div class="panel-heading">
            <a href="javascript:;" wire:click="export" class="btn btn-success">
                Export</a>
            <div class="w-100">
                <div class="panel-heading-btn float-end">
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
                    <input type="month" autocomplete="off" wire:model.lazy="bulan" class="form-control w-auto" min="2025-11" max="{{ date('Y-m') }}">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            @include('livewire.laporan.keuanganbulanan.bukubesar.cetak', ['cetak' => false])
        </div>
    </div>
    <x-alert />
    <x-modal.cetak judul="Buku Besar" />

    <div wire:loading>
        <x-loading />
    </div>
</div>
