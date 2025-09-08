<div class="text-center">
    <img src="/assets/img/login.png" class="w-200px">
</div>
<br>
<br>
<table class="table table-borderless fs-11px">
    <tr>
        <td class="text-nowrap w-50px p-0">Kasir</td>
        <td class="p-0">: {{ $data->pengguna->pegawai ? $data->pengguna->pegawai->nama : $data->pengguna->nama }}</td>
        <td class="p-0 text-end">No. {{ $data->id }}</td>
    </tr>
    <tr>
        <td class="text-nowrap p-0">Tanggal</td>
        <td class="p-0" colspan="2">: {{ $data->created_at }}</td>
    </tr>
</table>
<hr>
<table class="table table-borderless fs-11px">
    @foreach ($data->penjualanDetail as $detail)
        <tr>
            <th class="p-0">Barang<br><br></th>
            <th class="p-0 text-end">Qty<br><br></th>
            <th class="p-0 text-end">Harga<br><br></th>
        </tr>
        <tr>
            <td class="p-0">
                {{ $detail->barang->nama }}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;<small>{{ $detail->barangSatuan->nama }}</small>
            </td>
            <td class="p-0 ps-2 text-end text-nowrap w-100px">
                {{ number_format($detail->harga) }}<br>
                x {{ $detail->qty }}
            </td>
            <td class="p-0 text-end text-nowrap w-100px">Rp.
                {{ number_format($detail->qty * $detail->harga) }}
            </td>
        </tr>
    @endforeach
</table>
<hr>
<table class="table table-borderless fs-11px">
    <tr>
        <td class="p-0">Total Harga Barang</td>
        <td class="p-0 text-end">Rp. {{ number_format($data->total_harga_barang) }}</td>
    </tr>
    <tr>
        <td class="p-0">Diskon</td>
        <td class="p-0 text-end">Rp. {{ number_format($data->diskon) }}</td>
    </tr>
    <tr>
        <th class="p-0">Total</th>
        <th class="p-0 text-end">Rp.
            {{ number_format($data->total_harga_barang - $data->diskon) }}
        </th>
    </tr>
    <tr>
        <td class="p-0">Metode Bayar</td>
        <td class="p-0 text-end">{{ $data->metode_bayar }}</td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
    </tr>
    <tr>
        <td colspan="2" class="text-center">
            <h3>TERIMA KASIH</h3>
        </td>
    </tr>
</table>
<br>
<br>
