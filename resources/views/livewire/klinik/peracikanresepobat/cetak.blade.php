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
            <td class="text-nowrap w-50px p-0" colspan="2">Resep Obat</td>
        </tr>
        <tr>
            <td class="text-nowrap w-50px p-0">No.</td>
            <td class="p-0">: {{ $data->id }}</td>
        </tr>
        <tr>
            <td class="text-nowrap w-50px p-0">Pasien</td>
            <td class="p-0">: {{ $data->pasien->nama }}</td>
        </tr>
    </table>
    <hr>
    @foreach ($data->resepObat->groupBy('resep') as $resep)
        <table class="table table-borderless fs-10px">
            <tr>
                <td class="p-0" colspan="3">
                    {{ $resep->first()->nama }}<br>
                </td>
            </tr>
            @foreach ($resep as $item)
                <tr>
                    <td class="p-0">
                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $item->barang->nama }}
                    </td>
                    <td class="p-0">
                        {{ $item->qty }} {{ $item->barangSatuan->nama }}
                    </td>
                    <td class="p-0 text-end">
                        {{ number_format($item->harga * $item->qty) }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td class="p-0" colspan="2">
                    Total
                </td>
                <td class="p-0 text-end" nowrap>
                    {{ number_format($resep->sum(fn($q) => $q->harga * $q->qty)) }}
                </td>
            </tr>
            <tr>
                <td colspan="2">{{ $item->catatan }}</td>
            </tr>
        </table>
        <hr>
    @endforeach
    <table class="table table-borderless fs-10px">
        <tr>
            <th class="p-0">Total</th>
            <th class="p-0 text-end" nowrap>
                {{ number_format($data->resepObat->sum(fn($q) => $q->harga * $q->qty)) }}
            </th>
        </tr>
    </table>
</div>
