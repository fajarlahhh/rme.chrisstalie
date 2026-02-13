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
                    <input class="form-control" type="date" wire:model.lazy="tanggal1"
                        max="{{ date('Y-m-d') }}" />&nbsp;
                    <input class="form-control" type="date" wire:model.lazy="tanggal2"
                        max="{{ date('Y-m-d') }}" />&nbsp;
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
                        <th>No. Nota</th>
                        <th>Tanggal</th>
                        <th>Data Registrasi</th>
                        <th>Data Pasien</th>
                        <th>Tagihan</th>
                        <th>Pembayaran</th>
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
                            <td>{{ $row->tanggal }}</td>
                            <td>
                                @if ($row->registrasi)
                                    <small>
                                        No. Registrasi : {{ $row->registrasi->id }}<br>
                                        Tgl. Registrasi : {{ $row->registrasi->tanggal }}<br>
                                        No. RM : {{ $row->registrasi->pasien->id }}<br>
                                        Nama Pasien : {{ $row->registrasi->pasien->nama }}<br>
                                        Alamat : {{ $row->registrasi->pasien->alamat }}
                                    </small>
                                @endif
                            </td>
                            <td>
                                @if ($row->pasien)
                                    <small>
                                        No. RM : {{ $row->pasien_id }}<br>
                                        Nama Pasien : {{ $row->pasien->nama }}<br>
                                        Alamat : {{ $row->pasien->alamat }}
                                    </small>
                                @endif
                            </td>
                            <td nowrap>
                                <small>
                                    <ul>
                                        <li>Total Tindakan :
                                            <strong>{{ number_format($row->total_tindakan + $row->diskon) }}</strong>
                                        </li>
                                        <li>Total Resep : <strong>{{ number_format($row->total_resep) }}</strong></li>
                                        <li>Total Barang :
                                            <strong>{{ number_format($row->total_harga_barang) }}</strong>
                                        </li>
                                        <li>Total Diskon :
                                            <strong>{{ number_format($row->total_diskon_barang + $row->total_diskon_tindakan + $row->diskon) }}</strong>
                                        </li>
                                        <li>Total Tagihan :
                                            <strong>{{ number_format($row->total_tagihan) }}</strong>
                                        </li>
                                    </ul>
                                </small>
                            </td>
                            <td nowrap>
                                <small>
                                    <ul>
                                        <li>No. Nota : <strong>{{ $row->id }}</strong></li>
                                        <li><strong>{{ $row->metode_bayar }} :
                                                {{ number_format($row->bayar) }}</strong></li>
                                        @if ($row->metode_bayar_2)
                                            <li><strong>{{ $row->metode_bayar_2 }} :
                                                    {{ number_format($row->bayar_2) }}</strong></li>
                                        @endif
                                        <li>Kasir : <strong>{{ $row->pengguna->nama }}</strong></li>
                                        <li>Tanggal Input : <strong>{{ $row->created_at }}</strong></li>
                                    </ul>
                                </small>
                            </td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor|operator')
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
