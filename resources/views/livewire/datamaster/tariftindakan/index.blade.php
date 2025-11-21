<div>
    @section('title', 'Barang')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Tarif Tindakan</li>
    @endsection

    <h1 class="page-header">Tarif Tindakan</h1>
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
                        <th rowspan="2" class="w-10px">No.</th>
                        <th rowspan="2">Nama</th>
                        <th rowspan="2">Kategori</th>
                        <th rowspan="2">ICD 9 CM</th>
                        <th rowspan="2" class="text-end">Tarif</th>
                        <th colspan="3">Biaya</th>
                        <th rowspan="2" class="text-end">Keuntungan Klinik</th>
                        <th rowspan="2" class="text-end">Status</th>
                        <th rowspan="2"></th>
                    </tr>
                    <tr>
                        <th class="text-end">Biaya Alat Bahan</th>
                        <th class="text-end">Biaya Jasa Dokter</th>
                        <th class="text-end">Biaya Jasa Perawat</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->kode_akun_id }} - {{ $item->kodeAkun?->nama }}</td>
                            <td>{{ $item->icd_9_cm }}</td>
                            <td class="text-end">{{ number_format($item->tarif) }}</td>
                            <td class="text-end">{{ number_format($item->biaya_alat_barang) }}</td>
                            <td class="text-end">{{ number_format($item->biaya_jasa_dokter) }}</td>
                            <td class="text-end">{{ number_format($item->biaya_jasa_perawat) }}</td>
                            <th class="text-end">
                                {{ number_format(
                                    $item->tarif -
                                        $item->biaya_jasa_dokter -
                                        $item->biaya_jasa_perawat -
                                        $item->biaya_alat_barang,
                                ) }}
                            </th>
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
