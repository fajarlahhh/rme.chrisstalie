@push('styles')
    <style>
        @media print {

            body,
            html {
                height: auto !important;
                min-height: 0 !important;
                margin: 0 !important;
                padding: 0 !important;
            }

            .custom-print-container {
                width: 80mm !important;
                max-width: 100% !important;
                margin: 0 auto !important;
                padding: 0 !important;
                height: auto !important;
                box-sizing: border-box;
            }

            @page {
                size: auto !important;
                margin: 0 !important;
            }
        }
    </style>
@endpush
<div class="custom-print-container">
    <div class="text-center">
        <img src="/assets/img/login.png" class="w-200px">
    </div>
    <br>
    <br>
    <table class="table table-borderless fs-10px">
        <tr>
            <td class="text-nowrap w-50px p-0">No.</td>
            <td class="p-0">: {{ $data->id }}</td>
        </tr>
        <tr>
            <td class="text-nowrap w-50px p-0">Kasir</td>
            <td class="p-0">: {{ $data->pengguna->pegawai ? $data->pengguna->pegawai->nama : $data->pengguna->nama }}
            </td>
        </tr>
        <tr>
            <td class="text-nowrap p-0">Tanggal</td>
            <td class="p-0">: {{ $data->created_at }}</td>
        </tr>
    </table>
    <hr>
    <table class="table table-borderless fs-10px">
        <tr>
            <th class="p-0">Item<br><br></th>
            <th class="p-0 text-center w-10px">Qty<br><br></th>
            <th class="p-0 text-end">Harga<br><br></th>
        </tr>
        @foreach ($data->stokKeluar as $detail)
            <tr>
                <td class="p-0">
                    {{ $detail->barangSatuan->barang->nama }}<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<small>{{ $detail->barangSatuan->nama }} -
                        {{ number_format($detail->harga) }}</small>
                </td>
                <td class="p-0 ps-2 text-center w-10px" nowrap>
                    {{ $detail->qty }}
                </td>
                <td class="p-0 text-end w-50px" nowrap>
                    {{ number_format($detail->qty * $detail->harga) }}
                </td>
            </tr>
        @endforeach
    </table>
    <hr>
    <table class="table table-borderless fs-10px">
        <tr>
            <td class="p-0">Total Harga Barang</td>
            <td class="p-0 text-end">{{ number_format($data->total_harga_barang) }}</td>
        </tr>
        <tr>
            <td class="p-0">Diskon</td>
            <td class="p-0 text-end">{{ number_format($data->diskon) }}</td>
        </tr>
        <tr>
            <th class="p-0">Total</th>
            <th class="p-0 text-end">
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
</div>
