@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-100px" alt="" />
        <br>
        <br>
        <h5>Laporan Laba Rugi</h5>
        <h6>Periode : {{ $bulan }}</h6>
    </div>
@endif

<table class="table table-borderless table-hover fs-11px">
    <thead class="border border-gray-100 border-bottom border-top border-start border-end">
        <tr class="bg-gray-100">
            <th class="border-bottom border-top border-start border-end p-1">No</th>
            <th class="border-bottom border-top border-start border-end p-1">Uraian</th>
            <th class="border-bottom border-top border-start border-end p-1">Nilai</th>
        </tr>
    </thead>
    <tbody class="border border-gray-100 border-bottom border-top border-start border-end">
        @foreach ($data as $index => $item)
            <tr>
                <td class="w-10px p-1">{{ $item['nomor'] }}</td>
                <td class="p-1 border-start border-end">{!! $item['uraian'] !!}</td>
                <td class="text-end p-1">{{ $item['nilai'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
