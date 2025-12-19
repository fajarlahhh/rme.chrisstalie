@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-200px" alt="" />
        <br>
        <br>
        <h5>Laporan Jasa Perawat</h5>
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
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="bg-gray-300 text-white" rowspan="2">No.</th>
            <th class="bg-gray-300 text-white" rowspan="2">No. Nota</th>
            <th class="bg-gray-300 text-white" rowspan="2">Tanggal</th>
            <th class="bg-gray-300 text-white" rowspan="2">Nama Pasien</th>
            <th class="bg-gray-300 text-white" rowspan="2">Tindakan</th>
            <th class="bg-gray-300 text-white" colspan="{{ collect($data)->groupBy('perawat_id')->count() }}">Nama
                Perawat</th>
        </tr>
        <tr>
            @foreach (collect($data)->groupBy('perawat_id') as $key => $item)
                <th class="bg-gray-300 text-white">{{ $item->first()['nama_petugas'] }}</th>
            @endforeach
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['no_nota'] }}</td>
                <td>{{ substr($row['tanggal'], 0, 10) }}</td>
                <td>{{ $row['nama_pasien'] }}</td>
                <td>{{ $row['nama_tindakan'] }}</td>
                @foreach (collect($data)->groupBy('perawat_id') as $key => $item)
                    <td class="text-end">
                        @if ($row['perawat_id'] == $key)
                            {{ $cetak ? $row['biaya'] : number_format($row['biaya']) }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endforeach
        <tr>
            <th colspan="4">Total</th>
            @foreach (collect($data)->groupBy('perawat_id') as $key => $item)
                <th class="text-end">
                    {{ $cetak ? collect($data)->where('perawat_id', $key)->sum('biaya') : number_format(collect($data)->where('perawat_id', $key)->sum('biaya')) }}
                </th>
            @endforeach
        </tr>
    </tbody>
</table>
