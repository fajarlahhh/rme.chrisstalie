<div>
    @section('title', 'Stok Masuk')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item active">Stok Masuk</li>
    @endsection

    <h1 class="page-header">Stok Masuk</h1>
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
                        <th>Data Permintaan</th>
                        <th>Data Pemesanan</th>
                        <th>Tanggal</th>
                        <th>Barang</th>
                        <th>Qty</th>
                        <th>Satuan</th>
                        <th>No. Batch</th>
                        <th>Tanggal Kedaluarsa</th>
                        <th>No. Jurnal</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td nowrap>
                                <small>
                                    <ul>
                                        <li>Nomor: {{ $row->pengadaanPemesanan?->pengadaanPermintaan?->nomor }}</li>
                                        <li>Deskripsi: {{ $row->pengadaanPemesanan?->pengadaanPermintaan?->deskripsi }}
                                        </li>
                                        <li>Tanggal: {{ $row->pengadaanPemesanan?->pengadaanPermintaan?->created_at }}
                                        </li>
                                        <li>Jenis Barang:
                                            {{ $row->pengadaanPemesanan?->pengadaanPermintaan?->jenis_barang }}</li>
                                    </ul>
                                </small>
                            </td>
                            <td nowrap>
                                <small>
                                    <ul>
                                        @if ($row->pengadaanPemesanan->nomor)
                                            <li>No. SP : {{ $row->pengadaanPemesanan->nomor }}</li>
                                        @endif
                                        <li>Supplier : {{ $row->pengadaanPemesanan->supplier->nama }}</li>
                                        <li>Tanggal : {{ $row->pengadaanPemesanan->tanggal }}</li>
                                    </ul>
                                </small>
                            </td>
                            <td>{{ $row->created_at }}</td>
                            <td>{{ $row->barangSatuan->barang->nama }}</td>
                            <td>{{ $row->qty }}</td>
                            <td>{{ $row->barangSatuan->nama }}</td>
                            <td>{{ $row->no_batch }}</td>
                            <td>{{ $row->tanggal_kedaluarsa }}</td>
                            <td><a href="/jurnalkeuangan?bulan={{ substr($row->keuanganJurnal?->tanggal, 0, 7) }}&cari={{ $row->keuanganJurnal?->id }}"
                                    target="_blank">{{ $row->keuanganJurnal?->nomor }}</a></td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor')
                                    @if ($row->pengadaanPemesanan->pengadaanTagihan)
                                        <x-action :row="$row" custom="" :detail="false" :edit="false"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="false" />
                                    @else
                                        @if ($row->keluar->count() == 0)
                                            <x-action :row="$row" custom="" :detail="false" :edit="false"
                                                :print="false" :permanentDelete="false" :restore="false"
                                                :delete="true" />
                                        @else
                                            <x-action :row="$row" custom="" :detail="false"
                                                :edit="false" :print="false" :permanentDelete="false" :restore="false"
                                                :delete="false" />
                                        @endif
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
