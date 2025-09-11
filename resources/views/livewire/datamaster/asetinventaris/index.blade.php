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

            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <select class="form-control w-auto" wire:model.lazy="kategori">
                        <option value="">-- Semua Kategori --</option>
                        @foreach (\App\Enums\KategoriAsetEnum::cases() as $item)
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
                        <th>Satuan</th>
                        <th>Kategori</th>
                        <th>Tanggal Perolehan</th>
                        <th class="text-end">Harga Perolehan</th>
                        <th>Masa Manfaat</th>
                        <th>Lokasi</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->satuan }}</td>
                            <td>{{ $item->kategori }}</td>
                            <td>{{ $item->tanggal_perolehan }}</td>
                            <td class="text-end">{{ number_format($item->harga_perolehan) }}</td>
                            <td>{{ $item->masa_manfaat }} <small>bulan</small></td>
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
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor')
                                    <x-action :row="$item" custom="" :detail="false" :edit="false"
                                        :print="false" :permanentDelete="false" :restore="false" :delete="false" />
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
