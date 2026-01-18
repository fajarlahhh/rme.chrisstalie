<div>
    @section('title', 'Penambahan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Manajemen Stok</li>
        <li class="breadcrumb-item">Opname</li>
        <li class="breadcrumb-item">Penambahan</li>
        <li class="breadcrumb-item active">Data</li>
    @endsection

    <h1 class="page-header">Opname <small>Penambahan</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading">
            @role('administrator|supervisor|operator')
                <a href="/manajemenstok/opname/penambahan/form"
                    class="btn btn-primary">
                    Tambah</a>
            @endrole
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input class="form-control" type="date" wire:model.lazy="tanggal1" />&nbsp;
                    <input class="form-control" type="date" wire:model.lazy="tanggal2" />&nbsp;
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari" placeholder="Cari">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>Tanggal</th>
                        <th>Catatan</th>
                        <th>Barang</th>
                        <th>Qty</th>
                        <th>Operator</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-nowrap w-100px">{{ $row->created_at }}</td>
                            <td class="text-nowrap w-100px">{{ $row->catatan }}</td>
                            <td class="text-nowrap w-100px">{{ $row->barang->nama }}</td>
                            <td class="text-nowrap w-100px">{{ $row->qty }}</td>
                            <td class="text-nowrap w-100px">{{ $row->pengguna->nama }}</td>
                            <td></td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="10" class="text-center">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <x-alert />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
