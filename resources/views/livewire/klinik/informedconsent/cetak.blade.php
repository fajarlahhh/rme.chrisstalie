<img src="/assets/img/kop.png" class="mt-20px">
<div class="p-40px">
    <div class="text-center">
        <h5>PERSETUJUAN TINDAKAN KEDOKTERAN</h5>
        <h6 class="text-muted">(INFORMED CONSENT)</h6>
    </div>

    <h5 class="pb-2">DATA PASIEN : </h5>
    <div class="row mb-4">
        <div class="col-md-6">
            <strong>Nama Lengkap:</strong> {{ $data->pasien->nama }}
        </div>
        <div class="col-md-6">
            <strong>No. Rekam Medis:</strong> {{ $data->pasien_id }}
        </div>
        <div class="col-md-6">
            <strong>Tanggal Lahir:</strong> {{ $data->pasien->tanggal_lahir->format('d-m-Y') }}
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

    @php
        $hari = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jumat',
            'Saturday' => 'Sabtu',
        ];
        $tanggal = date('Y-m-d');
        $namaHari = $hari[date('l', strtotime($tanggal))];
        $tanggalFormat = date('d F Y', strtotime($tanggal));
    @endphp
    <p>Pada hari {{ $namaHari }}, tanggal {{ $tanggalFormat }}, Dokter Penanggung Jawab:
        <strong>{{ $data->nakes->nama }}</strong> telah
        memberikan penjelasan mengenai:
    </p>
    <div class="table-responsive">
        <table class="table table-bordered">
            <tbody>
                <tr>
                    <th class="w-50px">Diagnosis</th>
                    <td>
                        {{ $data->diagnosis->icd10_uraian->map(fn($q) => $q['id'] . ' - ' . $q['uraian'])->implode(', ') }}
                    </td>
                </tr>
                @foreach ($data->tindakanDenganInformConsent as $index => $item)
                    @if ($index == 0)
                        <tr>
                            <th class="w-50px" rowspan="{{ $data->tindakanDenganInformConsent->count() }}" nowrap>
                                Tindakan
                                Medis
                            </th>
                            <td>
                                <ul>
                                    <li><strong>{{ $item->tarifTindakan->nama }}</strong>
                                        <br>
                                        <small>{{ $item->deskripsi }}</small>
                                    </li>
                                    <li>Risiko & Komplikasi : {{ $item->risiko_komplikasi }}
                                    </li>
                                    <li>Alternatif & Risikonya : {{ $item->alternatif_risiko }}
                                    </li>
                                    <li>Prognosis : {{ $item->prognosis }}</li>
                                </ul>
                            </td>
                        </tr>
                    @else
                        <tr>
                            <td>
                                <ul>
                                    <li><strong>{{ $item->tarifTindakan->nama }}</strong>
                                        <br>
                                        <small>{{ $item->deskripsi }}</small>
                                    </li>
                                    <li>Risiko & Komplikasi : {{ $item->risiko_komplikasi }}
                                    </li>
                                    <li>Alternatif & Risikonya : {{ $item->alternatif_risiko }}
                                    </li>
                                    <li>Prognosis : {{ $item->prognosis }}</li>
                                </ul>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>


    <p>Setelah mendapatkan penjelasan yang lengkap, saya menyatakan telah mengerti
        dan dengan sadar membuat keputusan: <strong>MENYETUJUI TINDAKAN KEDOKTERAN</strong></p>

    <table class="table table-borderless">
        <tr>
            <td class="text-center fs-12px">Tanda Tangan Pasien/Wali
                <br>
                <br>
                <br>
                <br>
                {{ $data->informedConsent->ttd_pasien }}
            </td>
            <td class="text-center fs-12px">Nama Saksi (Perawat/Staf)
                <br>
                <br>
                <br>
                <br>
                {{ $data->informedConsent->ttd_saksi }}
            </td>
        </tr>
    </table>

</div>
