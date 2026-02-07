<table class="table table-hover">
    <thead>
        <tr>
            <th class="w-10px">No.</th>
            <th>Nama</th>
            <th>Satuan</th>
            <th>Kategori Persediaan</th>
            <th>Kategori Penjualan</th>
            <th>Kategori Modal</th>
            <th>KFA</th>
            <th>Perlu Resep</th>
            <th>Persediaan</th>
            @if ($cetak == false)
                <th></th>
            @endif
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $index => $item)
            <tr>
                <td>{{ $cetak == false ? ($data->currentPage() - 1) * $data->perPage() + $loop->iteration : $loop->iteration }}
                </td>
                <td>{{ $item->nama }}</td>
                <td>
                    @if ($cetak == false)
                        <table class="table table-bordered fs-11px">
                            <tbody>
                                @foreach ($item->barangSatuan as $satuan)
                                    <tr>
                                        <td class="p-1">{{ $satuan->nama }}</td>
                                        <td class="text-end w-100px p-1">
                                            {{ $cetak == false ? number_format($satuan->harga_jual, 0, ',', '.') : $satuan->harga_jual }}
                                        </td>
                                        <td class="p-1 text-nowrap w-150px">
                                            {!! $satuan->rasio_dari_terkecil == 1
                                                ? '<span class="badge bg-success">Terkecil</span>'
                                                : '<span class="badge bg-warning">' . $satuan->konversi_satuan . '</span>' !!}
                                            {!! $satuan->utama == 1 ? '<span class="badge bg-info">Utama</span>' : '' !!}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    @else
                        @foreach ($item->barangSatuan as $satuan)
                            {{ $satuan->nama }} - Rp. {{ $satuan->harga_jual }} ({!! $satuan->rasio_dari_terkecil == 1 ? ' Terkecil ' : ' ' . $satuan->konversi_satuan . ' ' !!}
                            {!! $satuan->utama == 1 ? ' Utama ' : '' !!})<br>
                        @endforeach
                    @endif
                </td>
                <td>{{ $item->kode_akun_id }} - {{ $item->kodeAkun?->nama }}</td>
                <td>{{ $item->kode_akun_penjualan_id }} - {{ $item->kodeAkunPenjualan?->nama }}</td>
                <td>{{ $item->kode_akun_modal_id }} - {{ $item->kodeAkunModal?->nama }}</td>
                <td>{{ $item->kfa }}</td>
                <td>{{ $item->perlu_resep ? 'Ya' : '' }}</td>
                <td>{{ $item->persediaan }}</td>
                @if ($cetak == false)
                    <td class="with-btn-group text-end" nowrap>
                        @role('administrator|supervisor')
                            <x-action :row="$item" custom="" :detail="false" :edit="true" :print="false"
                                :permanentdelete="false" :restore="false" :delete="true" />
                        @endrole
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
