<div>
    @section('title', 'LHK')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item active">LHK</li>
    @endsection

    <h1 class="page-header"> LHK</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <a href="javascript:;" wire:click="print" x-init="$($el).on('click', function() {
                setTimeout(() => {
                    $('#modal-cetak').modal('show')
                }, 1000)
            })" class="btn btn-warning">
                Cetak</a>
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input class="form-control" type="date" wire:model.lazy="tanggal" />
                    &nbsp;
                    <select class="form-control" wire:model.lazy="pengguna_id">
                        <option value="">Semua Pengguna</option>
                        @foreach ($dataPengguna as $item)
                            <option value="{{ $item->id }}">{{ $item->nama }}</option>
                        @endforeach
                    </select>&nbsp;
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            @include('livewire.laporan.lhk.cetak', ['cetak' => false])
        </div>
    </div>
    <x-alert />
    <x-modal.cetak judul="" />
</div>
