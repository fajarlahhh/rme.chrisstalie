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
        @php
            $total_tindakan = 0;
            $total_resep = 0;
            $total_bebas = 0;
            $total_diskon = 0;
            $total_total = 0;
        @endphp
        @foreach ($data as $row)
            @php
                $diskon = $row['diskon'];
                $tindakan = $row['registrasi_id'] ? $row['total_tindakan'] + $diskon : 0;
                $resep = $row['registrasi_id'] ? $row['total_resep'] : 0;
                $bebas = !$row['registrasi_id'] ? $row['total_harga_barang'] : 0;
                $total = $tindakan + $resep + $bebas - $diskon;
            @endphp
            <tr>
                <td>{{ $loop->iteration }}</td>
                <td>{{ $row['id'] }}</td>
                <td>
                    {{ isset($row['registrasi']) && isset($row['registrasi']['pasien']) && isset($row['registrasi']['pasien']['nama']) ? $row['registrasi']['pasien']['nama'] : '' }}
                </td>
                <td class="text-end">{{ $cetak ? $tindakan : number_format($tindakan) }}</td>
                <td class="text-end">{{ $cetak ? $bebas : number_format($bebas) }}</td>
                <td class="text-end">{{ $cetak ? $resep : number_format($resep) }}</td>
                <td class="text-end">{{ $cetak ? $diskon : number_format($diskon) }}</td>
                <td class="text-end">
                    {{ $cetak ? $total : number_format($total) }}
                </td>
                @role('administrator|supervisor')
                    <td>{{ $row['pengguna']['nama'] }}</td>
                @endrole
                <td>{{ $row['metode_bayar'] }}</td>
                <td>{{ $row['keterangan'] }}</td>
            </tr>
            @php
                $total_tindakan += $tindakan;
                $total_bebas += $bebas;
                $total_resep += $resep;
                $total_diskon += $diskon;
                $total_total += $total;
            @endphp
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="3">Total</th>
            <th class="text-end">
                {{ $cetak ? $total_tindakan : number_format($total_tindakan) }}
            </th>
            <th class="text-end">
                {{ $cetak ? $total_bebas : number_format($total_bebas) }}</th>
            <th class="text-end">{{ $cetak ? $total_resep : number_format($total_resep) }}
            </th>
            <th class="text-end">{{ $cetak ? $total_diskon : number_format($total_diskon) }}</th>
            <th class="text-end">
                {{ $cetak ? $total_total : number_format($total_total) }}
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
