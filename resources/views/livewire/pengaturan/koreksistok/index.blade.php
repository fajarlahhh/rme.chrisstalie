<div>
    @section('title', 'Koreksi Stok')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengaturan</li>
        <li class="breadcrumb-item">Koreksi Stok</li>
        <li class="breadcrumb-item active">Data</li>
    @endsection

    <h1 class="page-header">Koreksi Stok <small>Data</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading">
            @role('administrator|supervisor|operator')
                <a href="/pengaturan/koreksistok/form"
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
                        <th>Barang</th>
                        <th>No Batch</th>
                        <th>Tanggal Kedaluarsa</th>
                        <th>Harga Beli</th>
                        <th>Qty</th>
                        <th>Sub Total</th>
                        <th>Keterangan</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-nowrap w-100px">{{ $row->created_at }}</td>
                            <td class="text-nowrap w-100px">{{ $row->barang->nama }}</td>
                            <td class="text-nowrap w-100px">{{ $row->no_batch }}</td>
                            <td class="text-nowrap w-100px">{{ $row->tanggal_kedaluarsa }}</td>
                            <td class="text-nowrap w-100px">{{ $row->harga_beli }}</td>
                            <td class="text-nowrap w-100px">{{ $row->qty }}</td>
                            <td class="text-nowrap w-100px">{{ $row->sub_total }}</td>
                            <td class="text-nowrap w-100px">{{ $row->keterangan }}</td>
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
