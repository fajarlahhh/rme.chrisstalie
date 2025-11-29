<style>
    /* Reset umum */
    * {
        box-sizing: border-box;
    }

    /* Wrapper agar tampilan di layar komputer tidak berantakan */
    .struk-wrapper {
        width: 100%;
        max-width: 80mm;
        margin: 0 auto;
        padding: 5px;
        background: white;
    }

    /* --- SETTING KHUSUS PRINT --- */
    @media print {

        /* Memaksa ukuran kertas menjadi roll (jika browser mendukung) */
        @page {
            margin: 0;
            size: 80mm auto;
            /* Lebar 80mm, Tinggi Otomatis */
        }

        /* KUNCI: Memaksa HTML dan Body hanya setinggi kontennya */
        html,
        body {
            width: 80mm;
            height: max-content;
            /* Penting: Tinggi mengikuti isi, bukan tinggi kertas */
            margin: 0 !important;
            padding: 0 !important;
            overflow: hidden;
        }

        .struk-wrapper {
            width: 100%;
            margin: 0;
            padding-bottom: 5mm;
            /* Jarak sedikit di bawah agar tidak terpotong pas */
            border: none;
        }

        /* Sembunyikan header/footer browser default */
        header,
        footer,
        .no-print {
            display: none !important;
        }
    }

    /* Helper classes */
    .text-center {
        text-align: center;
    }

    .text-end {
        text-align: right;
    }

    .text-nowrap {
        white-space: nowrap;
    }

    .p-0 {
        padding: 0;
    }

    .fw-bold {
        font-weight: bold;
    }

    .fs-10px {
        font-size: 10pt;
    }

    table {
        width: 100%;
        border-collapse: collapse;
    }

    /* Garis putus-putus */
    hr {
        border: none;
        border-top: 1px dashed #000;
        margin: 8px 0;
    }
</style>
<div class="struk-wrapper">
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
            <td class="text-nowrap p-0">Kasir</td>
            <td class="p-0">:
                {{ $data->pengguna->panggilan }}
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
            <th class="p-0 text-center w-10px" nowrap>Qty<br><br></th>
            <th class="p-0 text-end">Total<br><br></th>
        </tr>
        @foreach ($data->registrasi->tindakan as $tindakan)
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
                <td class="p-0 text-end w-50px" nowrap>
                    {{ number_format(($tindakan->biaya - $tindakan->diskon) * $tindakan->qty) }}
                </td>
            </tr>
        @endforeach
        @foreach ($data->registrasi->resepObat->groupBy('resep') as $resep)
            <tr>
                <td class="p-0">
                    {{ $resep->first()->nama }}<br>
                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ number_format($resep->sum(fn($q) => $q->harga * $q->qty)) }}
                </td>
                <td class="p-0 ps-2 text-center w-10px" nowrap>
                    1
                </td>
                <td class="p-0 text-end w-50px" nowrap>
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
                {{ number_format($data->total_tindakan + $data->diskon) }}
            </td>
        </tr>
        <tr>
            <td class="p-0">Total Resep</td>
            <td class="p-0 text-end">{{ number_format($data->total_resep) }}</td>
        </tr>
        <tr>
            <td class="p-0">Diskon</td>
            <td class="p-0 text-end">{{ number_format($data->diskon) }}</td>
        </tr>
        <tr>
            <th class="p-0">Total</th>
            <th class="p-0 text-end" nowrap>
                {{ number_format($data->total_tagihan) }}
            </th>
        </tr>
        <tr>
            <td class="p-0">Metode Bayar</td>
            <td class="p-0 text-end">{{ $data->metode_bayar }}</td>
        </tr>
    </table>
    <br>

    <div class="text-center">
        <h3 style="font-size: 12pt; margin: 0;">TERIMA KASIH</h3>
    </div>

    <br>
    <br>
</div>
