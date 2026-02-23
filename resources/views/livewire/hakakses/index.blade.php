<div>
    @section('title', 'Hak Akses')

    @section('breadcrumb')
        <li class="breadcrumb-item active">Hak Akses</li>
    @endsection

    <h1 class="page-header">Hak Akses</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            @role('administrator|supervisor|operator')
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                    class="btn btn-primary">
                    Tambah</a>&nbsp;
            @endrole
            <select data-container="body" class="form-control w-auto" wire:model.lazy="exist">
                <option value="1">Exist</option>
                <option value="2">Deleted</option>
            </select>&nbsp;
            <input type="text" class="form-control w-auto" placeholder="Cari" aria-label="Sizing example input"
                autocomplete="off" aria-describedby="basic-addon2" wire:model.lazy="cari">
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>Nama</th>
                        <th>Pegawai</th>
                        <th>UID</th>
                        <th>Level</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>
                                {{ ($data->currentpage() - 1) * $data->perpage() + $loop->index + 1 }}
                            </td>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->kepegawaianPegawai ? $row->kepegawaianPegawai->satuan_tugas : '' }}</td>
                            <td>{{ $row->uid }}</td>
                            <td>{{ $row->getRoleNames()->first() }}
                            </td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor|operator')
                                    @if ($row->uid != 'administrator')
                                        @if ($row->trashed())
                                            <x-action :row="$row" custom="" :detail="false" :edit="false"
                                                :print="false" :permanentdelete="false" :restore="true" :delete="false" />
                                        @else
                                            <x-action :row="$row" custom="" :detail="false" :edit="true"
                                                :print="false" :permanentdelete="true" :restore="false"
                                                :delete="true" />
                                        @endif
                                    @endif
                                @endrole
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="panel-footer form-inline">
            {{ $data->links() }}
        </div>
    </div>

    <div wire:loading>
        <x-loading />
    </div>
</div>
