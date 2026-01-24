@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-200px" alt="" />
        <br>
        <br>
        <h5>Laporan Pengeluaran</h5>
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
            <th class="w-100px">Operator</th>
            <th class="w-10px">:</th>
            <td>{{ $pengguna ? $pengguna : 'Semua Operator' }}</td>
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
            <th class="bg-gray-300 text-white">No. Jurnal</th>
            <th class="bg-gray-300 text-white">Tanggal</th>
            <th class="bg-gray-300 text-white">Uraian</th>
            <th class="bg-gray-300 text-white">Sub Jenis</th>
            <th class="bg-gray-300 text-white">Metode Bayar</th>
            <th class="bg-gray-300 text-white">Operator</th>
            <th class="bg-gray-300 text-white">Nilai</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $row)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td>{{ $row->nomor }}</td>
                <td nowrap>{{ $row->tanggal }}</td>
                <td>{{ $row->uraian }}</td>
                <td>{{ $row->sub_jenis }}</td>
                <td nowrap>
                    {{ $row->jurnalKeuanganDetail->whereIn('kode_akun_id', collect($dataKodeAkun)->pluck('id'))->first()->kodeAkun->nama }}
                </td>
                <td nowrap>{{ $row['pengguna']['nama'] }}</td>
                <td class="text-end">
                    {{ $cetak ? $row->jurnalKeuanganDetail->sum('kredit') : number_format($row->jurnalKeuanganDetail->sum('kredit')) }}
                </td>
            </tr>
        @endforeach
        <tr>
            <th colspan="7">TOTAL</th>
            <th class="text-end">
                {{ $cetak ? $data->sum(fn($row) => $row->jurnalKeuanganDetail->sum('kredit')) : number_format($data->sum(fn($row) => $row->jurnalKeuanganDetail->sum('kredit'))) }}
            </th>
        </tr>
    </tbody>
</table>
