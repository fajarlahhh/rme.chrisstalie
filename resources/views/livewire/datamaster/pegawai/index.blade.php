<div>
    @section('title', 'Pegawai')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Pegawai</li>
    @endsection

    <h1 class="page-header">Pegawai</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            @role('administrator|supervisor|operator')
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'" class="btn btn-primary"
                    wire:ignore>
                    Tambah</a>&nbsp;
            @endrole
            <div class="ms-auto d-flex align-items-center">
                <select class="form-control w-auto" wire:model.lazy="status">
                    <option value="Aktif">Aktif</option>
                    <option value="Non Aktif">Non Aktif</option>
                </select>&nbsp;
                <input type="text" class="form-control w-auto" placeholder="Cari" autocomplete="off"
                    wire:model.lazy="cari">
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>No. KTP</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No. Hp</th>
                        <th>Tanggal Masuk</th>
                        <th>Satuan Tugas</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>
                                {{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}
                            </td>
                            <td>{{ $row->nik }}</td>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->alamat }}</td>
                            <td>{{ $row->no_hp }}</td>
                            <td>{{ $row->tanggal_masuk }}</td>
                            <td>{{ $row->satuan_tugas }}</td>
                            <td class="with-btn-group text-end" nowrap>
                                @if ($row->status == 'Aktif')
                                    @role('administrator|supervisor')
                                        <x-action :row="$row" custom="" :detail="false" :edit="true"
                                            :print="false" :permanentdelete="false" :restore="false" :delete="true" />
                                    @endrole
                                @else
                                    @role('administrator')
                                        <x-action :row="$row" custom="" :detail="false" :edit="true"
                                            :print="false" :permanentdelete="false" :restore="false" :delete="false" />
                                    @endrole
                                @endif
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
