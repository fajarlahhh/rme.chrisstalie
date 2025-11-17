<div>
    @section('title', 'Barang Dagang')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Barang Dagang</li>
    @endsection

    <h1 class="page-header">Barang Dagang</h1>
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
                    <select class="form-control w-auto" wire:model.lazy="persediaan">
                        <option value="">Semua Persediaan</option>
                        <option value="Apotek">Apotek</option>
                        <option value="Klinik">Klinik</option>
                    </select>&nbsp;
                    <select class="form-control w-auto" wire:model.lazy="kode_akun_id">
                        <option value="">Semua Kategori</option>
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
                        <th>Kategori Persediaan</th>
                        <th>Kategori Penjualan</th>
                        <th>Kategori Modal</th>
                        <th>KFA</th>
                        <th>Perlu Resep</th>
                        <th>Persediaan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>
                                <table class="table table-bordered fs-11px">
                                    <tbody>
                                        @foreach ($item->barangSatuan as $satuan)
                                            <tr>
                                                <td class="p-1">{{ $satuan->nama }}</td>
                                                <td class="text-end w-100px p-1">
                                                    {{ number_format($satuan->harga_jual, 0, ',', '.') }}</td>
                                                <td class="p-1 text-nowrap w-150px">
                                                    {!! $satuan->rasio_dari_terkecil == 1
                                                        ? '<span class="badge bg-success">Terkecil</span>'
                                                        : '<span class="badge bg-warning">' . $satuan->konversi_satuan . '</span>' !!}
                                                    {!! $satuan->utama == 1 ? '<span class="badge bg-info">Utama</span>' : '' !!}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                            <td>{{ $item->kode_akun_id }} - {{ $item->kodeAkun?->nama }}</td>
                            <td>{{ $item->kode_akun_penjualan_id }} - {{ $item->kodeAkunPenjualan?->nama }}</td>
                            <td>{{ $item->kode_akun_modal_id }} - {{ $item->kodeAkunModal?->nama }}</td>
                            <td>{{ $item->kfa }}</td>
                            <td>{{ $item->perlu_resep ? 'Ya' : '' }}</td>
                            <td>{{ $item->persediaan }}</td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor')
                                    <x-action :row="$item" custom="" :detail="false" :edit="true"
                                        :print="false" :permanentDelete="false" :restore="false" :delete="true" />
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
