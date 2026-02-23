<div>
    @section('title', 'Pemesanan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item active">Pemesanan</li>
    @endsection

    <h1 class="page-header">Pemesanan <small>Pengadaan Barang Dagang</small></h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
                    <select class="form-select" wire:model.lazy="status">
                        <option value="Belum Buat SP">Belum Buat SP</option>
                        <option value="Sudah Buat SP">Sudah Buat SP</option>
                    </select>&nbsp;
                    @if ($status == 'Sudah Buat SP')
                        <input type="month" class="form-control w-auto" wire:model.lazy="bulan"
                            max="{{ date('Y-m') }}">
                        &nbsp;
                    @endif
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari">
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @if ($status == 'Belum Buat SP')
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th class="w-10px">No.</th>
                            <th>Nomor Permintaan</th>
                            <th>Waktu Permintaan</th>
                            <th>Deskripsi Permintaan</th>
                            <th>Jenis Barang</th>
                            <th class="w-600px">Detail Barang Permintaan</th>
                            <th class="w-10px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                <td>{{ $item->nomor ?? $item->id }}</td>
                                <td>{{ $item->created_at }}</td>
                                <td>{{ $item->deskripsi }}</td>
                                <td>{{ $item->jenis_barang }}</td>
                                <td>
                                    <table class="table table-bordered fs-11px">
                                        <thead>
                                            <tr>
                                                <th>Barang</th>
                                                <th>Satuan</th>
                                                <th>Qty Diminta</th>
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
                                            Buat SP
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
                            <th>Data Permintaan</th>
                            <th>Nomor SP</th>
                            <th>Tanggal Pemesanan</th>
                            <th>Supplier</th>
                            <th>Penanggung Jawab</th>
                            <th>Catatan</th>
                            <th class="w-600px">Detail Barang</th>
                            <th class="w-10px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                <td nowrap>
                                    <small>
                                        <ul>
                                            <li>Nomor: {{ $item->pengadaanPermintaan?->nomor }}</li>
                                            <li>Deskripsi: {{ $item->pengadaanPermintaan?->deskripsi }}</li>
                                            <li>Tanggal: {{ $item->pengadaanPermintaan?->created_at }}</li>
                                            <li>Jenis Barang: {{ $item->pengadaanPermintaan?->jenis_barang }}</li>
                                        </ul>
                                    </small>
                                </td>
                                <td>{{ $item->nomor }}</td>
                                <td>{{ $item->tanggal }}</td>
                                <td>{{ $item->supplier->nama }}</td>
                                <td>{{ $item->penanggungJawab?->nama }}</td>
                                <td>{{ $item->catatan }}</td>
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
                                <td class="with-btn-group text-end" nowrap>
                                    @role('administrator|supervisor|operator')
                                        @if ($item->stokMasuk->count() > 0)
                                            <x-action :row="$item" custom="" :detail="false" :edit="false"
                                                :print="true" :permanentdelete="false" :restore="false" :delete="false" />
                                        @else
                                            {{-- @if ($status == 'Sudah Buat SP')
                                                <x-action :row="$item" custom="" :detail="false"
                                                    :edit="true" :print="true" :permanentdelete="false"
                                                    :restore="false" :delete="true" />
                                            @else --}}
                                                <x-action :row="$item" custom="" :detail="false"
                                                    :edit="false" :print="true" :permanentdelete="false"
                                                    :restore="false" :delete="true" />
                                            {{-- @endif --}}
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

    <div wire:loading>
        <x-loading />
    </div>
    <x-modal.cetak judul='Surat Pemesanan' />
</div>
