<div class="text-center">
    <img src="/assets/img/login.png" class="w-200px">
</div>
<br>
<br>
<table class="table table-borderless fs-10px">
    <tr>
        <td class="text-nowrap w-50px p-0">No.</td>
        <td class="p-0">: {{ $data->pembayaran->id }}</td>
    </tr>
    <tr>
        <td class="text-nowrap p-0">Kasir</td>
        <td class="p-0">:
            {{ $data->pembayaran->pengguna->pegawai
                ? $data->pembayaran->pengguna->pegawai?->nama
                : $data->pembayaran->pengguna->nama }}
        </td>
    </tr>
    <tr>
        <td class="text-nowrap p-0">Tanggal</td>
        <td class="p-0">: {{ $data->pembayaran->created_at }}</td>
    </tr>
</table>
<hr>
<table class="table table-borderless fs-10px">
    <tr>
        <th class="p-0">Item<br><br></th>
        <th class="p-0 text-center w-10px" nowrap>Qty<br><br></th>
        <th class="p-0 text-end">Total<br><br></th>
    </tr>
    @foreach ($data->tindakan as $tindakan)
        <tr>
            <td class="p-0">
                {{ $tindakan->tarifTindakan->nama }}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($tindakan->biaya) }} @if ($tindakan->diskon > 0)
                    - {{ number_format($tindakan->diskon) }}
                @endif
            </td>
            <td class="p-0 ps-2 text-center w-10px" nowrap>
                {{ $tindakan->qty }}<br>

            </td>
            <td class="p-0 text-end w-100px" nowrap>
                {{ number_format(($tindakan->biaya - $tindakan->diskon) * $tindakan->qty) }}
            </td>
        </tr>
    @endforeach
    @foreach ($data->resepObat->groupBy('resep') as $resep)
        <tr>
            <td class="p-0">
                {{ $resep->first()->nama }}<br>
                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($resep->sum(fn($q) => $q->harga * $q->qty)) }}
            </td>
            <td class="p-0 ps-2 text-center w-10px" nowrap>
                1
            </td>
            <td class="p-0 text-end w-100px" nowrap>
                {{ number_format($resep->sum(fn($q) => $q->harga * $q->qty)) }}
            </td>
        </tr>
    @endforeach
</table>
<hr>
<table class="table table-borderless fs-10px">
    <tr>
        <td class="p-0">Total Tindakan</td>
        <td class="p-0 text-end" nowrap>
            {{ number_format($data->pembayaran->total_tindakan + $data->pembayaran->diskon) }}
        </td>
    </tr>
    <tr>
        <td class="p-0">Total Resep</td>
        <td class="p-0 text-end">{{ number_format($data->pembayaran->total_resep) }}</td>
    </tr>
    <tr>
        <td class="p-0">Diskon</td>
        <td class="p-0 text-end">{{ number_format($data->pembayaran->diskon) }}</td>
    </tr>
    <tr>
        <th class="p-0">Total</th>
        <th class="p-0 text-end" nowrap>
            {{ number_format($data->pembayaran->total_tagihan) }}
        </th>
    </tr>
    <tr>
        <td class="p-0">Metode Bayar</td>
        <td class="p-0 text-end">{{ $data->pembayaran->metode_bayar }}</td>
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
