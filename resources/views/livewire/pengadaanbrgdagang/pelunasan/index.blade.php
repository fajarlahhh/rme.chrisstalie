<div>
    @section('title', 'Pelunasan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item active">Pelunasan</li>
    @endsection

    <h1 class="page-header">Pelunasan</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            @role('administrator|supervisor|operator')
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                    class="btn btn-primary">
                    Tambah</a>
            @endrole
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input type="month" class="form-control w-auto" wire:model.lazy="bulan" max="{{ date('Y-m') }}">
                    &nbsp;
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>Tanggal</th>
                        <th>Pembelian</th>
                        <th>Uraian</th>
                        <th>Pembayaran</th>
                        <th>Jumlah</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $row->tanggal }}</td>
                            <td>
                                <ul>
                                    <li><strong>{{ $row->pembelian->uraian }}</strong></li>
                                    <li>{{ $row->pembelian->tanggal }}</li>
                                </ul>
                            </td>
                            <td>{{ $row->uraian }}</td>
                            <td>{{ $row->kodeAkunPembayaran->nama }}</td>
                            <td>{{ number_format($row->jumlah) }}</td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor')
                                    <x-action :row="$row" custom="" :detail="false" :edit="false"
                                        :print="false" :permanentDelete="false" :restore="false" :delete="true" />
                                @endrole
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            {{ $data->links() }}
        </div>
    </div>
    <x-alert />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
