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
    @case('perhargajual')
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="bg-gray-300 text-white">No.</th>
                    <th class="bg-gray-300 text-white">Barang</th>
                    <th class="bg-gray-300 text-white">Satuan</th>
                    <th class="bg-gray-300 text-white">Harga Jual</th>
                    <th class="bg-gray-300 text-white">Qty</th>
                    <th class="bg-gray-300 text-white">Total</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total = 0;
                @endphp
                @foreach ($data as $key => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td nowrap>{{ $row[0]['nama'] }}</td>
                        <td nowrap>{{ $row[0]['satuan'] }}</td>
                        <td nowrap class="text-end">{{ number_format($row[0]['harga_jual'], 2) }}</td>
                        <td nowrap class="text-end">{{ number_format(collect($row)->sum('qty'),2) }}</td>
                        <td nowrap class="text-end">
                            {{ number_format(collect($row)->sum(fn($q) => $q['qty'] * $q['harga_jual']), 2) }}</td>
                        @php
                            $total += collect($row)->sum(fn($q) => $q['qty'] * $q['harga_jual']);
                        @endphp
                    </tr>
                @endforeach
                <tr>
                    <th colspan="5" class="text-end">Total</th>
                    <th class="text-end">{{ number_format($total, 2) }}
                    </th>
                </tr>
            </tbody>
        </table>
    @break

    @case('pertanggalkedaluarsa')
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="bg-gray-300 text-white">No.</th>
                    <th class="bg-gray-300 text-white">Barang</th>
                    <th class="bg-gray-300 text-white">Tanggal Kedaluarsa</th>
                    <th class="bg-gray-300 text-white">Satuan</th>
                    <th class="bg-gray-300 text-white">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td nowrap>{{ $row[0]['nama'] }}</td>
                        <td nowrap>{{ $row[0]['tanggal_kedaluarsa'] }}</td>
                        <td nowrap>{{ $row[0]['satuan'] }}</td>
                        <td nowrap class="text-end">{{ number_format(collect($row)->sum('qty'),2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @break

    @case('perbarang')
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="bg-gray-300 text-white">No.</th>
                    <th class="bg-gray-300 text-white">Barang</th>
                    <th class="bg-gray-300 text-white">Satuan Utama</th>
                    <th class="bg-gray-300 text-white">Qty</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($data as $key => $row)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td nowrap>{{ $row[0]['nama'] }}</td>
                        <td nowrap>{{ $row[0]['satuan'] }}</td>
                        <td nowrap class="text-end">{{ number_format(collect($row)->sum('qty'),2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @break

    @default
@endswitch
