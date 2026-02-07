<div>
    @section('title', 'Pemeriksaan Awal')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item active">Pemeriksaan Awal</li>
    @endsection

    <h1 class="page-header">Pemeriksaan Awal</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <select data-container="body" class="form-control" wire:model.lazy="status">
                        <option value="1">Belum Proses</option>
                        <option value="2">Sudah Proses</option>
                    </select>&nbsp;
                    @if ($status == 2)
                        <input class="form-control" type="date" wire:model.lazy="tanggal"
                            max="{{ date('Y-m-d') }}" />&nbsp;
                    @endif
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
                        <th>No. Registrasi</th>
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
                            <td>{{ $row->id }}</td>
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
                                    @if ($status == 1)
                                        <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form/{{ $row['id'] }}'"
                                            class="btn btn-primary btn-sm">
                                            Input
                                        </a>
                                    @else
                                        @php
                                            $custom =
                                                "<hr class='dropdown-divider'></li><a href='javascript:;'class='dropdown-item fs-8px'>" .
                                                $row->pemeriksaanAwal->pengguna->nama .
                                                '<br>' .
                                                $row->pemeriksaanAwal->updated_at .
                                                '</a>';
                                        @endphp

                                        @if ($row->pembayaran)
                                            <x-action :row="$row" :custom="$custom" :detail="false"
                                                :edit="true" :information="false" :print="false" :permanentdelete="false"
                                                :restore="false" :delete="false" />
                                        @else
                                            <x-action :row="$row" :custom="$custom" :detail="false"
                                                :edit="true" :information="false" :print="false" :permanentdelete="false"
                                                :restore="false" :delete="true" />
                                        @endif
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
