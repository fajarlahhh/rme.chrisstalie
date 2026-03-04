<div>
    @section('title', 'Rekapitulasi Bulanan')

    @section('breadcrumb')
        <li class="breadcrumb-item active">Rekapitulasi Bulanan</li>
    @endsection

    <h1 class="page-header">Rekapitulasi Bulanan</h1>

    <form wire:submit.prevent="submit">
        <div class="panel panel-inverse" data-sortable-id="table-basic-2">
            <div class="panel-heading overflow-auto d-flex">
                Form
            </div>
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Bulan</label>
                    <input type="month" class="form-control" wire:model="bulan" min="2025-09">
                </div>
                <div class="mb-3">
                    <label class="form-label">Tutup Buku</label>
                    <select class="form-control" wire:model="tutup_buku">
                        <option value="0">Tidak</option>
                        <option value="1">Ya</option>
                    </select>
                </div>
                <div class="alert alert-info">
                    <strong>Info:</strong> Rekapitulasi bulanan akan menghitung semua data yang ada pada bulan tersebut.
                    <ul>
                        <li>Stok Akhir Barang Dagang</li>
                        <li>Saldo Akhir Keuangan</li>
                        <li>Penyusutan Aset</li>
                        <li>Pembuatan Laporan Laba Rugi</li>
                        <li>Pembuatan Laporan Neraca</li>
                        <li>Pembuatan Laporan Arus Kas</li>
                    </ul>
                </div>
                <div class="alert alert-warning">
                    Lakukan Rekapitulasi Bulanan di awal bulan untuk mendapatankan data akhir bulan lalu.
                </div>
            </div>
            <div class="panel-footer">
                @unlessrole('guest')
                    <button type="button" x-init="$($el).on('click', function() {
                        $('#modal-konfirmasi').modal('show');
                    })" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Submit
                    </button>
                @endunlessrole
                <x-alert />
            </div>
        </div>

        <x-modal.konfirmasi />
    </form>

    <div wire:loading>
        <x-loading />
    </div>
</div>
