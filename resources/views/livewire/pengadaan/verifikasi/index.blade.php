<div>
    @section('title', 'Verifikasi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan</li>
        <li class="breadcrumb-item active">Verifikasi</li>
    @endsection

    <h1 class="page-header">Verifikasi <small>Pengadaan</small></h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <select class="form-select" wire:model.lazy="status">
                        <option value="Pending">Pending</option>
                        <option value="Terverifikasi">Terverifikasi</option>
                    </select>&nbsp;
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
                                        <li>
                                            @if ($verifikasi->status == 'Disetujui')
                                                <span class="badge bg-success">Disetujui</span>
                                            @else
                                                <span class="badge bg-danger">Ditolak</span>
                                            @endif
                                            <br>
                                            <small>
                                                {{ $verifikasi->pengguna->nama }} <br>
                                                {{ $verifikasi->waktu_verifikasi }}
                                            </small>
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>
                                <table class="table table-bordered fs-11px">
                                    <thead>
                                        <tr>
                                            <th>Barang</th>
                                            <th>Qty Permintaan</th>
                                            <th>Qty Disetujui</th>
                                            <th>Satuan</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($item->permintaanPembelianDetail as $detail)
                                            <tr>
                                                <td class="text-nowrap w-300px">{{ $detail->barang->nama }}</td>
                                                <td class="text-nowrap text-end w-80px">{{ $detail->qty_permintaan }}
                                                </td>
                                                <td class="text-nowrap text-end w-80px">{{ $detail->qty_disetujui }}
                                                </td>
                                                <td class="text-nowrap w-80px">{{ $detail->barang->satuan }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor')
                                    @if ($item->verifikasiDisetujui->count() > 0 || $item->verifikasiDitolak->count() > 0)
                                        <x-action :row="$item" custom="" :detail="false" :edit="false"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="false" />
                                    @else
                                        <x-action :row="$item" custom="" :detail="false" :edit="true"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="false" />
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
