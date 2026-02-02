<div>
    @section('title', 'Permintaan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item active">Permintaan</li>
    @endsection

    <h1 class="page-header">Permintaan</h1>
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
                    <select wire:model.lazy="status" class="form-control w-auto">
                        <option value="Disetujui">Disetujui</option>
                        <option value="Ditolak">Ditolak</option>
                        <option value="Pending">Pending</option>
                    </select>&nbsp;
                    @if ($status == 'Disetujui')
                        <input type="date" class="form-control w-200px" wire:model.lazy="tanggal" />&nbsp;
                    @endif
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
                        <th>Deskripsi</th>
                        <th>History Verifikasi</th>
                        <th>Status</th>
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
                                    @foreach ($item->pengadaanVerifikasi as $pengadaanVerifikasi)
                                        @if ($pengadaanVerifikasi->status)
                                            <li>
                                                @if ($pengadaanVerifikasi->status == 'Disetujui')
                                                    <span class="badge bg-success">Disetujui</span>
                                                @else
                                                    <span class="badge bg-danger">Ditolak
                                                        {{ ' - ' . $pengadaanVerifikasi->catatan }}</span>
                                                @endif
                                                <br>
                                                <small>
                                                    {{ $pengadaanVerifikasi->pengguna->nama }} <br>
                                                    {{ $pengadaanVerifikasi->waktu_verifikasi }}
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
                                <ul>
                                    @if ($item->pengadaanPemesanan)
                                        <li>Pembelian</li>
                                        @if ($item->pengadaanPemesanan->stokMasuk->count() > 0)
                                            <li>Stok Masuk</li>
                                        @endif
                                    @endif
                                </ul>
                            </td>
                            <td>
                                <table class="table table-bordered fs-11px">
                                    <thead>
                                        <tr>
                                            <th class="p-1">Barang</th>
                                            <th class="p-1">Satuan</th>
                                            <th class="p-1">Qty Permintaan</th>
                                            <th class="p-1">Qty Disetujui</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->pengadaanPermintaanDetail as $detail)
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
                                                <td class="text-nowrap text-end w-80px p-1">
                                                    {{ $detail->qty_permintaan }}
                                                </td>
                                                <td class="text-nowrap text-end w-80px p-1">
                                                    {{ $detail->qty_disetujui }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor|operator')
                                    @if ($item->pengadaanVerifikasiPending->count() > 0)
                                        <x-action :row="$item" custom="" :detail="false" :edit="false"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="true" />
                                    @else
                                        @if ($item->pengadaanVerifikasiDisetujui->count() > 0 || $item->pengadaanVerifikasiDitolak->count() > 0)
                                            @if ($item->pengadaanPemesanan && $item->pengadaanPemesanan->stokMasuk->count() > 0)
                                                <x-action :row="$item" custom="" :detail="false"
                                                    :edit="false" :print="false" :permanentDelete="false"
                                                    :restore="false" :delete="false" />
                                            @else
                                                <x-action :row="$item" custom="" :detail="false"
                                                    :edit="false" :print="false" :permanentDelete="false"
                                                    :restore="false" :delete="true" />
                                            @endif
                                        @else
                                            <x-action :row="$item" custom="" :detail="false"
                                                :edit="true" :print="false" :permanentDelete="false" :restore="false"
                                                :delete="true" />
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
