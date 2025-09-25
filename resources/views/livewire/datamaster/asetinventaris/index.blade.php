<div>
    @section('title', 'Data Aset/Inventaris')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Aset/Inventaris</li>
    @endsection

    <h1 class="page-header">Aset/Inventaris</h1>
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
                    <select class="form-control w-auto" wire:model.lazy="kode_akun_id">
                        <option value="">-- Semua Kategori --</option>
                        @foreach ($dataKodeAkun as $item)
                            <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                        @endforeach
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
                        <th>Nama</th>
                        <th>Satuan</th>
                        <th>Kategori</th>
                        <th>Tanggal Perolehan</th>
                        <th class="text-end">Harga Perolehan</th>
                        <th>Sumber Dana</th>
                        <th>Masa Manfaat</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th>Unit Bisnis</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>{{ $item->kode_akun_id }} - {{ $item->kodeAkun->nama }}</td>
                            <td>{{ $item->tanggal_perolehan }}</td>
                            <td class="text-end">{{ number_format($item->harga_perolehan) }}</td>
                            <td>
                                @if ($item->kode_akun_sumber_dana_id)
                                    {{ $item->kode_akun_sumber_dana_id }} - {{ $item->kodeAkunSumberDana->nama }}
                                @endif
                            </td>
                            <td>
                                @if ($item->metode_penyusutan == 'Satuan Hasil Produksi')
                                    {{ $item->masa_manfaat }} <small>x</small>
                                @else
                                    {{ $item->masa_manfaat }} <small>bulan</small>
                                @endif
                            </td>
                            <td>{{ $item->lokasi }}</td>
                            <td>
                                @switch($item->status)
                                    @case('Aktif')
                                        <span class="badge bg-success">Aktif</span>
                                    @break

                                    @case('Tidak Aktif')
                                        <span class="badge bg-danger">Tidak Aktif</span>
                                    @break

                                    @default
                                @endswitch
                            </td>
                            <td>{{ $item->unit_bisnis }}</td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor')
                                    <x-action :row="$item" custom="" :detail="false" :edit="true"
                                        :print="false" :permanentDelete="false" :restore="false" :delete="true" />
                                @endrole
                            </td>
                        </tr>
                    @endforeach
                    @if ($data->currentPage() == $data->lastPage())
                        <tr>
                            <th colspan="11" class="text-end">TOTAL</th>
                            <th class="text-end">{{ number_format($dataRaw->sum('harga_perolehan')) }}</th>
                        </tr>
                    @endif
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            {{ $data->links() }}
        </div>
    </div>
    <x-alert />
</div>
