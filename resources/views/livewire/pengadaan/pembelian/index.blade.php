<div>
    @section('title', 'Pembelian')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Pembelian</li>
    @endsection

    <h1 class="page-header">Pembelian</h1>
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
                        <th>Permintaan Pembelian</th>
                        <th>Uraian</th>
                        <th>Supplier</th>
                        <th>Pembayaran</th>
                        <th>Barang/Item</th>
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
                                    <li><strong>{{ $row->permintaanPembelian->deskripsi }}</strong></li>
                                    <li>{{ $row->permintaanPembelian->created_at->format('d-m-Y') }}</li>
                                </ul>
                            </td>
                            <td>{{ $row->uraian }}</td>
                            <td>{{ $row->supplier->nama }}</td>
                            <td>{!! $row->pembayaran == 'Jatuh Tempo'
                                ? '<span class="badge bg-danger">Jatuh Tempo' . $row->jatuh_tempo . '</span>'
                                : '<span class="badge bg-success">Lunas</span>' !!}
                            </td>
                            <td class="w-400px">
                                <table class="table-bordered fs-10px">
                                    <tr class="bg-gray-100">
                                        <th class="text-nowrap w-250px p-1">Barang/Item</th>
                                        <th class="w-100px p-1">Satuan</th>
                                        <th class="w-100px p-1">Harga Satuan</th>
                                        <th class="w-50px p-1">Qty</th>
                                        <th class="w-100px p-1">Harga</th>
                                    </tr>
                                    @foreach ($row->pembelianDetail as $j => $subRow)
                                        <tr>
                                            <td class="p-1">
                                                {{ $subRow->barang->nama }}</td>
                                            <td class="text-end p-1  text-nowrap">
                                                {{ $subRow->barang->satuan }}</td>
                                            <td class="text-end p-1  text-nowrap">
                                                {{ number_format($subRow->harga_beli) }}</td>
                                            <td class="text-end p-1  text-nowrap">
                                                {{ number_format($subRow->qty) }}</td>
                                            <td class="text-end p-1  text-nowrap">
                                                {{ number_format($subRow->qty * $subRow->harga_beli) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <td class="p-1" colspan="4">Sub Total</td>
                                        <td class="text-end p-1  text-nowrap">
                                            {{ number_format($row->pembelianDetail->sum(fn($q) => $q->harga_beli * $q->qty)) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" colspan="4">Diskon</td>
                                        <td class="text-end p-1  text-nowrap">
                                            {{ number_format($row->diskon) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td class="p-1" colspan="4">PPN</td>
                                        <td class="text-end p-1  text-nowrap">
                                            {{ number_format($row->ppn) }}
                                        </td>
                                    </tr>
                                    <tr>
                                        <th class="p-1" colspan="4">Sub Total</th>
                                        <th class="text-end p-1  text-nowrap">
                                            {{ number_format($row->pembelianDetail->sum(fn($q) => $q->harga_beli * $q->qty) - $row->diskon + $row->ppn) }}
                                        </th>
                                    </tr>
                                </table>
                            </td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor')
                                    @if ($row->stokMasuk->count() == 0)
                                        <x-action :row="$row" custom="" :detail="false" :edit="false"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="true" />
                                    @endif
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
</div>
