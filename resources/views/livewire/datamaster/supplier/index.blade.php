<div>
    @section('title', 'Supplier')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Supplier</li>
    @endsection

    <h1 class="page-header">Supplier</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            @role('administrator|supervisor|operator')
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                    class="btn btn-primary">
                    Tambah</a>&nbsp;
            @endrole
            <a href="javascript:;" wire:click="export" class="btn btn-success">
                Export</a>&nbsp;
                    <select data-container="body" class="form-control "wire:model.lazy="exist">
                        <option value="1">Exist</option>
                        <option value="2">Deleted</option>
                    </select>&nbsp;
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
                        <th>Alamat</th>
                        <th>No. Telp.</th>
                        <th>Kode Akun Hutang</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>
                                {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                            </td>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->alamat }}</td>
                            <td>{{ $row->no_hp }}</td>
                            <td>{{ $row->kode_akun_id }} - {{ $row->kodeAkun?->nama }}</td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor|operator')
                                    <x-action :row="$row" custom="" :detail="false" :edit="true"
                                        :print="false" :permanentdelete="false" :restore="false" :delete="true" />
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
