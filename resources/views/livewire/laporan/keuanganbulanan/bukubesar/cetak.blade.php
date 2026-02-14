<table class="table table-bordered mb-5">
    @php
        $kodeAkun = collect($dataKodeAkun)->where('id', $kodeAkunId)->first();
    @endphp
    <thead>
        <tr>
            <th colspan="8" class="bg-gray-100">{{ $kodeAkun ? $kodeAkun['id'] . ' - ' . $kodeAkun['nama'] : '' }}</th>
        </tr>
        <tr>
            <th class="w-20px bg-gray-100"></th>
            <th class="w-100px bg-gray-100">Tanggal</th>
            <th class="bg-gray-100">Uraian</th>
            <th class="bg-gray-100">Jurnal</th>
            <th class="w-200px text-nowrap bg-gray-100">ID</th>
            <th class="w-150px text-nowrap bg-gray-100">Debet</th>
            <th class="w-150px text-nowrap bg-gray-100">Kredit</th>
            <th class="w-150px text-nowrap bg-gray-100">Saldo</th>
        </tr>
    </thead>
    <tbody>
        <tr>
            <td>1</td>
            <td>{{ $bulan . '-01' }}</td>
            <td>Saldo Awal</td>
            <td></td>
            <td></td>
            <td class="text-end"></td>
            <td class="text-end"></td>
            <td class="text-end">
                @if ($cetak)
                    {{ $saldo }}
                @else
                    {{ number_format($saldo, 2) }}
                @endif
            </td>
        </tr>
        @php
            $sumSaldo = $saldo;
        @endphp
        @if ($kodeAkunId)
            @foreach ($data as $index => $sub)
                @php
                    $sumSaldo +=
                        $kodeAkun['kategori'] == 'Aktiva' || $kodeAkun['kategori'] == 'Beban'  ? $sub['debet'] - $sub['kredit'] : $sub['kredit'] - $sub['debet'];
                @endphp
                <tr>
                    <td>{{ $index + 2 }}</td>
                    <td>{{ $sub['periode'] }}</td>
                    <td>{{ $sub['uraian'] }}</td>
                    <td>{{ $sub['id'] }}</td>
                    <td class="text-nowrap">{{ $sub['id'] }}</td>
                    <td class="text-end">
                        @if ($cetak)
                            {{ $sub['debet'] }}
                        @else
                            {{ number_format($sub['debet'], 2) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($cetak)
                            {{ $sub['kredit'] }}
                        @else
                            {{ number_format($sub['kredit'], 2) }}
                        @endif
                    </td>
                    <td class="text-end">
                        @if ($cetak)
                            {{ $sumSaldo }}
                        @else
                            {{ number_format($sumSaldo, 2) }}
                        @endif
                    </td>
                </tr>
            @endforeach
        @endif
    </tbody>
</table>
