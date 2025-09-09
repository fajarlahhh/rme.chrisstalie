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
                    <select class="form-control w-auto" wire:model.lazy="kantor">
                        <option value="">Semua Kategori</option>
                        <option value="Medis">Medis</option>
                        <option value="Non Medis">Non Medis</option>
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
                        <th rowspan="2">ICD 10 CM</th>
                        <th colspan="6">Biaya</th>
                        <th rowspan="2"></th>
                    </tr>
                    <tr>
                        <th class="text-end">Biaya Alat Bahan</th>
                        <th class="text-end">Biaya Jasa Dokter</th>
                        <th class="text-end">Biaya Jasa Perawat</th>
                        <th class="text-end">Biaya Tidak Langsung</th>
                        <th class="text-end">Biaya Keuntungan Klinik</th>
                        <th class="text-end">Biaya Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->kategori }}</td>
                            <td>{{ $item->icd_10_cm }}</td>
                            <td class="text-end">{{ number_format($item->biaya_alat_bahan) }}</td>
                            <td class="text-end">{{ number_format($item->biaya_jasa_dokter) }}</td>
                            <td class="text-end">{{ number_format($item->biaya_jasa_perawat) }}</td>
                            <td class="text-end">{{ number_format($item->biaya_tidak_langsung) }}</td>
                            <td class="text-end">{{ number_format($item->biaya_keuntungan_klinik) }}</td>
                            <th class="text-end">{{ number_format($item->biaya_total) }}</th>
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
