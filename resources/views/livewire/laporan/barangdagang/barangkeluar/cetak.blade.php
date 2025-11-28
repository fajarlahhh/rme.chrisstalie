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
            <th class="w-100px">Periode</th>
            <th class="w-10px">:</th>
            <td>{{ $bulan }}</td>
        </tr>
    </table>
@endif
@switch($jenis)
    @case('pertanggalkeluar')
        <table class="table table-bordered table-hover">
            <thead>
                <tr>
                    <th class="bg-gray-300 text-white">No.</th>
                    <th class="bg-gray-300 text-white">Tanggal</th>
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
                        <td nowrap>{{ $row[0]['tanggal'] }}</td>
                        <td nowrap>{{ $row[0]['barang'] }}</td>
                        <td nowrap>{{ $row[0]['satuan'] }}</td>
                        <td nowrap class="text-end">{{ number_format($row[0]['harga_jual']) }}</td>
                        <td nowrap class="text-end">{{ number_format(collect($row)->sum('qty')) }}</td>
                        <td nowrap class="text-end">
                            {{ number_format(collect($row)->sum(fn($q) => $q['qty'] * $q['harga_jual'])) }}</td>
                        @php
                            $total += collect($row)->sum(fn($q) => $q['qty'] * $q['harga_jual']);
                        @endphp
                    </tr>
                @endforeach
                <tr>
                    <th colspan="6" class="text-end">Total</th>
                    <th class="text-end">{{ number_format($total) }}
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
                    <th class="bg-gray-300 text-white">Tanggal Kedaluarsa</th>
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
                        <td nowrap>{{ $key }}</td>
                        <td nowrap>{{ $row[0]['barang'] }}</td>
                        <td nowrap>{{ $row[0]['satuan'] }}</td>
                        <td nowrap class="text-end">{{ number_format($row[0]['harga_jual']) }}</td>
                        <td nowrap class="text-end">{{ number_format(collect($row)->sum('qty')) }}</td>
                        <td nowrap class="text-end">
                            {{ number_format(collect($row)->sum(fn($q) => $q['qty'] * $q['harga_jual'])) }}</td>
                        @php
                            $total += collect($row)->sum(fn($q) => $q['qty'] * $q['harga_jual']);
                        @endphp
                    </tr>
                @endforeach
                <tr>
                    <th colspan="6" class="text-end">Total</th>
                    <th class="text-end">{{ number_format($total) }}
                    </th>
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
                        <td nowrap>{{ $key }}</td>
                        <td nowrap>{{ $row[0]['satuan'] }}</td>
                        <td nowrap class="text-end">{{ number_format($row[0]['harga_jual']) }}</td>
                        <td nowrap class="text-end">{{ number_format(collect($row)->sum('qty')) }}</td>
                        <td nowrap class="text-end">
                            {{ number_format(collect($row)->sum(fn($q) => $q['qty'] * $q['harga_jual'])) }}</td>
                        @php
                            $total += collect($row)->sum(fn($q) => $q['qty'] * $q['harga_jual']);
                        @endphp
                    </tr>
                @endforeach
                <tr>
                    <th colspan="5" class="text-end">Total</th>
                    <th class="text-end">{{ number_format($total) }}
                    </th>
                </tr>
            </tbody>
        </table>
    @break

    @default
@endswitch
