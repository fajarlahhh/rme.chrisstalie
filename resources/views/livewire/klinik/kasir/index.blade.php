<div>
    @section('title', 'Kasir')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item active">Kasir</li>
    @endsection

    <h1 class="page-header">Kasir</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <select data-container="body" class="form-control" wire:model.lazy="status">
                        <option value="1">Belum Bayar</option>
                        <option value="2">Sudah Bayar</option>
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
                        @if ($status == 2)
                            <th>Pembayaran</th>
                        @endif
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
                            @if ($status == 2)
                                <td>No. Nota : <b>{{ $row->pembayaran->id }}</b>
                                    <br>Metode Bayar :
                                    <b>{{ $row->pembayaran->metode_bayar }}</b>
                                    <br>Kasir :
                                    <b>{{ $row->pembayaran->pengguna->nama }}</b>
                                    <br>Waktu :
                                    <b>{{ $row->pembayaran->created_at }}</b>
                                </td>
                            @endif
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
                                                $row->pembayaran->pengguna->nama .
                                                '<br>' .
                                                $row->pembayaran->updated_at .
                                                '</a>';
                                        @endphp
                                        @role('administrator')
                                            <x-action :row="$row" custom="" :detail="false" :edit="false"
                                                :print="true" :permanentDelete="false" :restore="false" :delete="true" />
                                        @endrole
                                        @role('supervisor')
                                            @if (substr($row->created_at, 0, 10) == date('Y-m-d'))
                                                <x-action :row="$row" custom="" :detail="false" :edit="false"
                                                    :print="true" :permanentDelete="false" :restore="false"
                                                    :delete="true" />
                                            @else
                                                <x-action :row="$row" custom="" :detail="false"
                                                    :edit="false" :print="true" :permanentDelete="false" :restore="false"
                                                    :delete="false" />
                                            @endif
                                        @endrole
                                        @role('operator')
                                            <x-action :row="$row" custom="" :detail="false" :edit="false"
                                                :print="true" :permanentDelete="false" :restore="false" :delete="false" />
                                        @endrole
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
    <x-modal.cetak judul='Nota' />
</div>
