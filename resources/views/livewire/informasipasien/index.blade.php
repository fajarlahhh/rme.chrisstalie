<div>
    @section('title', 'Informasi Pasien')

    @section('breadcrumb')
        <li class="breadcrumb-item active">Informasi Pasien</li>
    @endsection

    <h1 class="page-header">Informasi Pasien</h1>

    @if ($dataPasien)
    @endif

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <div class="w-100">
                <div wire:ignore>
                    <select class="form-control" x-init="$($el).select2({
                        width: '100%',
                        dropdownAutoWidth: true,
                        templateResult: format,
                        placeholder: 'Ketik Nama/No. KTP/No. RM',
                        minimumInputLength: 1,
                        dataType: 'json',
                        ajax: {
                            url: '/cari/pasien',
                            data: function(params) {
                                var query = {
                                    cari: params.term
                                }
                                return query;
                            },
                            processResults: function(data, params) {
                                return {
                                    results: data,
                                };
                            },
                            cache: true
                        }
                    });
                    
                    $($el).on('change', function(element) {
                        $wire.set('pasienId', $($el).val());
                    });
                    
                    function format(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        var $data = $('<table><tr><th>No. RM</th><th>:</th><th>' + data.rm + '</th></tr>' +
                            '<tr><th>No. KTP</th><th>:</th><th>' + data.nik + '</th></tr>' +
                            '<tr><th>Nama</th><th>:</th><th>' + data.nama + '</th></tr>' +
                            '<tr><th>Alamat</th><th>:</th><th>' + data.alamat + '</th></tr></table>');
                        return $data;
                    }">
                    </select>
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            @if ($dataPasien)
                <div class="note alert-primary mb-2">
                    <div class="note-content">
                        <h5>Data Pasien</h5>
                        <hr>
                        <table class="w-100">
                            <tr>
                                <td class="w-150px">No. RM</td>
                                <td class="w-10px">:</td>
                                <td>{{ $dataPasien->id }}</td>
                            </tr>
                            <tr>
                                <td>No. KTP</td>
                                <td class="w-10px">:</td>
                                <td>{{ $dataPasien->nik }}</td>
                            </tr>
                            <tr>
                                <td>Nama</td>
                                <td class="w-10px">:</td>
                                <td>{{ $dataPasien->nama }}</td>
                            </tr>
                            <tr>
                                <td>Tgl. Lahir</td>
                                <td class="w-10px">:</td>
                                <td>{{ $dataPasien->tanggal_lahir->format('d M Y') }} ({{ $dataPasien->umur }}
                                    Tahun)</td>
                            </tr>
                            <tr>
                                <td>Jenis Kelamin</td>
                                <td class="w-10px">:</td>
                                <td>{{ $dataPasien->jenis_kelamin }}</td>
                            </tr>
                            <tr>
                                <td>Alamat</td>
                                <td class="w-10px">:</td>
                                <td>{{ $dataPasien->alamat }}</td>
                            </tr>
                            <tr>
                                <td>No. Telp.</td>
                                <td class="w-10px">:</td>
                                <td>{{ $dataPasien->no_hp }}</td>
                            </tr>
                            <tr>
                                <td colspan="3">
                                    <hr>
                                </td>
                            </tr>
                            <tr>
                                <td>Tgl. Daftar</td>
                                <td class="w-10px">:</td>
                                <td>{{ $dataPasien->tanggal_daftar->format('d M Y') }}</td>
                            </tr>
                            <tr>
                                <td>Catatan</td>
                                <td class="w-10px">:</td>
                                <td>{{ $dataPasien->deskripsi }}</td>
                            </tr>
                        </table>
                    </div>
                </div>
                <table class="table table-bordered table-hover">
                    <tr>
                        <th class="bg-secondary-subtle" nowrap>No.</th>
                        <th class="bg-secondary-subtle" nowrap>Registrasi</th>
                        <th class="bg-secondary-subtle" nowrap>Hasil Pemeriksaan Awal</th>
                        <th class="bg-secondary-subtle" nowrap>Tes Up And Go</th>
                        <th class="bg-secondary-subtle" nowrap>Diagnosis</th>
                        <th class="bg-secondary-subtle" nowrap>Tindakan</th>
                        <th class="bg-secondary-subtle" nowrap>Resep Obat</th>
                        <th class="bg-secondary-subtle" nowrap>Pembayaran</th>
                    </tr>
                    @foreach ($dataPasien->rekamMedis as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td nowrap>
                                <strong>No. Registrasi : </strong>{{ $row->id }}<br>
                                <strong>Nakes : </strong>{{ $row->nakes->nama }}<br>
                                <strong>Keluhan Awal : </strong>{{ $row->keluhan_awal }}
                                <hr>
                                <small>
                                    {{ $row->pengguna->nama }}<br>
                                    {{ $row->created_at->format('d F Y, H:i') }}
                                </small>
                            </td>
                            <td nowrap>
                                @if ($row->pemeriksaanAwal)
                                    <strong>ANAMNESIS </strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Keluhan Utama :
                                    </strong>{{ $row->pemeriksaanAwal->keluhan_utama }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Riwayat Penyakit Sekarang :
                                    </strong>{{ $row->pemeriksaanAwal->riwayat_sekarang }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Riwayat Penyakit Dahulu & Riwayat Penyakit
                                        Keluarga : </strong>{{ $row->pemeriksaanAwal->riwayat_dahulu }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Riwayat Alergi :
                                    </strong>{{ $row->pemeriksaanAwal->riwayat_alergi }}
                                    <br><br>
                                    <strong>PEMERIKSAAN FISIK </strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tanda-Tanda Vital</strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tekanan
                                        Darah :
                                    </strong>{{ $row->pemeriksaanAwal->tekanan_darah }} mmHg<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nadi :
                                    </strong>{{ $row->pemeriksaanAwal->nadi }} x/menit<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pernapasan :
                                    </strong>{{ $row->pemeriksaanAwal->pernapasan }} x/menit<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Suhu :
                                    </strong>{{ $row->pemeriksaanAwal->suhu }} °C<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SpO2 :
                                    </strong>{{ $row->pemeriksaanAwal->saturasi_o2 }} %<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Berat Badan
                                        :
                                    </strong>{{ $row->pemeriksaanAwal->berat_badan }} kg<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tinggi Badan
                                        :
                                    </strong>{{ $row->pemeriksaanAwal->tinggi_badan }} cm
                                    <br><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Keadaan Umum & Kesadaran</strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tingkat
                                        Kesadaran : </strong>{{ $row->pemeriksaanAwal->kesadaran }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kesan Sakit
                                        :
                                    </strong>{{ $row->pemeriksaanAwal->kesan_sakit }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Status Gizi
                                        :
                                    </strong>{{ $row->pemeriksaanAwal->status_gizi }}<br><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pemeriksaan Head to Toe</strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kepala,
                                        Mata,
                                        THT, Leher :
                                    </strong>{{ $row->pemeriksaanAwal->kepala_normal == 1 ? 'Normal' : $row->pemeriksaanAwal->kepala_temuan }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jantung :
                                    </strong>{{ $row->pemeriksaanAwal->jantung_normal == 1 ? 'Normal' : $row->pemeriksaanAwal->jantung_temuan }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paru :
                                    </strong>{{ $row->pemeriksaanAwal->paru_normal == 1 ? 'Normal' : $row->pemeriksaanAwal->paru_temuan }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Abdomen :
                                    </strong>{{ $row->pemeriksaanAwal->abdomen_normal == 1 ? 'Normal' : $row->pemeriksaanAwal->abdomen_temuan }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ekstremitas
                                        :
                                    </strong>{{ $row->pemeriksaanAwal->ekstremitas_normal == 1 ? 'Normal' : $row->pemeriksaanAwal->ekstremitas_temuan }}
                                    <br><br>
                                    <strong>DIAGNOSIS & PERENCANAAN </strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Diagnosis Kerja :
                                    </strong>{{ $row->pemeriksaanAwal->diagnosis_kerja }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rencana Awal :
                                    </strong>{{ $row->pemeriksaanAwal->rencana_awal }}
                                    <hr>
                                    <small>
                                        {{ $row->pemeriksaanAwal->pengguna->nama }}<br>
                                        {{ $row->pemeriksaanAwal->created_at->format('d F Y, H:i') }}
                                    </small>
                                @endif
                            </td>
                            <td nowrap>
                                @if ($row->tug)
                                    <strong>HASIL TES</strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Waktu Tes :
                                    </strong>{{ $row->tug->waktu_tes_detik }} detik<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Observasi Kualitatif Gerakan :
                                    </strong><br>
                                    @foreach (is_string($row->tug->observasi_kualitatif) ? json_decode($row->tug->observasi_kualitatif, true) ?? [] : (is_array($row->tug->observasi_kualitatif) ? $row->tug->observasi_kualitatif : []) as $item)
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $item }}<br>
                                    @endforeach
                                    <br>
                                    <strong>PENILAIAN & REKOMENDASI</strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Penilaian Risiko Jatuh :
                                    </strong>{{ $row->tug->risiko_jatuh }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Catatan Tambahan / Rekomendasi :
                                    </strong>{{ $row->tug->catatan }}
                                    <hr>
                                    <small>
                                        {{ $row->tug->pengguna->nama }}<br>
                                        {{ $row->tug->created_at->format('d F Y, H:i') }}
                                    </small>
                                @endif
                            </td>
                            <td nowrap>
                                @if ($row->diagnosis)
                                    @foreach ($row->diagnosis->icd10_uraian as $item)
                                        {{ $item->id }} - {{ $item->uraian }}<br>
                                    @endforeach
                                    <br>
                                    <strong>Diagnosis Banding (Differential Diagnosis) :
                                    </strong>{{ $row->diagnosis->diagnosis_banding }}
                                    <br><br>
                                    <strong>DOKUMENTASI</strong><br>
                                    @foreach ($row->diagnosis->file as $item)
                                        <div class="border p-2 mb-2">
                                            <img src="{{ $item->link }}" alt="{{ $item->judul }}"
                                                class="img-fluid w-100"><br>
                                            <small>
                                                Judul :{{ $item->judul }}
                                                <br>
                                                {{ $item->keterangan }}
                                            </small>
                                        </div><br>
                                    @endforeach
                                    <hr>
                                    <small>
                                        {{ $row->diagnosis->pengguna->nama }}<br>
                                        {{ $row->diagnosis->created_at->format('d F Y, H:i') }}
                                    </small>
                                @endif
                            </td>
                            <td nowrap>
                                @if ($row->tindakan->count() > 0)
                                    @foreach ($row->tindakan as $item)
                                        {{ $loop->iteration }}. {{ $item->tarifTindakan->nama }} <br>
                                        <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Dokter :
                                            {{ $item->dokter?->nama }}</small><br>
                                        <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Perawat :
                                            {{ $item->perawat?->nama }}</small><br>
                                        <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- {{ $item->qty }} x
                                            {{ number_format($item->biaya) }}
                                            {{ $item->diskon > 0 ? ' - ' . number_format($item->diskon) : '' }} =
                                            <strong>{{ number_format($item->biaya * $item->qty - $item->diskon) }}</strong>
                                        </small><br>
                                        <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Catatan :
                                            &nbsp;&nbsp;&nbsp;{{ $item->catatan }}</small><br>
                                        <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- @if ($item->membutuhkan_sitemarking)
                                                <a href="/klinik/sitemarking/form/{{ $row->id }}"
                                                    target="_blank">Site
                                                    Marking</a>
                                            @endif
                                        </small><br>
                                    @endforeach
                                    <hr>
                                    <small>
                                        {{ $row->tindakan->first()->pengguna->nama }}<br>
                                        {{ $row->tindakan->first()->created_at->format('d F Y, H:i') }}
                                    </small>
                                @endif
                            </td>
                            <td nowrap>
                                @if ($row->resepObat->count() > 0)
                                    @foreach (collect($row->resepObat)->groupBy('resep')->map(function ($group) {
            $first = $group->first();
            return [
                'catatan' => $first->catatan,
                'nama' => $first->nama,
                'barang' => $group->map(function ($r) {
                        return [
                            'id' => $r->barang_satuan_id,
                            'satuan' => $r->barangSatuan->nama,
                            'nama' => $r->barangSatuan->barang->nama,
                            'harga' => $r->harga,
                            'qty' => $r->qty,
                            'subtotal' => $r->harga * $r->qty,
                        ];
                    })->toArray(),
            ];
        })->values()->toArray() as $item)
                                        Resep {{ $loop->iteration }} : {{ $item['nama'] }} <br>
                                        @foreach ($item['barang'] as $barang)
                                            <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- {{ $barang['nama'] }} /
                                                {{ $barang['satuan'] }}<br>
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $barang['qty'] }}
                                                x
                                                {{ number_format($barang['harga']) }} =
                                                <strong>{{ number_format($barang['subtotal']) }}</strong></small><br>
                                        @endforeach
                                        <small>Catatan :<br>&nbsp;&nbsp;&nbsp;{{ $item['catatan'] }}</small><br>
                                    @endforeach
                                    <hr>
                                    <small>
                                        {{ $row->resepObat->first()->pengguna->nama }}<br>
                                        {{ $row->resepObat->first()->created_at->format('d F Y, H:i') }}
                                    </small>
                                @endif
                            </td>
                            <td nowrap>
                                @if ($row->pembayaran)
                                    <strong>Metode Pembayaran :</strong> {{ $row->pembayaran->metode_bayar }}<br>
                                    <strong>No. Nota :</strong> {{ $row->pembayaran->id }}<br>
                                    <strong>Total Tindakan :</strong>
                                    {{ number_format($row->pembayaran->total_tindakan) }}<br>
                                    <strong>Total Resep :</strong>
                                    {{ number_format($row->pembayaran->total_resep) }}<br>
                                    <strong>Diskon :</strong> {{ number_format($row->pembayaran->diskon) }}<br>
                                    <strong>Total :</strong> {{ number_format($row->pembayaran->total_tagihan) }}
                                    <hr>
                                    <small>
                                        {{ $row->pembayaran->pengguna->nama }}<br>
                                        {{ $row->pembayaran->created_at->format('d F Y, H:i') }}
                                    </small>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </table>
            @endif
        </div>
    </div>
</div>
