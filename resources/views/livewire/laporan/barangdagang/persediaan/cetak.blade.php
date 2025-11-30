@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-200px" alt="" />
        <br>
        <br>
        <h5>Laporan Persediaan Barang Dagang</h5>
        <hr>
    </div>
    <br>
    <table>
        <tr>
            <th class="w-100px">Periode</th>
            <th class="w-10px">:</th>
            <td>{{ now() }}</td>
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
            <th class="w-10px bg-gray-300 text-white">No.</th>
            <th class="bg-gray-300 text-white">Nama</th>
            <th class="bg-gray-300 text-white">Satuan</th>
            <th class="bg-gray-300 text-white">Kategori</th>
            <th class="bg-gray-300 text-white">Tanggal Kedaluarsa</th>
            <th class="bg-gray-300 text-white">Harga Beli</th>
            <th class="bg-gray-300 text-white">Stok</th>
            <th class="bg-gray-300 text-white">Total Persediaan</th>
        </tr>
    </thead>
    <tbody>
        @php
            $total = 0;
        @endphp
        @foreach ($data as $item)
            @php
                $stok = $dataStok->where('barang_id', $item->id)->map(function ($q) use ($item) {
                    return [
                        'tanggal_kedaluarsa' => $q->tanggal_kedaluarsa,
                        'harga_beli' => $q->harga_beli,
                        'stok' => $q->stok / $item->barangSatuanUtama?->rasio_dari_terkecil,
                        'total' => ($q->harga_beli / $item->barangSatuanUtama?->rasio_dari_terkecil) * $q->stok,
                    ];
                });
                $total += $stok->sum(fn($q) => $q['total']);
            @endphp
            <tr @if ($stok->count() > 0) class="bg-green-100" @endif>
                <td @if ($stok->count() > 0) rowspan="{{ $stok->count() + 1 }}" @endif>
                    {{ $loop->iteration }}</td>
                <td nowrap @if ($stok->count() > 0) rowspan="{{ $stok->count() + 1 }}" @endif>
                    {{ $item->nama }}</td>
                <td nowrap @if ($stok->count() > 0) rowspan="{{ $stok->count() + 1 }}" @endif>
                    {{ $item->barangSatuanUtama?->nama }}
                    {{ $item->barangSatuanUtama?->konversi_satuan }}</td>
                <td nowrap @if ($stok->count() > 0) rowspan="{{ $stok->count() + 1 }}" @endif>
                    {{ $item->kode_akun_id }} - {{ $item->kodeAkun?->nama }}</td>
                @if ($stok->count() == 0)
                    <td nowrap></td>
                    <td nowrap class="text-end">0</td>
                    <td nowrap class="text-end">0</td>
                    <td nowrap class="text-end">0</td>
                @endif
            </tr>
            @foreach ($stok->sortBy('tanggal_kedaluarsa') as $subItem)
                <tr class="bg-green-100">
                    <td nowrap class="text-end">{{ $subItem['tanggal_kedaluarsa'] }}</td>
                    <td nowrap class="text-end">{{ number_format($subItem['harga_beli']) }}</td>
                    <td nowrap class="text-end">{{ number_format($subItem['stok'], 2) }}</td>
                    <td nowrap class="text-end">
                        {{ number_format($subItem['total']) }}</td>
                </tr>
            @endforeach
            </tr>
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <th colspan="7" class="text-end">Total Nilai Persediaan</th>
            <th class="text-end">{{ number_format($total) }}</th>
    </tfoot>
</table>
