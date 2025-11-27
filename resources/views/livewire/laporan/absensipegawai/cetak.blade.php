@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-200px" alt="" />
        <br>
        <br>
        <h5>Laporan Absensi Pegawai</h5>
        <hr>
    </div>
    <br>
    <table>
        <tr>
            <th class="w-100px">Periode</th>
            <th class="w-10px">:</th>
            <td>{{ $tanggal1 }} s/d {{ $tanggal2 }}</td>
        </tr>
    </table>
@endif
@if ($jenis == 'Rekap')
    <table class="table table-bordered table-hover">
        <thead>
            <tr>
                <th rowspan="2">No.</th>
                <th rowspan="2">Pegawai</th>
                <th rowspan="2">Jml.<br>Hari Kerja</th>
                <th rowspan="2">Kehadiran</th>
                <th rowspan="2">Tanpa<br>Keterangan</th>
                <th rowspan="2">Telat</th>
                <th colspan="2">Jenis Izin</th>
            </tr>
            <tr>
                <th>Sakit</th>
                <th>Izin</th>
            </tr>
        </thead>
        <tbody>
            @php
                $no = 0;
            @endphp
            @foreach ($data as $key => $row)
                <tr>
                    <td>{{ ++$no }}</td>
                    <td>{{ $row['nama'] }}</td>
                    <td>{{ count($row['absensi']) }}</td>
                    <td>{{ collect($row['absensi'])->whereNotNull('masuk')->count() }}</td>
                    <td>{{ collect($row['absensi'])->whereNotNull('masuk')->where('jam_masuk', '>', 'masuk')->count() }}
                    </td>
                    <td>{{ collect($row['absensi'])->whereNull('masuk')->count() }}</td>
                    <td>{{ collect($row['absensi'])->where('izin', 'Sakit')->count() }}</td>
                    <td>{{ collect($row['absensi'])->where('izin', 'Izin')->count() }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    @if ($pegawai_id)
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tanggal</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Izin</th>
                    <th>Jadwal Shift</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach (collect($data)->first()['absensi'] as $key => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['tanggal'] }}</td>
                        <td>{{ $row['masuk'] }}</td>
                        <td>{{ $row['pulang'] }}</td>
                        <td>{{ $row['izin'] }}</td>
                        <td>{{ $row['jam_masuk'] }} s/d {{ $row['jam_pulang'] }}</td>
                        <td>
                            @if ($row['masuk'])
                                @if ($row['masuk'] > $row['jam_masuk'])
                                    <span class="badge bg-warning">Telat</span>
                                @endif
                            @else
                                <span class="badge bg-danger">Tanpa Keterangan</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif
@endif
