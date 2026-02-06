<div>
    @section('title', 'Permintaan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item active">Permintaan</li>
    @endsection

    <h1 class="page-header">Permintaan <small>Pengadaan Barang Dagang</small></h1>
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
                        <option value="Pending Verifikasi">Pending Verifikasi</option>
                        <option value="Belum Kirim Verifikasi">Belum Kirim Verifikasi</option>
                    </select>&nbsp;
                    @if ($status == 'Disetujui')
                        <input type="month" class="form-control w-200px" wire:model.lazy="bulan"
                            max="{{ date('Y-m') }}" />&nbsp;
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
                        <th>Nomor</th>
                        <th>Deskripsi</th>
                        <th>Waktu Permintaan</th>
                        <th>History Verifikasi</th>
                        <th class="w-600px">Detail</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->nomor }}</td>
                            <td>{{ $item->deskripsi }}</td>
                            <td>{{ $item->created_at }}</td>
                            <td>
                                @foreach ($item->pengadaanVerifikasi as $pengadaanVerifikasi)
                                    @if ($pengadaanVerifikasi->status)
                                        @if ($pengadaanVerifikasi->status == 'Disetujui')
                                            <span class="badge bg-success">Disetujui :
                                                {{ $pengadaanVerifikasi->waktu_verifikasi }}</span><br>
                                        @else
                                            <span class="badge bg-danger">Ditolak
                                                {{ ' - ' . $pengadaanVerifikasi->catatan }} :
                                                {{ $pengadaanVerifikasi->waktu_verifikasi }}</span><br>
                                        @endif
                                    @else
                                        <span class="badge bg-warning">Pending Verifikasi</span><br>
                                    @endif
                                @endforeach
                            </td>
                            <td>
                                <table class="table table-bordered fs-11px">
                                    <thead>
                                        <tr>
                                            <th class="p-1">Barang</th>
                                            <th class="p-1">Satuan</th>
                                            <th class="p-1">Qty Permintaan</th>
                                            <th class="p-1">Qty Disetujui</th>
                                            <th class="p-1">Qty Dipesan</th>
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
                                                <td class="text-nowrap text-end w-80px p-1">
                                                    {{ $item->pengadaanPemesananDetail->where('barang_id', $detail->barang_id)->sum('qty') }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor|operator')
                                    @if ($item->pengadaanVerifikasi->whereNull('status')->count() > 0)
                                        <x-action :row="$item" custom="" :detail="false" :edit="false"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="true" />
                                    @else
                                        @if ($item->pengadaanVerifikasi->whereNotNull('status')->where('status', 'Disetujui')->count() > 0)
                                            @if ($item->pengadaanPemesanan)
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
