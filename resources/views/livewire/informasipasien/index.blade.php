<div>
    @section('title', 'Informasi Pasien')

    @section('breadcrumb')
        <li class="breadcrumb-item active">Informasi Pasien</li>
    @endsection

    <h1 class="page-header">Informasi Pasien</h1>

    @if ($pasien)
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
                        <td>{{ $dataPasien->tanggal_lahir->format('d F Y') }} ({{ $dataPasien->umur }} Tahun)</td>
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
                        <td>{{ $dataPasien->tanggal_daftar->format('d F Y') }}</td>
                    </tr>
                    <tr>
                        <td>Catatan</td>
                        <td class="w-10px">:</td>
                        <td>{{ $dataPasien->deskripsi }}</td>
                    </tr>
                </table>
            </div>
        </div>
    @endif

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <form wire:submit.prevent="submit">
            <div class="panel-body table-responsive">
                @if ($dataPasien)
                    <table class="table table-bordered table-hover">
                        <tr>
                            <th class="bg-secondary-subtle">No.</th>
                            <th class="bg-secondary-subtle">Registrasi</th>
                            <th class="bg-secondary-subtle">Hasil Pemeriksaan Awal</th>
                            <th class="bg-secondary-subtle">Tes Up And Go</th>
                            <th class="bg-secondary-subtle">Diagnosis</th>
                            <th class="bg-secondary-subtle">Tindakan</th>
                            <th class="bg-secondary-subtle">Resep Obat</th>
                            <th class="bg-secondary-subtle">Pembayaran</th>
                        </tr>
                        @foreach ($dataPasien->rekamMedis as $index => $row)
                            <tr>
                                <td>{{ $index + 1 }}</td>
                                <td nowrap>
                                    <strong>Nakes : </strong>{{ $row->nakes->nama }}<br>
                                    <strong>Keluhan Awal : </strong>{{ $row->keluhan_awal }}
                                    <hr>
                                    <small>
                                        {{ $row->pengguna->pegawai->nama ?? $row->pengguna->nama }}<br>
                                        {{ $row->updated_at->format('d F Y, H:i') }}
                                    </small>
                                </td>
                                <td nowrap>
                                    <strong>ANAMNESIS </strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Keluhan Utama :
                                    </strong>{{ $row->pemeriksaanAwal?->keluhan_utama }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Riwayat Penyakit Sekarang :
                                    </strong>{{ $row->pemeriksaanAwal?->riwayat_sekarang }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Riwayat Penyakit Dahulu & Riwayat Penyakit
                                        Keluarga : </strong>{{ $row->pemeriksaanAwal?->riwayat_dahulu }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Riwayat Alergi :
                                    </strong>{{ $row->pemeriksaanAwal?->riwayat_alergi }}
                                    <br><br>
                                    <strong>PEMERIKSAAN FISIK </strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tanda-Tanda Vital</strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tekanan Darah :
                                    </strong>{{ $row->pemeriksaanAwal?->tekanan_darah }} mmHg<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nadi :
                                    </strong>{{ $row->pemeriksaanAwal?->nadi }} x/menit<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pernapasan :
                                    </strong>{{ $row->pemeriksaanAwal?->pernapasan }} x/menit<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Suhu :
                                    </strong>{{ $row->pemeriksaanAwal?->suhu }} °C<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SpO2 :
                                    </strong>{{ $row->pemeriksaanAwal?->saturasi_o2 }} %<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Berat Badan :
                                    </strong>{{ $row->pemeriksaanAwal?->berat_badan }} kg<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tinggi Badan :
                                    </strong>{{ $row->pemeriksaanAwal?->tinggi_badan }} cm
                                    <br><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Keadaan Umum & Kesadaran</strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tingkat
                                        Kesadaran : </strong>{{ $row->pemeriksaanAwal?->kesadaran }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kesan Sakit :
                                    </strong>{{ $row->pemeriksaanAwal?->kesan_sakit }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Status Gizi :
                                    </strong>{{ $row->pemeriksaanAwal?->status_gizi }}<br><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pemeriksaan Head to Toe</strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kepala, Mata,
                                        THT, Leher :
                                    </strong>{{ $row->pemeriksaanAwal?->kepala_normal == 1 ? 'Normal' : $row->pemeriksaanAwal?->kepala_temuan }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jantung :
                                    </strong>{{ $row->pemeriksaanAwal?->jantung_normal == 1 ? 'Normal' : $row->pemeriksaanAwal?->jantung_temuan }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paru :
                                    </strong>{{ $row->pemeriksaanAwal?->paru_normal == 1 ? 'Normal' : $row->pemeriksaanAwal?->paru_temuan }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Abdomen :
                                    </strong>{{ $row->pemeriksaanAwal?->abdomen_normal == 1 ? 'Normal' : $row->pemeriksaanAwal?->abdomen_temuan }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ekstremitas :
                                    </strong>{{ $row->pemeriksaanAwal?->ekstremitas_normal == 1 ? 'Normal' : $row->pemeriksaanAwal?->ekstremitas_temuan }}
                                    <br><br>
                                    <strong>DIAGNOSIS & PERENCANAAN </strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Diagnosis Kerja :
                                    </strong>{{ $row->pemeriksaanAwal?->diagnosis_kerja }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rencana Awal :
                                    </strong>{{ $row->pemeriksaanAwal?->rencana_awal }}
                                    <hr>
                                    <small>
                                        {{ $row->pemeriksaanAwal->pengguna->pegawai->nama ?? $row->pemeriksaanAwal->pengguna->nama }}<br>
                                        {{ $row->pemeriksaanAwal->updated_at->format('d F Y, H:i') }}
                                    </small>
                                </td>
                                <td nowrap>
                                    <strong>HASIL TES</strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Waktu Tes :
                                    </strong>{{ $row->tug?->waktu_tes_detik }} detik<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Observasi Kualitatif Gerakan : </strong><br>
                                    @foreach (is_string($row->tug->observasi_kualitatif) ? json_decode($row->tug->observasi_kualitatif, true) ?? [] : (is_array($row->tug->observasi_kualitatif) ? $row->tug->observasi_kualitatif : []) as $item)
                                        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $item }}<br>
                                    @endforeach
                                    <br>
                                    <strong>PENILAIAN & REKOMENDASI</strong><br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Penilaian Risiko Jatuh :
                                    </strong>{{ $row->tug?->risiko_jatuh }}<br>
                                    <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Catatan Tambahan / Rekomendasi :
                                    </strong>{{ $row->tug?->catatan }}
                                    <hr>
                                    <small>
                                        {{ $row->tug->pengguna->pegawai->nama ?? $row->tug->pengguna->nama }}<br>
                                        {{ $row->tug->updated_at->format('d F Y, H:i') }}
                                    </small>
                                </td>
                                <td nowrap>
                                    @foreach ($row->diagnosis->icd10_uraian as $item)
                                        {{ $item->id }} - {{ $item->uraian }}<br>
                                    @endforeach
                                    <br>
                                    <strong>Diagnosis Banding (Differential Diagnosis) :
                                    </strong>{{ $row->diagnosis?->diagnosis_banding }}
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
                                        {{ $row->diagnosis->pengguna->pegawai->nama ?? $row->diagnosis->pengguna->nama }}<br>
                                        {{ $row->diagnosis->updated_at->format('d F Y, H:i') }}
                                    </small>
                                </td>
                                <td nowrap>
                                    @foreach ($row->tindakan as $item)
                                        {{ $loop->iteration }}. {{ $item->tarifTindakan->nama }} <br>
                                        <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Dokter :
                                            {{ $item->dokter->nama }}</small><br>
                                        <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Perawat :
                                            {{ $item->perawat?->nama }}</small><br>
                                        <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- {{ $item->qty }} x
                                            {{ number_format($item->biaya) }}
                                            {{ $item->diskon > 0 ? ' - ' . number_format($item->diskon) : '' }} =
                                            <strong>{{ number_format($item->biaya * $item->qty - $item->diskon) }}</strong>
                                        </small><br>
                                        <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Catatan :
                                            {{ $item->catatan }}</small><br>
                                        <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- @if ($item->membutuhkan_sitemarking)
                                                <a href="/klinik/sitemarking/form/{{ $row->id }}"
                                                    target="_blank">Site
                                                    Marking</a>
                                            @endif
                                        </small><br>
                                    @endforeach
                                    <hr>    
                                    <small>
                                        {{ $row->tindakan->first()->pengguna->pegawai->nama ?? $row->tindakan->first()->pengguna->nama }}<br>
                                        {{ $row->tindakan->first()->updated_at->format('d F Y, H:i') }}
                                    </small>
                                </td>
                                <td nowrap>
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
                                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $barang['qty'] }} x
                                                {{ number_format($barang['harga']) }} =
                                                <strong>{{ number_format($barang['subtotal']) }}</strong></small><br>
                                        @endforeach
                                        <small>Catatan :<br>{{ $item['catatan'] }}</small><br>
                                    @endforeach
                                    <hr>    
                                    <small>
                                        {{ $row->resepObat->first()->pengguna->pegawai->nama ?? $row->resepObat->first()->pengguna->nama }}<br>
                                        {{ $row->resepObat->first()->updated_at->format('d F Y, H:i') }}
                                    </small>
                                </td>
                                <td nowrap>
                                    <strong>Metode Pembayaran :</strong> {{ $row->pembayaran->metode_bayar }}<br>

                                    <strong>Total Tindakan :</strong> {{ number_format($row->pembayaran->total_tindakan) }}<br>
                                    <strong>Total Resep :</strong> {{ number_format($row->pembayaran->total_resep) }}<br>
                                    <strong>Diskon :</strong> {{ number_format($row->pembayaran->diskon) }}<br>
                                    <strong>Total :</strong> {{ number_format($row->pembayaran->total_tagihan) }}
                                    <hr>
                                    <small>
                                        {{ $row->pembayaran->pengguna->pegawai->nama ?? $row->pembayaran->pengguna->nama }}<br>
                                        {{ $row->pembayaran->updated_at->format('d F Y, H:i') }}
                                    </small>
                                </td>
                            </tr>
                        @endforeach
                    </table>
                @else
                    <div>
                        <label class="form-label">Cari Pasien</label>
                        <div wire:ignore>
                            <select class="form-control" x-init="$($el).select2({
                                width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ? '100%' : 'style',
                                dropdownAutoWidth: true,
                                templateResult: format,
                                placeholder: 'Ketik Nama/No. KTP/No. RM',
                                minimumInputLength: 3,
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
                                $wire.set('pasien', $($el).val());
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
                @endif
            </div>
        </form>
    </div>
</div>
