<div>
    @section('title', 'Data Site Marking')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item">Site Marking</li>
        <li class="breadcrumb-item active">Data</li>
    @endsection

    <h1 class="page-header">Site Marking <small>Data</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input class="form-control" type="date" wire:model.lazy="tanggal" />&nbsp;
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
                        <th>RM</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Tanggal Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>Alamat</th>
                        <th>No. Telp.</th>
                        <th>Catatan</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>
                                {{ ($data->currentpage() - 1) * $data->perpage() + $loop->index + 1 }}
                            </td>
                            <td>{{ $row->pasien->id }}</td>
                            <td>{{ $row->pasien->nama }}</td>
                            <td>{{ $row->pasien->nik }}</td>
                            <td>{{ $row->pasien->tanggal_lahir->format('d-m-Y') }}</td>
                            <td>{{ $row->pasien->jenis_kelamin }}</td>
                            <td>{{ $row->pasien->alamat }}</td>
                            <td>{{ $row->pasien->no_hp }}</td>
                            <td>{{ $row->catatan }}</td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor|operator')
                                    @if (!$row->payment)
                                        <x-action :row="$row" :custom="false" :detail="false" :edit="false"
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
</div>
