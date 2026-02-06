<table class="table table-hover">
    <thead>
        <tr>
            <th class="w-10px">No.</th>
            <th>Nama</th>
            <th>Satuan</th>
            <th>Kategori</th>
            <th>Tanggal Perolehan</th>
            <th class="text-end">Harga Perolehan</th>
            <th>Sumber Dana</th>
            <th>Masa Manfaat</th>
            <th>Nilai Penyusutan</th>
            <th>Lokasi</th>
            <th>Status</th>
            <th>No. Jurnal</th>
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
                <td>{{ $item->satuan }}</td>
                <td>{{ $item->kode_akun_id }} - {{ $item->kodeAkun->nama }}</td>
                <td>{{ $item->tanggal_perolehan }}</td>
                <td class="text-end">
                    {{ $cetak == false ? number_format($item->harga_perolehan) : $item->harga_perolehan }}</td>
                <td>
                    @if ($item->kode_akun_sumber_dana_id)
                        {{ $item->kode_akun_sumber_dana_id }} - {{ $item->kodeAkunSumberDana->nama }}
                    @endif
                </td>
                <td>
                    @if ($item->metode_penyusutan == 'Satuan Hasil Produksi')
                        {{ $item->masa_manfaat }} <small>x</small>
                    @else
                        {{ $item->masa_manfaat }} <small>bulan</small>
                    @endif
                </td>
                <td class="text-end">
                    {{ $cetak == false ? number_format($item->nilai_penyusutan, 2) : $item->nilai_penyusutan }}</td>
                <td>{{ $item->lokasi }}</td>
                <td>
                    @switch($item->status)
                        @case('Aktif')
                            <span class="badge bg-success">Aktif</span>
                        @break

                        @case('Tidak Aktif')
                            <span class="badge bg-danger">Tidak Aktif</span>
                        @break

                        @default
                    @endswitch
                </td>
                <td><a href="/jurnalkeuangan?bulan={{ substr($item->keuanganJurnal?->tanggal, 0, 7) }}&cari={{ $item->keuanganJurnal?->id }}"
                        target="_blank">{{ $item->keuanganJurnal?->nomor }}</a></td>
                @if ($cetak == false)
                    <td class="with-btn-group text-end" nowrap>
                        @role('administrator|supervisor')
                            @if ($item->keuanganJurnal->waktu_tutup_buku)
                                <x-action :row="$item" custom="" :detail="false" :edit="true"
                                    :print="false" :permanentDelete="false" :restore="false" :delete="false" />
                            @else
                                <x-action :row="$item" custom="" :detail="false" :edit="true"
                                    :print="false" :permanentDelete="false" :restore="false" :delete="true" />
                            @endif
                        @endrole
                    </td>
                @endif
            </tr>
        @endforeach
        @if ($cetak == true)
            <tr>
                <th colspan="10" class="text-end">TOTAL</th>
                <th class="text-end">{{ $data->sum('harga_perolehan') }}</th>
            </tr>
        @else
            @if ($data->currentPage() == $data->lastPage())
                <tr>
                    <th colspan="11" class="text-end">TOTAL</th>
                    <th class="text-end">{{ number_format($dataRaw->sum('harga_perolehan')) }}</th>
                </tr>
            @endif
        @endif
    </tbody>
</table>
