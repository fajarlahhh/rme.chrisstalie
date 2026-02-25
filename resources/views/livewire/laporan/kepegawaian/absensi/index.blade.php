<div>
    @section('title', 'Absensi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Laporan</li>
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item active">Absensi</li>
    @endsection

    <!-- BEGIN page-header -->
    <h1 class="page-header">Absensi</h1>
    <!-- END page-header -->

    <div class="panel panel-inverse" data-sortable-id="table-basic-2">
        <!-- BEGIN panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <a href="javascript:;" wire:click="print" x-init="$($el).on('click', function() {
                setTimeout(() => {
                    $('#modal-cetak').modal('show')
                }, 1000)
            })" class="btn btn-outline-info btn-block">
                Cetak</a>&nbsp;
            <div class="ms-auto d-flex align-items-center">
                <select class="form-control w-auto" wire:model.lazy="jenis">
                    <option value="Rekap">Rekap</option>
                    <option value="Per Pegawai">Per Pegawai</option>
                </select>
                &nbsp;
                <input type="date" autocomplete="off" min="2025-11-29" max="{{ date('Y-m-d') }}"
                    wire:model.lazy="tanggal1" id="tanggal" class="form-control w-auto">&nbsp;s/d&nbsp;
                <input type="date" autocomplete="off" min="2025-11-29" max="{{ date('Y-m-d') }}"
                    wire:model.lazy="tanggal2" id="tanggal" class="form-control w-auto">&nbsp;
                @if ($jenis == 'Per Pegawai')
                    <select class="form-control w-auto" wire:model.lazy="kepegawaian_pegawai_id">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach ($dataPegawai as $row)
                            <option value="{{ $row['id'] }}">{{ $row['nama'] }}</option>
                        @endforeach
                    </select>
                @endif
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @include('livewire.laporan.kepegawaian.absensi.cetak', ['cetak' => false])
        </div>
    </div>
    <x-modal.cetak judul="Absensi" />

    <div wire:loading>
        <x-loading />
    </div>
</div>
