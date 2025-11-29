@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-200px" alt="" />
        <br>
        <br>
        <h5>Laporan Harian Kas</h5>
        <hr>
    </div>
    <br>
    <table>
        <tr>
            <th class="w-100px">Periode</th>
            <th class="w-10px">:</th>
            <td>{{ $tanggal1 }} s/d {{ $tanggal2 }}</td>
        </tr>
        <tr>
            <th class="w-100px">Kasir</th>
            <th class="w-10px">:</th>
            <td>{{ $pengguna ? $pengguna : 'Semua Kasir' }}</td>
        </tr>
        <tr>
            <th class="w-100px">Metode Bayar</th>
            <th class="w-10px">:</th>
            <td>{{ $metode_bayar ? $metode_bayar : 'Semua Metode Bayar' }}</td>
        </tr>
    </table>
@endif
<table class="table table-bordered">
    <thead>
        <tr>
            <th class="bg-gray-300 text-white">No.</th>
            <th class="bg-gray-300 text-white">No. Nota</th>
            <th class="bg-gray-300 text-white">Pasien</th>
            <th class="bg-gray-300 text-white">Tindakan</th>
            <th class="bg-gray-300 text-white">Penjualan Bebas</th>
            <th class="bg-gray-300 text-white">Resep</th>
            <th class="bg-gray-300 text-white">Diskon</th>
            <th class="bg-gray-300 text-white">Total</th>
            @role('administrator|supervisor')
                <th class="bg-gray-300 text-white">Kasir</th>
            @endrole
            <th class="bg-gray-300 text-white">Metode Bayar</th>
            <th class="bg-gray-300 text-white">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row['id'] }}</td>
                <td>
                    {{ isset($row['registrasi']) && isset($row['registrasi']['pasien']) && isset($row['registrasi']['pasien']['nama']) ? $row['registrasi']['pasien']['nama'] : '' }}
                </td>
                <td class="text-end">{{ $cetak ? $row['total_tindakan'] : number_format($row['total_tindakan']) }}
                </td>
                <td class="text-end">
                    {{ $cetak ? $row['total_harga_barang'] : number_format($row['total_harga_barang']) }}</td>
                <td class="text-end">{{ $cetak ? $row['total_resep'] : number_format($row['total_resep']) }}</td>
                <td class="text-end">{{ $cetak ? $row['diskon'] : number_format($row['diskon']) }}</td>
                <td class="text-end">
                    {{ $cetak
                        ? $row['total_tindakan'] + $row['total_harga_barang'] + $row['total_resep'] - $row['diskon']
                        : number_format($row['total_tindakan'] + $row['total_harga_barang'] + $row['total_resep'] - $row['diskon']) }}
                </td>
                @role('administrator|supervisor')
                    <td>{{ $row['pengguna']['nama'] }}</td>
                @endrole
                <td>{{ $row['metode_bayar'] }}</td>
                <td>{{ $row['keterangan'] }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total</th>
            <th class="text-end">
                {{ $cetak ? $data->sum('total_tindakan') : number_format($data->sum('total_tindakan')) }}</th>
            <th class="text-end">
                {{ $cetak ? $data->sum('total_harga_barang') : number_format($data->sum('total_harga_barang')) }}</th>
            <th class="text-end">{{ $cetak ? $data->sum('total_resep') : number_format($data->sum('total_resep')) }}
            </th>
            <th class="text-end">{{ $cetak ? $data->sum('diskon') : number_format($data->sum('diskon')) }}</th>
            <th class="text-end">
                {{ $cetak
                    ? $data->sum(fn($row) => $row['total_tindakan'] + $row['total_harga_barang'] + $row['total_resep'] - $row['diskon'])
                    : number_format(
                        $data->sum(
                            fn($row) => $row['total_tindakan'] + $row['total_harga_barang'] + $row['total_resep'] - $row['diskon'],
                        ),
                    ) }}
            </th>
            @role('administrator|supervisor')
                <th colspan="2"></th>
            @endrole
            @role('operator')
                <th colspan="3"></th>
            @endrole
        </tr>
        <tr>
            <th class="bg-gray-300 text-white">No.</th>
            <th class="bg-gray-300 text-white">No. Nota</th>
            <th class="bg-gray-300 text-white">Pasien</th>
            <th class="bg-gray-300 text-white">Tindakan</th>
            <th class="bg-gray-300 text-white">Penjualan Bebas</th>
            <th class="bg-gray-300 text-white">Resep</th>
            <th class="bg-gray-300 text-white">Diskon</th>
            <th class="bg-gray-300 text-white">Total</th>
            @role('administrator|supervisor')
                <th class="bg-gray-300 text-white">Kasir</th>
            @endrole
            <th class="bg-gray-300 text-white">Metode Bayar</th>
            <th class="bg-gray-300 text-white">Keterangan</th>
        </tr>
    </tfoot>
</table>
