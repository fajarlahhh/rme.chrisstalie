@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-200px" alt="" />
        <br>
        <br>
        <h5>Laporan Barang Masuk</h5>
        <hr>
    </div>
    <br>
    <table>
        <tr>
            <th>Persediaan</th>
            <th class="w-10px">:</th>
            <td>{{ $persediaan }}</td>
        </tr>
        <tr>
            <th class="w-100px">Periode</th>
            <th class="w-10px">:</th>
            <td>{{ $tanggal1 }} s/d {{ $tanggal2 }}</td>
        </tr>
        <tr>
            <th>Jenis</th>
            <th class="w-10px">:</th>
            <td>{{ $jenis }}</td>
        </tr>
    </table>
@endif
@switch($jenis)
    @case('pertransaksi')
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="bg-gray-300 text-white">No.</th>
                    <th class="bg-gray-300 text-white">Tanggal</th>
                    <th class="bg-gray-300 text-white">Operator</th>
                    <th class="bg-gray-300 text-white">Metode Bayar</th>
                    <th class="bg-gray-300 text-white">Barang</th>
                    <th class="bg-gray-300 text-white">Satuan Utama</th>
                    <th class="bg-gray-300 text-white">No. Batch</th>
                    <th class="bg-gray-300 text-white">Tanggal Kedaluarsa</th>
                    <th class="bg-gray-300 text-white">Supplier</th>
                    <th class="bg-gray-300 text-white">Harga Beli</th>
                    <th class="bg-gray-300 text-white">Qty</th>
                    <th class="bg-gray-300 text-white">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $i => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td nowrap>{{ $row[0]['tanggal'] }}</td>
                        <td nowrap>{{ $row[0]['operator'] }}</td>
                        <td nowrap>{{ $row[0]['metode_bayar'] }}</td>
                        <td nowrap>{{ $row[0]['barang'] }}</td>
                        <td nowrap>{{ $row[0]['satuan'] }}</td>
                        <td nowrap>{{ $row[0]['no_batch'] }}</td>
                        <td nowrap>{{ $row[0]['tanggal_kedaluarsa'] }}</td>
                        <td nowrap>{{ $row[0]['supplier'] }}</td>
                        <td nowrap class="text-end">{{ number_format($row[0]['harga_beli']) }}</td>
                        <td nowrap class="text-end">{{ number_format($row[0]['qty']) }}</td>
                        <td nowrap class="text-end">{{ number_format($row[0]['total']) }}</td>
                    </tr>
                @endforeach
                <tr>
                    <th colspan="10" class="text-end">Total</th>
                    <th class="text-end">{{ number_format(collect($data)->sum(fn($q) => collect($q)->sum(fn($q) => $q['qty'] * $q['harga_beli']))) }}</th>
                </tr>
            </tbody>
        </table>
    @break

    @case('perbarang')
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="bg-gray-300 text-white">No.</th>
                    <th class="bg-gray-300 text-white">Barang</th>
                    <th class="bg-gray-300 text-white">Satuan</th>
                    <th class="bg-gray-300 text-white">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $i => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td nowrap>{{ $row[0]['nama'] }}</td>
                        <td nowrap>{{ $row[0]['satuan'] }}</td>
                        <td nowrap class="text-end">{{ number_format(collect($row)->sum('qty')) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @break

@endswitch
