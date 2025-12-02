<div>
    @section('title', 'Home')

    <!-- BEGIN page-header -->
    <h1 class="page-header">Hai, Selamat @php
        $hour = date('H');
    @endphp
        @if ($hour >= 3 && $hour < 10)
            Pagi
        @elseif($hour >= 10 && $hour < 15)
            Siang
        @elseif($hour >= 15 && $hour < 18)
            Sore
        @elseif($hour >= 18 && $hour < 19)
            Petang
        @elseif(($hour >= 19 && $hour < 24) || ($hour >= 0 && $hour < 3))
            Malam
        @endif
        <strong>{{ auth()->user()->nama }}</strong>
    </h1>
    <!-- END page-header -->

    <div class="row">
        <div class="col-12">
            <div class="row">
                <div class="col-xl-3 col-md-6">
                    <div class="widget widget-stats bg-teal">
                        <div class="stats-icon stats-icon-lg"><i class="fa fa-users fa-fw"></i></div>
                        <div class="stats-content">
                            <div class="stats-title">KUNJUNGAN BULAN INI</div>
                            <div class="stats-number">{{ $dataPembayaranBulanIni->count() }}</div>
                            <div class="stats-progress progress">
                                <div class="progress-bar" style="width: 100%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
                @role('administrator')
                    <div class="col-xl-3 col-md-6">
                        <div class="widget widget-stats bg-blue">
                            <div class="stats-icon stats-icon-lg"><i class="fa fa-dollar fa-fw"></i></div>
                            <div class="stats-content">
                                <div class="stats-title">PENERIMAAN BULAN INI</div>
                                <div class="stats-number text-end">
                                    {{ number_format($dataPembayaranBulanIni->sum('total_tagihan'), 2) }}
                                </div>
                                <div class="stats-progress progress">
                                    <div class="progress-bar" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xl-3 col-md-6">
                        <div class="widget widget-stats bg-red">
                            <div class="stats-icon stats-icon-lg"><i class="fa fa-dollar fa-fw"></i></div>
                            <div class="stats-content">
                                <div class="stats-title">PENGELUARAN BULAN INI</div>
                                <div class="stats-number text-end">
                                    {{ number_format($dataPengeluaranBulanIni->sum('debet'), 2) }}
                                </div>
                                <div class="stats-progress progress">
                                    <div class="progress-bar" style="width: 100%;"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endrole
            </div>
        </div>
        @if (auth()->user()->pegawai_id)
            <div class="col-sm-12 col-md-12 col-lg-12 col-xl-6 col-xxl-6 col-xs-12">
                <div class="panel panel-inverse" data-sortable-id="index-6">
                    <div class="panel-heading ui-sortable-handle">
                        <h4 class="panel-title">Jadwal Shift</h4>
                        <div class="panel-heading-btn">
                            <input type="month" class="form-control w-auto" wire:model.lazy="bulanShift">
                        </div>
                    </div>
                    <div class="row text-center p-2">
                        @foreach ($dataJadwalShift as $i => $row)
                            <div class="col-xl-2 col-lg-3 col-md-4 col-sm-4 col-4">
                                <div
                                    class="card w-100 text-center  @if ($dataJadwalShift[$i]['absen'] !== false) border-1 bg-cyan-100 border-secondary @else border-1 bg-yellow-100 border-secondary @endif mb-2">
                                    <div class="card-body p-1">
                                        <strong>{{ \Carbon\Carbon::parse($row['tanggal'])->format('d') }}</strong><br>
                                        @if ($row['jam_masuk'] && $row['jam_pulang'])
                                            {{ \Carbon\Carbon::parse($row['jam_masuk'])->format('H:i') }} s/d
                                            {{ \Carbon\Carbon::parse($row['jam_pulang'])->format('H:i') }}
                                            <div class="row">
                                                <div class="col-md-6 col-sm-6 col-6">
                                                    <badge class="badge bg-success">Masuk</badge><br>
                                                    <small>
                                                        {{ \Carbon\Carbon::parse($row['masuk'])->format('H:i') }}
                                                    </small>
                                                </div>
                                                <div class="col-md-6 col-sm-6 col-6">
                                                    <badge class="badge bg-danger">Pulang</badge><br>
                                                    <small>
                                                        {{ \Carbon\Carbon::parse($row['pulang'])->format('H:i') }}
                                                    </small>
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        @endif
        <div class="col-sm-12 col-md-12 col-lg-12 col-xl-6 col-xxl-6 col-xs-12">
            <div class="panel panel-inverse" data-sortable-id="index-6">
                <div class="panel-heading ui-sortable-handle">
                    <h4 class="panel-title">Pengadaan Barang Jatuh Tempo</h4>
                </div>
                <div class="table-responsive">
                    <table class="table table-panel align-middle mb-0">
                        <thead>
                            <tr>
                                <th>No. Faktur</th>
                                <th>Vendor</th>
                                <th>Tanggal Jatuh Tempo</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($dataPengadaanBarangJatuhTempo as $row)
                                <tr>
                                    <td nowrap="" nowrap>{{ $row->uraian }}</td>
                                    <td nowrap="" nowrap>{{ $row->supplier?->nama }}</td>
                                    <td nowrap="" nowrap>
                                        @if ($row->jatuh_tempo < date('Y-m-d'))
                                            <span class="badge bg-danger">{{ $row->jatuh_tempo }}</span>
                                        @elseif ($row->jatuh_tempo = date('Y-m-d'))
                                            <span class="badge bg-warning">{{ $row->jatuh_tempo }}</span>
                                        @elseif ($row->jatuh_tempo > date('Y-m-d'))
                                            <span class="badge bg-success">{{ $row->jatuh_tempo }}</span>
                                        @endif
                                    </td>
                                    <td class="text-end" nowrap>Rp. {{ number_format($row->total_harga) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
