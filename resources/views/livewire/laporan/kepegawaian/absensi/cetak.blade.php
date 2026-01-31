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
                <th rowspan="2" class="bg-red-100">Tanpa<br>Keterangan</th>
                <th rowspan="2" class="bg-orange-100">Telat</th>
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
                    <td>
                        @php
                            $hariKerja = collect($row['kepegawaian_absensi'])->count();
                        @endphp
                        @if ($hariKerja > 0)
                            <strong>{{ $hariKerja }}</strong>
                        @else
                            0
                        @endif
                    </td>
                    <td>
                        @php
                            $kepegawaianKehadiran = collect($row['kepegawaian_absensi'])->whereNotNull('masuk')->count();
                        @endphp
                        @if ($kepegawaianKehadiran > 0)
                            <strong>{{ $kepegawaianKehadiran }}</strong>
                        @else
                            0
                        @endif
                    </td>
                    <td class="bg-red-100">
                        @php
                            $tanpaKeterangan = collect($row['kepegawaian_absensi'])->whereNull('masuk')->count();
                        @endphp
                        @if ($tanpaKeterangan > 0)
                            <strong>{{ $tanpaKeterangan }}</strong>
                        @else
                            0
                        @endif
                    </td>
                    <td class="bg-orange-100">
                        @php
                            $telat = collect($row['kepegawaian_absensi'])
                                ->whereNotNull('masuk')
                                ->map(function ($item) {
                                    return $item['masuk'] > $item['jam_masuk'] ? 1 : 0;
                                })
                                ->sum();
                        @endphp
                        @if ($telat > 0)
                            <strong>{{ $telat }}</strong>
                        @else
                            0
                        @endif
                    </td>
                    <td>
                        @php
                            $sakit = collect($row['kepegawaian_absensi'])->where('izin', 'Sakit')->count();
                        @endphp
                        @if ($sakit > 0)
                            <strong>{{ $sakit }}</strong>
                        @else
                            0
                        @endif
                    </td>
                    <td>
                        @php
                            $izin = collect($row['kepegawaian_absensi'])->where('izin', 'Izin')->count();
                        @endphp
                        @if ($izin > 0)
                            <strong>{{ $izin }}</strong>
                        @else
                            0
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
@else
    @if ($kepegawaian_pegawai_id)
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th>No.</th>
                    <th>Tanggal</th>
                    <th>Jadwal Shift</th>
                    <th>Jam Masuk</th>
                    <th>Jam Pulang</th>
                    <th>Izin</th>
                    <th>Jumlah Jam Kerja</th>
                    <th>Jam Kerja Standar</th>
                    <th>Keterangan</th>
                </tr>
            </thead>
            <tbody>
                @foreach (collect($data)->first()['kepegawaian_absensi'] as $key => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $row['tanggal'] }}</td>
                        <td>{{ $row['jam_masuk'] }} s/d {{ $row['jam_pulang'] }}</td>
                        <td>
                            @if (!$row['izin'])
                                {{ $row['masuk'] }}
                            @endif
                        </td>
                        <td>
                            @if (!$row['izin'])
                                {{ $row['masuk'] != $row['pulang'] ? $row['pulang'] : null }}
                            @endif
                        </td>
                        <td>
                            @if (!$row['izin'])
                                {{ $row['izin'] }}
                            @endif
                        </td>
                        <td>
                            @if (!$row['izin'] && $row['masuk'] && $row['pulang'] && $row['pulang'] != $row['masuk'])
                                @php
                                    $detik = strtotime($row['pulang']) - strtotime($row['masuk']);
                                    $jam = floor($detik / 3600);
                                    $menit = floor(($detik % 3600) / 60);
                                    $jamKerja = sprintf('%02d:%02d', $jam, $menit);
                                @endphp
                                {{ $jamKerja }}
                            @endif
                        </td>
                        <td>
                            @php
                                $detikStandar = strtotime($row['jam_pulang']) - strtotime($row['jam_masuk']);
                                $jamStandar = floor($detikStandar / 3600);
                                $menitStandar = floor(($detikStandar % 3600) / 60);
                                $jamKerjaStandar = sprintf('%02d:%02d', $jamStandar, $menitStandar);
                            @endphp
                            {{ $jamKerjaStandar }}
                        </td>
                        <td>
                            @if ($row['masuk'])
                                @if ($row['masuk'] > $row['jam_masuk'])
                                    @php
                                        // Hitung jumlah waktu telat dalam menit
                                        $jamMasuk = strtotime($row['jam_masuk']);
                                        $masuk = strtotime($row['masuk']);
                                        $terlambat = $masuk - $jamMasuk;
                                        $telatJam = floor($terlambat / 3600);
                                        $telatMenit = floor(($terlambat % 3600) / 60);
                                    @endphp
                                    <span class="badge bg-warning">
                                        Telat
                                        @if ($telatJam > 0)
                                            {{ $telatJam }} jam
                                        @endif
                                        @if ($telatMenit > 0)
                                            {{ $telatMenit }} menit
                                        @elseif($telatJam == 0 && $telatMenit == 0 && $terlambat > 0)
                                            Kurang dari 1 menit
                                        @endif
                                    </span>
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
