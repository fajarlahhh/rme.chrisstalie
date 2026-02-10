@if ($cetak)
    <div class="w-100 text-center">
        <img src="/assets/img/login.png" class="w-200px" alt="" />
        <br>
        <br>
        <h5>Laporan Neraca</h5>
        <h6>Periode : {{ $bulan }}</h6>
        <hr>
    </div>
@endif

<div class="row">
    <div class="col-6">
        <table class="table table-borderless table-hover if">
            <tbody>
                @foreach ($dataAktiva as $index => $item)
                    <tr>
                        <td class="w-10px p-1">{{ $item['nomor'] }}</td>
                        <td class="p-1">{!! $item['uraian'] !!}</td>
                        <td class="text-end p-1">{{ $item['nilai'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
    <div class="col-6">
        <table class="table table-borderless table-hover if">
            <tbody>
                @foreach ($dataPasiva as $index => $item)
                    <tr>
                        <td class="w-10px p-1">{{ $item['nomor'] }}</td>
                        <td class="p-1">{!! $item['uraian'] !!}</td>
                        <td class="text-end p-1">{{ $item['nilai'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
