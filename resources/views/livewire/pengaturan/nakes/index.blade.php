<div>
    @section('title', 'Nakes')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Nakes</li>
    @endsection

    <h1 class="page-header">Nakes</h1>
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
                    <select data-container="body" class="form-control "wire:model.lazy="aktif">
                        <option value="1">Aktif</option>
                        <option value="0">Non Aktif</option>
                    </select>&nbsp;
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>IHS</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Alamat</th>
                        <th>No. Telp.</th>
                        <th>Dokter</th>
                        <th>Pegawai</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>
                                {{ ($data->currentpage() - 1) * $data->perpage() + $loop->index + 1 }}
                            </td>
                            <td>{{ $row->ihs }}</td>
                            <td>{{ $row->nik }}</td>
                            <td>{{ $row->nama }}</td>
                            <td>{{ $row->alamat }}</td>
                            <td>{{ $row->no_hp }}</td>
                            <td>
                                <span
                                    class="badge bg-primary">{{ $row->kode_akun_jasa_dokter_id ? $row->kode_akun_jasa_dokter_id . ' - ' . $row->kodeAkunJasaDokter->nama : '' }}</span>
                            </td>
                            <td>
                                <span
                                    class="badge bg-primary">{{ $row->kode_akun_jasa_perawat_id ? $row->kode_akun_jasa_perawat_id . ' - ' . $row->kodeAkunJasaPerawat->nama : '' }}</span>
                            </td>
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
