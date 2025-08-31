<div>
    @section('title', 'Data Barang Konsinyasi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Barang Konsinyasi</li>
    @endsection

    <h1 class="page-header">Barang Konsinyasi</h1>
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
                        <option value="Alat Kesehatan">Alat Kesehatan</option>
                        <option value="Obat">Obat</option>
                        <option value="Produk Kecantikan">Produk Kecantikan</option>
                    </select>&nbsp;
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                @if ($jenis == 'Obat' || $jenis == 'Produk Kecantikan')
                    <thead>
                        <tr>
                            <th class="w-10px">No.</th>
                            <th>Nama</th>
                            <th>Satuan</th>
                            <th>Harga Jual</th>
                            <th>Bentuk</th>
                            <th>Konsinyator</th>
                            <th>Perlu Resep</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $item)
                            <tr>
                                <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                                <td>{{ $item->nama }}</td>
                                <td>{{ $item->satuan }}</td>
                                <td>{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                                <td>{{ $item->bentuk }}</td>
                                <td>{{ $item->konsinyator->nama }}</td>
                                <td>{{ $item->perlu_resep ? 'Ya' : 'Tidak' }}</td>
                                <td class="with-btn-group text-end" nowrap>
                                    @role('administrator|supervisor')
                                        <x-action :row="$item" custom="" :detail="false" :edit="true"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="true" />
                                    @endrole
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                @else
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Satuan</th>
                            <th>Harga Jual</th>
                            <th>Garansi</th>
                            <th>Konsinyator</th>
                            <th></th>
                        </tr>
                    </thead>
                @endif
            </table>
        </div>
        <div class="panel-footer">
            {{ $data->links() }}
        </div>
    </div>
    <x-alert />
</div>
