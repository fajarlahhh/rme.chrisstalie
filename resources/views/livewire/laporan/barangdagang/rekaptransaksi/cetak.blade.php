@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-200px" alt="" />
        <br>
        <br>
        <h5>Laporan Rekap Transaksi Barang Dagang</h5>
        <hr>
    </div>
    <br>
    <table>
        <tr>
            <th class="w-100px">Periode</th>
            <th class="w-10px">:</th>
            <td>{{ $bulan }}</td>
        </tr>
        <tr>
            <th>Persediaan</th>
            <th class="w-10px">:</th>
            <td>{{ $persediaan ?? 'Semua Persediaan' }}</td>
        </tr>
        <tr>
            <th>Kategori</th>
            <th class="w-10px">:</th>
            <td>{{ $kode_akun ?? 'Semua Kategori' }}</td>
        </tr>
        <tr>
            <th>Kata Kunci</th>
            <th class="w-10px">:</th>
            <td>{{ $cari }}</td>
        </tr>
    </table>
@endif
<table class="table table-bordered table-hover">
    <thead>
        <tr>
            <th class="bg-gray-300 text-white">No.</th>
            <th class="bg-gray-300 text-white">Nama Barang</th>
            <th class="bg-gray-300 text-white">Satuan</th>
            <th class="bg-gray-300 text-white">Stok Awal</th>
            <th class="bg-gray-300 text-white">Stok Masuk</th>
            <th class="bg-gray-300 text-white">Stok Keluar</th>
            <th class="bg-gray-300 text-white">Stok Akhir</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $i => $row)
            <tr>
                @php
                    $stokAwal = $row->stokAwal
                        ->map(
                            fn($q) => [
                                'qty' => $q->qty,
                            ],
                        )
                        ->sum('qty');
                    $stokMasuk = $row->stokMasuk
                        ->map(
                            fn($q) => [
                                'qty' => $q->qty / $row->barangSatuanUtama?->rasio_dari_terkecil,
                            ],
                        )
                        ->sum('qty');
                    $stokKeluar = $row->stokKeluar
                        ->map(
                            fn($q) => [
                                'qty' => $q->qty / $row->barangSatuanUtama?->rasio_dari_terkecil,
                            ],
                        )
                        ->sum('qty');
                    $stokAkhir = $stokAwal + $stokMasuk - $stokKeluar;
                @endphp
                <td>{{ ++$i }}</td>
                <td>{{ $row->nama }}</td>
                <td>{{ $row->barangSatuanUtama?->nama }} {{ $row->barangSatuanUtama?->konversi_satuan }}</td>
                <td class="text-end">
                    @if ($stokAwal > 0)
                        <strong>{{ number_format($stokAwal, 2) }}</strong>
                    @else
                        0
                    @endif
                </td>
                <td class="text-end">
                    @if ($stokMasuk > 0)
                        <strong>{{ number_format($stokMasuk, 2) }}</strong>
                    @else
                        0
                    @endif
                </td>
                <td class="text-end">
                    @if ($stokKeluar > 0)
                        <strong>{{ number_format($stokKeluar, 2) }}</strong>
                    @else
                        0
                    @endif
                </td>
                <td class="text-end">
                    @if ($stokAkhir > 0)
                        <strong>{{ number_format($stokAkhir, 2) }}</strong>
                    @else
                        0
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
