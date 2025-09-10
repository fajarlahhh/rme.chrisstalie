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
                    <select class="form-control w-auto" wire:model.lazy="jenis">
                        @foreach (\App\Enums\JenisBarangEnum::cases() as $item)
                            <option value="{{ $item->value }}">{{ $item->label() }}</option>
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
                        <th>Kode Akun</th>
                        <th>KFA</th>
                        <th>Perlu Resep</th>
                        <th>Satuan</th>
                        <th>Unit Bisnis</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->kode_akun_id }} - {{ $item->kodeAkun?->nama }}</td>
                            <td>{{ $item->kfa }}</td>
                            <td>{{ $item->perlu_resep ? 'Ya' : 'Tidak' }}</td>
                            <td>
                                <table class="table table-bordered fs-11px">
                                    <tbody>
                                        @foreach ($item->barangSatuan as $satuan)
                                            <tr>
                                                <td class="p-1">{{ $satuan->nama }}</td>
                                                <td class="text-end w-100px p-1">
                                                    {{ number_format($satuan->harga_jual, 0, ',', '.') }}</td>
                                                <td class="text-center p-1 text-nowrap w-50px">
                                                    {!! $satuan->rasio_dari_terkecil == 1
                                                        ? '<span class="badge bg-success">Terkecil</span>'
                                                        : '1/' . $satuan->rasio_dari_terkecil !!} </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
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
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            {{ $data->links() }}
        </div>
    </div>
    <x-alert />
</div>
