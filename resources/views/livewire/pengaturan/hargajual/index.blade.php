<div>
    @section('title', 'Harga Jual')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengaturan</li>
        <li class="breadcrumb-item active">Harga Jual</li>
    @endsection

    <h1 class="page-header">Harga Jual</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            @role('administrator|supervisor')
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                    class="btn btn-primary">
                    Tambah</a>
            @endrole
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <select class="form-control w-200px" wire:model.lazy="barang_id" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    })">
                        <option value="">Semua Barang</option>
                        @foreach ($dataBarang as $item)
                            <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>Nama Barang</th>
                        <th>Satuan</th>
                        <th>Utama</th>
                        <th>Harga Jual</th>
                        <th>Konversi Satuan</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->barang_nama }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{!! $item->utama == 1
                                ? '<span class="badge bg-success">Ya</span>'
                                : '' !!}</td>
                            <td>{{ number_format($item->harga_jual, 0, ',', '.') }}</td>
                            <td>{!! $item->rasio_dari_terkecil == 1
                                ? '<span class="badge bg-success">Satuan Terkecil</span>'
                                : $item->konversi_satuan !!}</td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor')
                                    @if ($item->rasio_dari_terkecil == 1)
                                        <x-action :row="$item" custom="" :detail="false" :edit="true"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="false" />
                                    @else
                                        <x-action :row="$item" custom="" :detail="false" :edit="true"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="true" />
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
