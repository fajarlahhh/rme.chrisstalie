<div>
    @section('title', 'Kasir')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item active">Kasir</li>
    @endsection

    <h1 class="page-header">Kasir</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
                    <select data-container="body" class="form-control" wire:model.lazy="status">
                        <option value="1">Belum Bayar</option>
                        <option value="2">Sudah Bayar</option>
                    </select>&nbsp;
                    @if ($status == 2)
                        <input class="form-control" type="date" wire:model.lazy="tanggal1"
                            max="{{ date('Y-m-d') }}" />&nbsp;
                        <input class="form-control" type="date" wire:model.lazy="tanggal2"
                            max="{{ date('Y-m-d') }}" />&nbsp;
                    @endif
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
                        <th>No. Registrasi</th>
                        <th>No. Nota</th>
                        <th>Tgl. Registrasi</th>
                        <th>RM</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Tanggal Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>Alamat</th>
                        <th>No. Telp.</th>
                        <th>Catatan</th>
                        @if ($status == 1)
                            <th>Proses</th>
                        @endif
                        @if ($status == 2)
                            <th>Jumlah</th>
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
                            <td>{{ $this->status == 1 ? $row->id : $row->registrasi->id }}</td>
                            <td>{{ $this->status == 1 ? $row->id : $row->id }}</td>
                            <td>{{ $this->status == 1 ? $row->tanggal : $row->registrasi->tanggal }}</td>
                            <td>{{ $this->status == 1 ? $row->pasien->id : $row->registrasi->pasien->id }}</td>
                            <td>{{ $this->status == 1 ? $row->pasien->nama : $row->registrasi->pasien->nama }}</td>
                            <td>{{ $this->status == 1 ? $row->pasien->nik : $row->registrasi->pasien->nik }}</td>
                            <td>{{ $this->status == 1 ? $row->pasien->tanggal_lahir->format('d-m-Y') : $row->registrasi->pasien->tanggal_lahir->format('d-m-Y') }}
                            </td>
                            <td>{{ $this->status == 1 ? $row->pasien->jenis_kelamin : $row->registrasi->pasien->jenis_kelamin }}
                            </td>
                            <td>{{ $this->status == 1 ? $row->pasien->alamat : $row->registrasi->pasien->alamat }}</td>
                            <td>{{ $this->status == 1 ? $row->pasien->no_hp : $row->registrasi->pasien->no_hp }}</td>
                            <td>{{ $this->status == 1 ? $row->catatan : $row->registrasi->catatan }}</td>
                            @if ($status == 1)
                                <td nowrap>
                                    Tindakan : {!! $row->tindakan->count() > 0
                                        ? '<span class="badge bg-success">' . $row->tindakan->first()->created_at . '</span>'
                                        : '' !!}
                                    <br>
                                    Resep : {!! $row->resepObat->count() > 0
                                        ? '<span class="badge bg-success">' . $row->resepObat->first()->created_at . '</span>'
                                        : '' !!}
                                    <br>
                                    @if ($row->resepObat->count() > 0)
                                        Peracikan Resep : {!! $row->peracikanResepObat
                                            ? '<span class="badge bg-success">' . $row->peracikanResepObat->created_at . '</span>'
                                            : '' !!}
                                    @endif
                                </td>
                            @endif
                            @if ($status == 2)
                                <td nowrap>
                                    <small>
                                        <ul>
                                            <li>Total Tindakan :
                                                <b>{{ number_format($row->total_tindakan + $row->diskon) }}</b>
                                            </li>
                                            <li>Total Resep : <b>{{ number_format($row->total_resep) }}</b></li>
                                            <li>Total Diskon : <b>{{ number_format($row->diskon) }}</b></li>
                                            <li>Total :
                                                <b>{{ number_format($row->total_tindakan + $row->total_resep) }}</b>
                                            </li>
                                        </ul>
                                    </small>
                                </td>
                                <td nowrap>
                                    <small>
                                        <ul>
                                            <li>No. Nota : <b>{{ $row->id }}</b></li>
                                            <li>Metode Bayar : <b>{{ $row->metode_bayar }}</b></li>
                                            <li>Kasir : <b>{{ $row->pengguna->nama }}</b></li>
                                            <li>Tanggal Input : <b>{{ $row->created_at }}</b></li>
                                        </ul>
                                    </small>
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
                                                $row->pengguna->nama .
                                                '<br>' .
                                                $row->updated_at .
                                                '</a>';
                                        @endphp
                                        @role('administrator|supervisor')
                                            <x-action :row="$row" custom="" :detail="false" :edit="false"
                                                :print="true" :permanentdelete="false" :restore="false" :delete="true" />
                                        @endrole
                                        {{-- @role('supervisor')
                                            @if (substr($row->created_at, 0, 10) == date('Y-m-d'))
                                                <x-action :row="$row" custom="" :detail="false" :edit="false"
                                                    :print="true" :permanentdelete="false" :restore="false"
                                                    :delete="true" />
                                            @else
                                                <x-action :row="$row" custom="" :detail="false"
                                                    :edit="false" :print="true" :permanentdelete="false" :restore="false"
                                                    :delete="false" />
                                            @endif
                                        @endrole --}}
                                        @role('operator')
                                            <x-action :row="$row" custom="" :detail="false" :edit="false"
                                                :print="true" :permanentdelete="false" :restore="false" :delete="false" />
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
    <x-modal.cetak judul='Nota' />

    <div wire:loading>
        <x-loading />
    </div>
</div>
