<div>
    @section('title', 'Pembelian')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item active">Pembelian</li>
    @endsection

    <h1 class="page-header">Pembelian</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <select class="form-select" wire:model.lazy="status">
                        <option value="1">Belum Dibeli</option>
                        <option value="2">Sudah Dibeli</option>
                    </select>&nbsp;
                    @if ($status == 2)
                        <input type="month" class="form-control w-auto" wire:model.lazy="bulan"
                            max="{{ date('Y-m') }}">
                        &nbsp;
                    @endif
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            @if ($status == 1)
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="w-10px">No.</th>
                            <th>Deskripsi</th>
                            <th>History Verifikasi</th>
                            <th class="w-600px">Detail</th>
                            <th class="w-10px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                <td>{{ $item->deskripsi }}</td>
                                <td>
                                    <ul>
                                        @foreach ($item->verifikasi as $verifikasi)
                                            @if ($verifikasi->status)
                                                <li>
                                                    @if ($verifikasi->status == 'Disetujui')
                                                        <span class="badge bg-success">Disetujui</span>
                                                    @else
                                                        <span class="badge bg-danger">Ditolak
                                                            {{ ' - ' . $verifikasi->catatan }}</span>
                                                    @endif
                                                    <br>
                                                    <small>
                                                        {{ $verifikasi->pengguna->nama }} <br>
                                                        {{ $verifikasi->waktu_verifikasi }}
                                                    </small>
                                                </li>
                                            @else
                                                <li>
                                                    <span class="badge bg-warning">Pending</span>
                                                </li>
                                            @endif
                                        @endforeach
                                    </ul>
                                </td>
                                <td>
                                    <table class="table table-bordered fs-11px">
                                        <thead>
                                            <tr>
                                                <th>Barang</th>
                                                <th>Satuan</th>
                                                <th>Qty Permintaan</th>
                                                <th>Qty Disetujui</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item->permintaanPembelianDetail as $detail)
                                                <tr>
                                                    <td class="text-nowrap w-300px">
                                                        {{ $detail->barangSatuan->barang->nama }}</td>
                                                    <td class="text-nowrap w-80px">
                                                        @if ($detail->barangSatuan->konversi_satuan)
                                                            {!! $detail->barangSatuan->nama . ' <small>' . $detail->barangSatuan->konversi_satuan . '</small>' !!}
                                                        @else
                                                            {{ $detail->barangSatuan->nama }}
                                                        @endif
                                                    </td>
                                                    <td class="text-nowrap text-end w-80px">
                                                        {{ $detail->qty_permintaan }}
                                                    </td>
                                                    <td class="text-nowrap text-end w-80px">
                                                        {{ $detail->qty_disetujui }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td class="with-btn-group text-end" nowrap>
                                    @role('administrator|supervisor|operator')
                                        <a href="/pengadaanbrgdagang/pembelian/form/{{ $item->id }}"
                                            class="btn btn-info">
                                            Input
                                        </a>
                                    @endrole
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
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
                                    ? '<span class="badge bg-danger">Jatuh Tempo : ' . $row->jatuh_tempo . '</span>'
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
                                                    {{ $subRow->barangSatuan->barang->nama }}</td>
                                                <td class="p-1 text-nowrap">
                                                    @if ($subRow->barangSatuan->konversi_satuan)
                                                        {!! $subRow->barangSatuan->nama . ' (' . $subRow->barangSatuan->konversi_satuan . ')' !!}
                                                    @else
                                                        {{ $subRow->barangSatuan->nama }}
                                                    @endif
                                                </td>
                                                <td class="text-end p-1  text-nowrap">
                                                    {{ number_format($subRow->harga_beli) }}</td>
                                                <td class="text-end p-1  text-nowrap">
                                                    {{ number_format($subRow->qty) }}</td>
                                                <td class="text-end p-1  text-nowrap">
                                                    {{ number_format($subRow->qty * $subRow->harga_beli) }}</td>
                                            </tr>
                                        @endforeach
                                        <tr>
                                            <td class="p-1" colspan="4">Total</td>
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
            @endif
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
