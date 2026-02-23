@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-200px" alt="" />
        <br>
        <br>
        <h5>Laporan Penerimaan</h5>
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
            <th class="bg-gray-300 text-white">Nama</th>
            <th class="bg-gray-300 text-white">Alamat</th>
            <th class="bg-gray-300 text-white">Jenis Kelamin</th>
            <th class="bg-gray-300 text-white">Tindakan</th>
            <th class="bg-gray-300 text-white">Resep</th>
            <th class="bg-gray-300 text-white">Penjualan Barang</th>
            <th class="bg-gray-300 text-white">Total Sebelum Diskon</th>
            <th class="bg-gray-300 text-white">Diskon</th>
            <th class="bg-gray-300 text-white">Total Setelah Diskon</th>
            @role('administrator|supervisor')
                <th class="bg-gray-300 text-white">Kasir</th>
            @endrole
            <th class="bg-gray-300 text-white">Metode Bayar</th>
            <th class="bg-gray-300 text-white">Keterangan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $row)
            @php
                $diskon = $row['total_diskon_barang'] + $row['total_diskon_tindakan'] + $row['diskon'];
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row['id'] }}</td>
                <td>
                    {{ isset($row['registrasi']) && isset($row['registrasi']['pasien']) && isset($row['registrasi']['pasien']['nama']) ? $row['registrasi']['pasien']['nama'] : '' }}
                </td>
                <td>
                    {{ isset($row['registrasi']) && isset($row['registrasi']['pasien']) && isset($row['registrasi']['pasien']['alamat']) ? $row['registrasi']['pasien']['alamat'] : '' }}
                </td>
                <td>
                    {{ isset($row['registrasi']) && isset($row['registrasi']['pasien']) && isset($row['registrasi']['pasien']['jenis_kelamin']) ? $row['registrasi']['pasien']['jenis_kelamin'] : '' }}
                </td>
                <td class="text-end">{{ $cetak ? $row['total_tindakan'] : number_format($row['total_tindakan']) }}</td>
                <td class="text-end">{{ $cetak ? $row['total_resep'] : number_format($row['total_resep']) }}</td>
                <td class="text-end">
                    {{ $cetak ? $row['total_harga_barang'] : number_format($row['total_harga_barang']) }}</td>
                <td class="text-end">
                    {{ $cetak ? $row['total_tindakan'] + $row['total_resep'] + $row['total_harga_barang'] : number_format($row['total_tindakan'] + $row['total_resep'] + $row['total_harga_barang']) }}
                </td>
                <td class="text-end">{{ $cetak ? $diskon : number_format($diskon) }}</td>
                <td class="text-end">
                    {{ $cetak ? $row['total_tagihan'] : number_format($row['total_tagihan']) }}
                </td>
                @role('administrator|supervisor')
                    <td>{{ $row['pengguna']['nama'] }}</td>
                @endrole
                <td>{{ $row['metode_bayar'] }} : {{ number_format($row['bayar']) }}
                    @if ($row['metode_bayar_2'])
                        -
                        {{ $row['metode_bayar_2'] ? $row['metode_bayar_2'] . ' : ' . number_format($row['bayar_2']) : '' }}
                    @endif
                </td>
                <td>{{ $row['keterangan'] }}</td>
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="5">Total</th>
            <th class="text-end">
                {{ $cetak ? $data->sum('total_tindakan') : number_format($data->sum('total_tindakan')) }}
            </th>
            <th class="text-end">{{ $cetak ? $data->sum('total_resep') : number_format($data->sum('total_resep')) }}
            </th>
            <th class="text-end">
                {{ $cetak ? $data->sum('total_harga_barang') : number_format($data->sum('total_harga_barang')) }}</th>
            <th class="text-end">
                {{ $cetak ? $data->sum('total_tindakan') + $data->sum('total_resep') + $data->sum('total_harga_barang') : number_format($data->sum('total_tindakan') + $data->sum('total_resep') + $data->sum('total_harga_barang')) }}
            </th>
            <th class="text-end">
                {{ $cetak ? $data->sum('total_diskon_barang') + $data->sum('total_diskon_tindakan') + $data->sum('diskon') : number_format($data->sum('total_diskon_barang') + $data->sum('total_diskon_tindakan') + $data->sum('diskon')) }}
            </th>
            <th class="text-end">
                {{ $cetak ? $data->sum('total_tagihan') : number_format($data->sum('total_tagihan')) }}
            </th>
            @role('administrator|supervisor')
                <th colspan="3"></th>
            @endrole
            @role('operator')
                <th colspan="2"></th>
            @endrole
        </tr>
    </tfoot>
</table>
