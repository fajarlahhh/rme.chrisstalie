<table class="table table-hover">
    <thead>
        <tr>
            <th rowspan="2" class="w-10px">No.</th>
            <th rowspan="2">Nama</th>
            <th rowspan="2">Kategori</th>
            <th rowspan="2">ICD 9 CM</th>
            <th rowspan="2" class="text-end">Tarif</th>
            <th colspan="3">Biaya</th>
            <th rowspan="2" class="text-end">Keuntungan Klinik</th>
            <th rowspan="2">Catatan</th>
            @if ($cetak == false)
                <th rowspan="2"></th>
            @endif
        </tr>
        <tr>
            <th class="text-end">Biaya Alat Bahan</th>
            <th class="text-end">Biaya Jasa Dokter</th>
            <th class="text-end">Biaya Jasa Perawat</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $cetak == false ? ($data->currentPage() - 1) * $data->perPage() + $loop->iteration : $loop->iteration }}
                </td>
                <td>{{ $item->nama }}</td>
                <td>{{ $item->kode_akun_id }} - {{ $item->kodeAkun?->nama }}</td>
                <td>{{ $item->icd_9_cm }}</td>
                <td class="text-end">{{ $cetak == false ? number_format($item->tarif) : $item->tarif }}</td>
                <td class="text-end">
                    {{ $cetak == false ? number_format($item->biaya_alat_barang) : $item->biaya_alat_barang }}</td>
                <td class="text-end">
                    {{ $cetak == false ? number_format($item->biaya_jasa_dokter) : $item->biaya_jasa_dokter }}</td>
                <td class="text-end">
                    {{ $cetak == false ? number_format($item->biaya_jasa_perawat) : $item->biaya_jasa_perawat }}</td>
                <th class="text-end">
                    {{ $cetak == false
                        ? number_format($item->tarif - $item->biaya_jasa_dokter - $item->biaya_jasa_perawat - $item->biaya_alat_barang)
                        : $item->tarif - $item->biaya_jasa_dokter - $item->biaya_jasa_perawat - $item->biaya_alat_barang }}
                </th>
                <td nowrap>{!! nl2br(e($item->catatan)) !!}</td>
                @if ($cetak == false)
                    <td class="with-btn-group text-end" nowrap>
                        @role('administrator|supervisor')
                            <x-action :row="$item" custom="" :detail="false" :edit="true" :print="false"
                                :permanentDelete="false" :restore="false" :delete="true" />
                        @endrole
                    </td>
                @endif
            </tr>
        @endforeach
    </tbody>
</table>
