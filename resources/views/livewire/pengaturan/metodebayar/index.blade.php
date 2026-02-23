<div>
    @section('title', 'Metode Bayar')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengaturan</li>
        <li class="breadcrumb-item active">Metode Bayar</li>
    @endsection

    <h1 class="page-header">Metode Bayar</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            @role('administrator|supervisor')
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                    class="btn btn-primary">
                    Tambah</a>&nbsp;
            @endrole
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari">
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>Nama</th>
                        <th>Kode Akun</th>
                        <th>Biaya Admin</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $item)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->kode_akun_id }} - {{ $item->kodeAkun?->nama }}</td>
                            <td>{{ number_format($item->biaya_admin, 2) }}</td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor')
                                    @if ($item->nama == 'Cash')
                                        <x-action :row="$item" custom="" :detail="false" :edit="true"
                                            :print="false" :permanentdelete="false" :restore="false" :delete="false" />
                                    @else
                                        <x-action :row="$item" custom="" :detail="false" :edit="true"
                                            :print="false" :permanentdelete="false" :restore="false" :delete="true" />
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
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
