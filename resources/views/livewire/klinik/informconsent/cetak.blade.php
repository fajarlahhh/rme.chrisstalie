<div class="text-center">
    <img src="/assets/img/login.png" class="w-200px"><hr>
    <div class="document-title">
        <h4>PERSETUJUAN TINDAKAN KEDOKTERAN</h4>
        <h5>(INFORMED CONSENT)</h5>
    </div>
</div>

<h5 class="mb-3 border-bottom pb-2">BAGIAN 1: DATA PASIEN</h5>
<div class="row mb-4">
    <div class="col-md-6">
        <strong>Nama Lengkap:</strong> {{ $data->pasien->nama }}
    </div>
    <div class="col-md-6">
        <strong>No. Rekam Medis:</strong> {{ $data->pasien_id }}
    </div>
    <div class="col-md-6">
        <strong>Tanggal Lahir:</strong> {{ $data->pasien->tanggal_lahir }}
    </div>
    <div class="col-md-6">
        <strong>Alamat:</strong> {{ $data->pasien->alamat }}
    </div>
    <div class="col-md-6">
        <strong>Jenis Kelamin:</strong> {{ $data->pasien->jenis_kelamin }}
    </div>
    <div class="col-md-6">
        <strong>Usia:</strong> {{ $data->pasien->umur }}
    </div>
</div>

<h5 class="mb-3 border-bottom pb-2">BAGIAN 2: PEMBERIAN INFORMASI OLEH DOKTER
</h5>
<p>Dokter Penanggung Jawab: <strong>{{ $data->nakes->nama }}</strong> telah
    memberikan penjelasan mengenai:</p>
<div class="table-responsive">
    <table class="table table-bordered">
        <tbody>
            <tr>
                <th style="width: 25%;">Diagnosis</th>
                <td>
                    {{ $data->diagnosis->icd10_uraian->map(fn($q) => $q['id'] . ' - ' . $q['uraian'])->implode(', ') }}
                </td>
            </tr>
            <tr>
                <th>Tindakan Medis</th>
                <td>
                    <ul>
                        @foreach ($data->tindakanDenganInformConsent as $item)
                            <li>{{ $item->tarifTindakan->nama }}
                                <br>
                                <small>{{ $item->deskripsi }}</small>
                            </li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            <tr>
                <th>Tujuan & Manfaat</th>
                <td>
                    <ul>
                        @foreach ($data->tindakanDenganInformConsent as $item)
                            <li>{{ $item->tujuan_manfaat }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            <tr>
                <th>Risiko & Komplikasi</th>
                <td>
                    <ul>
                        @foreach ($data->tindakanDenganInformConsent as $item)
                            <li>{{ $item->risiko_komplikasi }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            <tr>
                <th>Alternatif & Risikonya</th>
                <td>
                    <ul>
                        @foreach ($data->tindakanDenganInformConsent as $item)
                            <li>{{ $item->alternatif_risiko }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
            <tr>
                <th>Prognosis</th>
                <td>
                    <ul>
                        @foreach ($data->tindakanDenganInformConsent as $item)
                            <li>{{ $item->prognosis }}</li>
                        @endforeach
                    </ul>
                </td>
            </tr>
        </tbody>
    </table>
</div>


<h5 class="mt-4 mb-3 border-bottom pb-2">BAGIAN 3: PERNYATAAN PERSETUJUAN</h5>
<p>Setelah mendapatkan penjelasan yang lengkap, saya menyatakan telah mengerti
    dan dengan sadar membuat keputusan: <strong>MENYETUJUI</strong></p>

<h5 class="mb-3 border-bottom pb-2">BAGIAN 4: TANDA TANGAN</h5>
<table class="table">
    <tr>
        <td>Tanda Tangan Pasien/Wali
            <br>
            <br>
            <br>
            {{ $data->ttd_pasien }}
        </td>
        <td>Nama Saksi (Perawat/Staf)
            <br>
            <br>
            <br>
            {{ $data->ttd_saksi }}
        </td>
    </tr>
</table>
