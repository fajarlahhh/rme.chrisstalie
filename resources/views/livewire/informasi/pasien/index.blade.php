<div>
    @section('title', 'Informasi Pasien')

    @section('breadcrumb')
        <li class="breadcrumb-item">Informasi</li>
        <li class="breadcrumb-item active">Pasien</li>
    @endsection

    <h1 class="page-header">Informasi Pasien</h1>

    @if ($dataPasien)
    @endif

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
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
                        $wire.set('noRm', $($el).val());
                    });
                    
                    function format(data) {
                        if (!data.id) {
                            return data.text;
                        }
                        var $data = $('<table><tr><th>No. RM</th><th>:</th><th>' + data.rm + '</th></tr>' +
                            '<tr><th>No. KTP</th><th>:</th><td>' + data.nik + '</td></tr>' +
                            '<tr><th>Nama</th><th>:</th><td>' + data.nama + '</td></tr>' +
                            '<tr><th>Alamat</th><th>:</th><td>' + data.alamat + '</td></tr></table>');
                        return $data;
                    }">
                    </select>
                </div>
            </div>
        </div>

        @php
            $dokter = auth()->user()->hasRole('administrator')
                ? 1
                : auth()->user()->kepegawaianPegawai?->nakes?->dokter ?? 0;
        @endphp
        <div class="panel-body ">
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
                @if ($dataPasien->rekamMedis->count() > 0)
                    <div class="panel panel-inverse panel-with-tabs" data-sortable-id="ui-unlimited-tabs-1">
                        <!-- BEGIN panel-heading -->
                        <div class="panel-heading p-0">
                            <!-- BEGIN nav-tabs -->
                            <div class="tab-overflow">
                                <ul class="nav nav-tabs nav-tabs-inverse">
                                    <li class="nav-item prev-button"><a href="javascript:;" data-click="prev-tab"
                                            class="nav-link text-primary"><i class="fa fa-arrow-left"></i></a></li>
                                    @foreach ($dataPasien->rekamMedis as $key => $row)
                                        <li class="nav-item"><a href="#nav-tab-{{ $key + 1 }}"
                                                data-bs-toggle="tab"
                                                class="nav-link {{ $loop->first ? 'active' : '' }}">{{ $row->created_at->format('d F Y') }}</a>
                                        </li>
                                    @endforeach
                                    <li class="nav-item next-button"><a href="javascript:;" data-click="next-tab"
                                            class="nav-link text-primary"><i class="fa fa-arrow-right"></i></a></li>
                                </ul>
                            </div>
                            <!-- END nav-tabs -->
                            <div class="panel-heading-btn me-2 ms-2 d-flex">
                                <a href="javascript:;" class="btn btn-xs btn-icon btn-circle btn-secondary"
                                    data-toggle="panel-expand"><i class="fa fa-expand"></i></a>
                            </div>
                        </div>
                        <!-- END panel-heading -->
                        <!-- BEGIN tab-content -->
                        <div class="panel-body tab-content">
                            @foreach ($dataPasien->rekamMedis as $key => $row)
                                <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }} table-responsive"
                                    id="nav-tab-{{ $key + 1 }}">
                                    <table class="table table-bordered table-striped fs-12px">
                                        <tr>
                                            <th class="bg-secondary-subtle w-150px">Proses</th>
                                            <th class="bg-secondary-subtle" style="min-width: 500px;">Hasil</th>
                                            <th class="bg-secondary-subtle">Operator/Waktu</th>
                                        </tr>
                                        <tr>
                                            <th nowrap>
                                                <h5>Registrasi</h5>
                                            </th>
                                            <td nowrap>
                                                <strong>Nomor : </strong>{{ $row->id }}<br>
                                                <strong>Dokter : </strong>{{ $row->nakes?->nama }}<br>
                                                <strong>Keluhan Awal : </strong>{{ $row->keluhan_awal }}
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $row->pengguna->nama }}<br>
                                                    {{ $row->created_at->format('d F Y, H:i') }}
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th nowrap>
                                                <h5>Hasil Pemeriksaan Awal</h5>
                                            </th>

                                            <td nowrap>
                                                <strong>ANAMNESIS </strong><br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Keluhan Utama :
                                                </strong>{{ $row->pemeriksaanAwal?->keluhan_utama }}<br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Riwayat Penyakit Sekarang :
                                                </strong>{{ $row->pemeriksaanAwal?->riwayat_sekarang }}<br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Riwayat Penyakit Dahulu & Riwayat
                                                    Penyakit
                                                    Keluarga :
                                                </strong>{{ $row->pemeriksaanAwal?->riwayat_dahulu }}<br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Riwayat Alergi :
                                                </strong>{{ $row->pemeriksaanAwal?->riwayat_alergi }}
                                                <hr>
                                                <strong>PEMERIKSAAN FISIK </strong><br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tanda-Tanda
                                                    Vital</strong><br>
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tekanan
                                                            Darah :
                                                        </strong>{{ $row->pemeriksaanAwal?->tekanan_darah }} mmHg<br>
                                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Nadi
                                                            :
                                                        </strong>{{ $row->pemeriksaanAwal?->nadi }} x/menit<br>
                                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pernapasan
                                                            :
                                                        </strong>{{ $row->pemeriksaanAwal?->pernapasan }} x/menit<br>
                                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Suhu
                                                            :
                                                        </strong>{{ $row->pemeriksaanAwal?->suhu }} °C<br>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;SpO2
                                                            :
                                                        </strong>{{ $row->pemeriksaanAwal?->saturasi_o2 }} %<br>
                                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Berat
                                                            Badan
                                                            :
                                                        </strong>{{ $row->pemeriksaanAwal?->berat_badan }} kg<br>
                                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tinggi
                                                            Badan
                                                            :
                                                        </strong>{{ $row->pemeriksaanAwal?->tinggi_badan }} cm
                                                    </div>
                                                </div>
                                                <br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Keadaan Umum &
                                                    Kesadaran</strong><br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Tingkat
                                                    Kesadaran : </strong>{{ $row->pemeriksaanAwal?->kesadaran }}<br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kesan
                                                    Sakit
                                                    :
                                                </strong>{{ $row->pemeriksaanAwal?->kesan_sakit }}<br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Status
                                                    Gizi
                                                    :
                                                </strong>{{ $row->pemeriksaanAwal?->status_gizi }}<br><br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Pemeriksaan Head to
                                                    Toe</strong><br>
                                                <div class="row">
                                                    <div class="col-xl-6">
                                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Kepala,
                                                            Mata,
                                                            THT, Leher :
                                                        </strong>{{ $row->pemeriksaanAwal?->kepala_normal == 1 ? 'Normal' : $row->pemeriksaanAwal?->kepala_temuan }}<br>
                                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jantung
                                                            :
                                                        </strong>{{ $row->pemeriksaanAwal?->jantung_normal == 1 ? 'Normal' : $row->pemeriksaanAwal?->jantung_temuan }}<br>
                                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Paru
                                                            :
                                                        </strong>{{ $row->pemeriksaanAwal?->paru_normal == 1 ? 'Normal' : $row->pemeriksaanAwal?->paru_temuan }}
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Abdomen
                                                            :
                                                        </strong>{{ $row->pemeriksaanAwal?->abdomen_normal == 1 ? 'Normal' : $row->pemeriksaanAwal?->abdomen_temuan }}<br>
                                                        <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Ekstremitas
                                                            :
                                                        </strong>{{ $row->pemeriksaanAwal?->ekstremitas_normal == 1 ? 'Normal' : $row->pemeriksaanAwal?->ekstremitas_temuan }}
                                                    </div>
                                                </div>
                                                <hr>
                                                <strong>DIAGNOSIS & PERENCANAAN </strong><br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Diagnosis Kerja :
                                                </strong>{{ $row->pemeriksaanAwal?->diagnosis_kerja }}<br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Rencana Awal :
                                                </strong>{{ $row->pemeriksaanAwal?->rencana_awal }}
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $row->pemeriksaanAwal?->pengguna->nama }}<br>
                                                    {{ $row->pemeriksaanAwal?->created_at->format('d F Y, H:i') }}
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th nowrap>
                                                <h5>Tes Up And Go</h5>
                                            </th>
                                            <td nowrap>
                                                <strong>HASIL TES</strong><br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Waktu Tes :
                                                </strong>{{ $row->tug?->waktu_tes_detik }} detik<br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Observasi Kualitatif Gerakan :
                                                </strong><br>
                                                @foreach (is_string($row->tug?->observasi_kualitatif) ? json_decode($row->tug?->observasi_kualitatif, true) ?? [] : (is_array($row->tug?->observasi_kualitatif) ? $row->tug?->observasi_kualitatif : []) as $item)
                                                    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $item }}<br>
                                                @endforeach
                                                <br>
                                                <strong>PENILAIAN & REKOMENDASI</strong><br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Penilaian Risiko Jatuh :
                                                </strong>{{ $row->tug?->risiko_jatuh }}<br>
                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Catatan Tambahan / Rekomendasi :
                                                </strong>{{ $row->tug?->catatan }}
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $row->tug?->pengguna->nama }}<br>
                                                    {{ $row->tug?->created_at->format('d F Y, H:i') }}
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th nowrap>
                                                <h5>Diagnosis</h5>
                                            </th>
                                            <td nowrap>
                                                @if ($row->diagnosis)
                                                    @foreach ($row->diagnosis?->icd10_uraian as $item)
                                                        {{ $item->id }} - {{ $item->uraian }}<br>
                                                    @endforeach
                                                @endif
                                                <br>
                                                <strong>Diagnosis Banding (Differential Diagnosis) :
                                                </strong>{{ $row->diagnosis?->diagnosis_banding }}
                                                <br><br>
                                                <strong>DOKUMENTASI</strong><br>
                                                <div class="row">
                                                    <div class="col-4">
                                                        @if ($row->diagnosis)
                                                            @foreach ($row->diagnosis?->file as $item)
                                                                <img src="{{ $item->link }}"
                                                                    alt="{{ $item->judul }}"
                                                                    class="img-fluid w-100"><br>
                                                            @endforeach
                                                        @endif
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $row->diagnosis?->pengguna->nama }}<br>
                                                    {{ $row->diagnosis?->created_at->format('d F Y, H:i') }}
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <h5 nowrap>Tindakan</h5>
                                            </th>
                                            <td nowrap>
                                                <div class="row ps-2 pe-2">
                                                    @foreach ($row->tindakan as $item)
                                                        <div class="col-xl-6">
                                                            <div class="alert alert-indigo">
                                                                <strong>{{ $loop->iteration }}.
                                                                    {{ $item->tarifTindakan->nama }}</strong>
                                                                <hr>
                                                                <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Dokter :
                                                                    {{ $item->dokter?->nama }}</small><br>
                                                                <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;- Perawat :
                                                                    {{ $item->perawat?->nama }}</small><br>
                                                                <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-
                                                                    {{ $item->qty }} x
                                                                    {{ number_format($item->biaya) }}
                                                                    {{ $item->diskon > 0 ? ' - ' . number_format($item->diskon) : '' }}
                                                                    =
                                                                    <strong>{{ number_format($item->biaya * $item->qty - $item->diskon) }}</strong>
                                                                </small><br>
                                                                <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<strong>Catatan
                                                                        :</strong>
                                                                    &nbsp;&nbsp;&nbsp;{{ $item->catatan }}</small><br>
                                                                @if ($item->membutuhkan_sitemarking)
                                                                    <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-
                                                                        <a href="/klinik/sitemarking/form/{{ $row->id }}"
                                                                            target="_blank">Site
                                                                            Marking</a>
                                                                    </small>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $row->tindakan?->first()?->pengguna->nama }}<br>
                                                    {{ $row->tindakan?->first()?->created_at->format('d F Y, H:i') }}
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th nowrap>
                                                <h5>Resep Obat</h5>
                                            </th>
                                            <td nowrap>
                                                <div class="row ps-2 pe-2">
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
                                                        <div class="col-xl-6 ">
                                                            <div class="alert alert-primary">
                                                                <strong>Resep {{ $loop->iteration }} :
                                                                    {{ $item['nama'] }}</strong>
                                                                <hr>
                                                                @foreach ($item['barang'] as $barang)
                                                                    <small>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-
                                                                        {{ $barang['nama'] }}
                                                                        /
                                                                        {{ $barang['satuan'] }}
                                                                        <br>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{{ $barang['qty'] }}
                                                                        x
                                                                        {{ number_format($barang['harga']) }} =
                                                                        {{ number_format($barang['subtotal']) }}</small><br>
                                                                @endforeach
                                                                <small><strong>Catatan
                                                                        :</strong><br>&nbsp;&nbsp;&nbsp;{{ $item['catatan'] }}</small>
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $row->resepObat?->first()?->pengguna->nama }}<br>
                                                    {{ $row->resepObat?->first()?->created_at->format('d F Y, H:i') }}
                                                </small>
                                            </td>
                                        </tr>
                                        <tr>
                                            <th>
                                                <h5>Pembayaran</h5>
                                            </th>
                                            <td nowrap>
                                                <strong>No. Nota :</strong> {{ $row->pembayaran?->id }}<br>
                                                <strong>Total Tindakan :</strong>
                                                {{ number_format($row->pembayaran?->total_tindakan) }}<br>
                                                <strong>Total Resep :</strong>
                                                {{ number_format($row->pembayaran?->total_resep) }}<br>
                                                <strong>Diskon :</strong>
                                                {{ number_format($row->pembayaran?->diskon) }}<br>
                                                <strong>Total Tagihan :</strong>
                                                {{ number_format($row->pembayaran?->total_tagihan) }}
                                                <br><br>
                                                <div class="row ps-2 pe-2">
                                                    <div class="col-xl-6">
                                                        <div class="alert alert-success">
                                                            <small><strong>Pembayaran 1 </strong>
                                                                <hr>
                                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Metode
                                                                    Pembayaran
                                                                    :</strong>
                                                                {{ $row->pembayaran?->metode_bayar }}<br>
                                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah
                                                                    Pembayaran :</strong>
                                                                {{ number_format($row->pembayaran?->bayar) }}<br>
                                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Catatan
                                                                    :</strong>
                                                                {{ $row->pembayaran?->keterangan }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                    <div class="col-xl-6">
                                                        <div class="alert alert-success">
                                                            <small><strong>Pembayaran 2 </strong>
                                                                <hr>
                                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Metode
                                                                    Pembayaran
                                                                    :</strong>
                                                                {{ $row->pembayaran?->metode_bayar_2 }}<br>
                                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Jumlah
                                                                    :</strong>
                                                                {{ number_format($row->pembayaran?->bayar_2) }}<br>
                                                                <strong>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;Catatan
                                                                    :</strong>
                                                                {{ $row->pembayaran?->keterangan_2 }}
                                                            </small>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td>
                                                <small>
                                                    {{ $row->pembayaran?->pengguna->nama }}<br>
                                                    {{ $row->pembayaran?->created_at->format('d F Y, H:i') }}
                                                </small>
                                            </td>
                                        </tr>
                                    </table>
                                </div>
                            @endforeach
                        </div>
                        <!-- END tab-content -->
                        <!-- BEGIN hljs-wrapper -->
                        <div class="hljs-wrapper">
                            <pre><code class="html" data-url="../assets/data/ui-unlimited-tabs/code-1.json"></code></pre>
                        </div>
                        <!-- END hljs-wrapper -->
                    </div>
                @else
                    <div class="alert alert-warning text-center">
                        <strong>Data Rekam Medis tidak ada</strong>
                    </div>
                @endif
            @endif
        </div>
    </div>

    <div wire:loading>
        <x-loading />
    </div>
</div>
