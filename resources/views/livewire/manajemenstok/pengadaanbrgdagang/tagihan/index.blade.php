<div>
    @section('title', 'Tagihan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item active">Tagihan</li>
    @endsection

    <h1 class="page-header">Tagihan</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            @role('administrator|supervisor')
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
                        <th>Data Pemesanan</th>
                        <th>No. Faktur</th>
                        <th>Tanggal</th>
                        <th>Catatan</th>
                        <th>Jatuh Tempo</th>
                        <th>Detail Tagihan</th>
                        <th>Status</th>
                        <th>No. Jurnal</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td nowrap>
                                <ul>
                                    @if ($row->pengadaanPemesanan->nomor)
                                        <li>No. SP : {{ $row->pengadaanPemesanan->nomor }}</li>
                                    @endif
                                    <li>Supplier : {{ $row->pengadaanPemesanan->supplier->nama }}</li>
                                    <li>Tanggal : {{ $row->pengadaanPemesanan->tanggal }}</li>
                                </ul>
                            </td>
                            <td>{{ $row->no_faktur }}</td>
                            <td>{{ $row->tanggal }}</td>
                            <td>{{ $row->catatan }}</td>
                            <td>{{ $row->tanggal_jatuh_tempo }}</td>
                            <td>
                                <table class="table table-bordered fs-11px">
                                    <thead>
                                        <tr>
                                            <th class="p-1">Barang</th>
                                            <th class="p-1">Satuan</th>
                                            <th class="p-1">Qty</th>
                                            <th class="p-1">Harga Beli</th>
                                            <th class="p-1">Total</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($row->pengadaanPemesanan->pengadaanPemesananDetail as $detail)
                                            <tr>
                                                <td class="text-nowrap w-300px p-1">
                                                    {{ $detail->barangSatuan->barang->nama }}</td>
                                                <td class="text-nowrap w-80px p-1">
                                                    @if ($detail->barangSatuan->konversi_satuan)
                                                        {!! $detail->barangSatuan->nama . ' <small>' . $detail->barangSatuan->konversi_satuan . '</small>' !!}
                                                    @else
                                                        {{ $detail->barangSatuan->nama }}
                                                    @endif
                                                </td>
                                                <td class="text-nowrap text-end w-50px p-1">
                                                    {{ $detail->qty }}
                                                </td>
                                                <td class="text-nowrap text-end w-80px p-1">
                                                    {{ number_format($detail->harga_beli) }}
                                                </td>
                                                <td class="text-nowrap text-end w-80px p-1">
                                                    {{ number_format($detail->harga_beli * $detail->qty) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th colspan="4" class="text-end p-1">Total Harga Barang :</th>
                                            <th class="text-end p-1">{{ number_format($row->total_harga_barang) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4" class="text-end p-1">Diskon :</th>
                                            <th class="text-end p-1">{{ number_format($row->diskon) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4" class="text-end p-1">PPN :</th>
                                            <th class="text-end p-1">{{ number_format($row->ppn) }}</th>
                                        </tr>
                                        <tr>
                                            <th colspan="4" class="text-end p-1">Tagihan :</th>
                                            <th class="text-end p-1">{{ number_format($row->total_tagihan) }}</th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </td>
                            <td>{{ $row->status }}</td>
                            <td><a href="/jurnalkeuangan?bulan={{ substr($row->keuanganJurnal?->tanggal, 0, 7) }}&cari={{ $row->keuanganJurnal?->id }}"
                                    target="_blank">{{ $row->keuanganJurnal?->nomor }}</a></td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor')
                                    @if ($row->pengadaanPelunasan->count() > 0)
                                        <x-action :row="$row" custom="" :detail="false" :edit="false"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="false" />
                                    @else
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

    <div wire:loading>
        <x-loading />
    </div>
</div>
