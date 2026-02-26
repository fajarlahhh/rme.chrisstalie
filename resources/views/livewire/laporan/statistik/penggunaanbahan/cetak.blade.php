@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-200px" alt="" />
        <br>
        <br>
        <h5>Laporan Penggunaan Bahan</h5>
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
            <th class="bg-gray-300 text-white">No.</th>
            <th class="bg-gray-300 text-white">Nama Bahan</th>
            <th class="bg-gray-300 text-white">Qty</th>
            <th class="bg-gray-300 text-white">Total Biaya</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row['nama'] }}</td>
                <td class="text-end">{{ $cetak ? $row['qty'] : number_format($row['qty']) }}</td>
                <td class="text-end">{{ $cetak ? $row['biaya'] : number_format($row['biaya'], 2) }}</td>
            </tr>
        @endforeach
        <tr>
            <th colspan="3">TOTAL</th>
            <th class="text-end">
                {{ $cetak ? collect($data)->sum('biaya') : number_format(collect($data)->sum('biaya'), 2) }}</th>
        </tr>
    </tbody>
</table>
