<div>
    @section('title', 'Pemesanan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item active">Pemesanan</li>
    @endsection

    <h1 class="page-header">Pemesanan <small>Pengadaan Barang Dagang</small></h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">

            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <select class="form-select" wire:model.lazy="status">
                        <option value="Belum Dipesan">Belum Dipesan</option>
                        <option value="Sudah Dipesan">Sudah Dipesan</option>
                        <option value="Sudah Persetujuan">Sudah Persetujuan</option>
                    </select>&nbsp;
                    @if ($status == 'Sudah Dipesan' || $status == 'Sudah Persetujuan')
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
            @if ($status == 'Belum Dipesan')
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="w-10px">No.</th>
                            <th>Deskripsi</th>
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
                                    <table class="table table-bordered fs-11px">
                                        <thead>
                                            <tr>
                                                <th>Barang</th>
                                                <th>Satuan</th>
                                                <th>Qty Permintaan</th>
                                                <th>Qty Dipesan</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item->pengadaanPermintaanDetail as $detail)
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
                                                        {{ $detail->qty_disetujui }}
                                                    </td>
                                                    <td class="text-nowrap text-end w-80px">
                                                        {{ $item->pengadaanPemesananDetail->where('barang_id', $detail->barang_id)->sum('qty') }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td class="with-btn-group text-end" nowrap>
                                    @role('administrator|supervisor|operator')
                                        <a href="/manajemenstok/pengadaanbrgdagang/pemesanan/form/{{ $item->id }}"
                                            class="btn btn-info btn-sm">
                                            Buat
                                        </a>
                                    @endrole
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @elseif ($status == 'Sudah Dipesan')
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="w-10px">No.</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Deskripsi Permintaan</th>
                            <th>Supplier</th>
                            <th class="w-600px">Detail</th>
                            <th class="w-100px">Total Harga</th>
                            <th class="w-10px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                <td>{{ $item->tanggal }}</td>
                                <td>{{ $item->pengadaanPermintaan->deskripsi }}</td>
                                <td>{{ $item->supplier->nama }}</td>
                                <td>
                                    <table class="table table-bordered fs-11px">
                                        <thead>
                                            <tr>
                                                <th>Barang</th>
                                                <th>Satuan</th>
                                                <th>Qty</th>
                                                <th>Harga Beli</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ($item->pengadaanPemesananDetail as $detail)
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
                                                        {{ $detail->qty }}
                                                    </td>
                                                    <td class="text-nowrap text-end w-80px">
                                                        {{ number_format($detail->harga_beli) }}
                                                    </td>
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </td>
                                <td class="text-nowrap text-end w-100px">
                                    {{ number_format($item->pengadaanPemesananDetail->sum(fn($q) => $q->harga_beli * $q->qty)) }}
                                </td>
                                <td class="with-btn-group text-end" nowrap>
                                    @role('administrator|supervisor|operator')
                                        @if (!$item->pengadaanPermintaan->pengadaanVerifikasiPersetujuan)
                                            <x-action :row="$item" custom="" :detail="false" :edit="false"
                                                :print="false" :permanentDelete="false" :restore="false" :delete="true" />
                                        @else
                                            <x-action :row="$item" custom="" :detail="false" :edit="true"
                                                :print="true" :permanentDelete="false" :restore="false" :delete="false" />
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
