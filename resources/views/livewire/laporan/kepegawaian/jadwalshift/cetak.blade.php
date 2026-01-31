@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-200px" alt="" />
        <br>
        <br>
        <h5>Jadwal Shift</h5>
        <hr>
    </div>
    <br>
    <table>
        <tr>
            <th class="w-100px">Periode</th>
            <th class="w-10px">:</th>
            <td>{{ $bulan }}</td>
        </tr>
    </table>
@endif
<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th rowspan="2">No.</th>
            <th rowspan="2">Pegawai</th>
            <th colspan="3">Jadwal Shift</th>
        </tr>
        <tr>
            <th>Tanggal</th>
            <th>Masuk</th>
            <th>Pulang</th>
        </tr>
    </thead>
    <tbody>
        @php
            $no = 0;
        @endphp
        @foreach ($data as $key => $row)
            <tr>
                <td rowspan="{{ collect($row['kepegawaian_absensi'])->count() + 1 }}">{{ ++$no }}</td>
                <td rowspan="{{ collect($row['kepegawaian_absensi'])->count() + 1 }}">{{ $row['nama'] }}</td>
            </tr>
            @foreach ($row['kepegawaian_absensi'] as $key => $subRow)
                <tr>
                    @if ($subRow['jam_masuk'])
                        <td>{{ $subRow['tanggal'] }}</td>
                        <td>{{ $subRow['jam_masuk'] }}</td>
                        <td>{{ $subRow['jam_pulang'] }}</td>
                    @else
                        <td>{{ $subRow['tanggal'] }}</td>
                        <td></td>
                        <td></td>
                    @endif
                </tr>
            @endforeach
        @endforeach
    </tbody>
</table>
