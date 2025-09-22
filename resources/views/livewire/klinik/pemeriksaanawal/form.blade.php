<div>
    @section('title', 'Input Pemeriksaan Awal')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item">Pemeriksaan Awal</li>
        <li class="breadcrumb-item active">Input</li>
    @endsection

    <h1 class="page-header">Pemeriksaan Awal <small>Input</small></h1>

    <x-alert />
    <div class="note alert-primary mb-2">
        <div class="note-content">
            <h5>Data Pasien</h5>
            <hr>
            <table class="w-100">
                <tr>
                    <td class="w-200px">No. RM</td>
                    <td class="w-10px">:</td>
                    <td>{{ $data->pasien_id }}</td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td class="w-10px">:</td>
                    <td>{{ $data->pasien->nama }}</td>
                </tr>
                <tr>
                    <td>Usia</td>
                    <td class="w-10px">:</td>
                    <td>{{ $data->pasien->umur }} Tahun</td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td class="w-10px">:</td>
                    <td>{{ $data->pasien->jenis_kelamin }}</td>
                </tr>
            </table>
        </div>
    </div>
    <ul class="nav nav-tabs" role="tablist">
        <li class="nav-item" role="presentation" wire:ignore>
            <a href="#default-tab-0" data-bs-toggle="tab" class="nav-link active" aria-selected="true" role="tab">
                <span class="d-sm-none">Pemeriksaan Awal</span>
                <span class="d-sm-block d-none">Pemeriksaan Awal</span>
            </a>
        </li>
        <li class="nav-item" role="presentation" wire:ignore>
            <a href="#default-tab-1" data-bs-toggle="tab" class="nav-link" aria-selected="true" role="tab">
                <span class="d-sm-none">TUG</span>
                <span class="d-sm-block d-none">Tes Up and Go</span>
            </a>
        </li>
    </ul>
    <div class="tab-content panel rounded-0 p-3 m-0">
        <div class="tab-pane fade active show" id="default-tab-0" role="tabpanel" wire:ignore.self>
            <form wire:submit.prevent="submitPemeriksaanAwal">
                <div class="panel panel-inverse bg-gray-100">
                    <div class="panel-heading">
                        <h4 class="panel-title">Anamnesis (Subjective)</h4>
                    </div>
                    <div class="panel-body">
                        <div class="mb-3">
                            <label for="keluhan_utama" class="form-label">Keluhan Utama</label>
                            <textarea class="form-control" id="keluhan_utama" name="keluhan_utama"
                                placeholder="Apa keluhan utama yang membawa pasien datang?" wire:model="keluhan_utama"></textarea>
                            @error('keluhan_utama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="riwayat_sekarang" class="form-label">Riwayat Penyakit Sekarang</label>
                            <textarea class="form-control" id="riwayat_sekarang" name="riwayat_sekarang"
                                placeholder="Jelaskan detail keluhan sejak kapan, lokasi, kronologi, kualitas, kuantitas, faktor yang memperberat dan memperingan, serta gejala penyerta."
                                wire:model="riwayat_sekarang"></textarea>
                            @error('riwayat_sekarang')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="riwayat_dahulu" class="form-label">Riwayat Penyakit Dahulu & Riwayat
                                Penyakit Keluarga</label>
                            <textarea class="form-control" id="riwayat_dahulu" name="riwayat_dahulu"
                                placeholder="Sebutkan penyakit kronis (hipertensi, DM), riwayat operasi, rawat inap, dan penyakit signifikan di keluarga."
                                wire:model="riwayat_dahulu"></textarea>
                            @error('riwayat_dahulu')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="riwayat_alergi" class="form-label">Riwayat Alergi</label>
                            <input type="text" class="form-control" id="riwayat_alergi" name="riwayat_alergi"
                                placeholder="Sebutkan alergi obat atau makanan, jika tidak ada tulis 'Tidak Ada'"
                                wire:model="riwayat_alergi">
                            @error('riwayat_alergi')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>

                <div class="panel panel-inverse bg-gray-100">
                    <div class="panel-heading">
                        <h4 class="panel-title">Pemeriksaan Fisik (Objective)</h4>
                    </div>
                    <div class="panel-body">
                        <h5 class="mb-3">Tanda-Tanda Vital</h5>
                        <div class="row vital-signs-grid">
                            <div class="col-md-3 mb-3">
                                <label for="tekanan_darah" class="form-label">Tekanan Darah (mmHg)</label>
                                <input type="text" class="form-control" id="tekanan_darah" name="tekanan_darah"
                                    placeholder="120/80" wire:model="tekanan_darah">
                                @error('tekanan_darah')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="nadi" class="form-label">Nadi (x/menit)</label>
                                <input type="number" class="form-control" id="nadi" name="nadi"
                                    placeholder="80" wire:model="nadi">
                                @error('nadi')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="pernapasan" class="form-label">Pernapasan (x/menit)</label>
                                <input type="number" class="form-control" id="pernapasan" name="pernapasan"
                                    placeholder="18" wire:model="pernapasan">
                                @error('pernapasan')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="suhu" class="form-label">Suhu (°C)</label>
                                <input type="number" class="form-control" id="suhu" name="suhu"
                                    step="0.1" placeholder="36.5" wire:model="suhu">
                                @error('suhu')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="saturasi_o2" class="form-label">SpO2 (%)</label>
                                <input type="number" class="form-control" id="saturasi_o2" name="saturasi_o2"
                                    placeholder="98" wire:model="saturasi_o2">
                                @error('saturasi_o2')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="berat_badan" class="form-label">Berat Badan (kg)</label>
                                <input type="number" class="form-control" id="berat_badan" name="berat_badan"
                                    step="0.1" placeholder="65.5" wire:model="berat_badan">
                                @error('berat_badan')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-3 mb-3">
                                <label for="tinggi_badan" class="form-label">Tinggi Badan (cm)</label>
                                <input type="number" class="form-control" id="tinggi_badan" name="tinggi_badan"
                                    placeholder="170" wire:model="tinggi_badan">
                                @error('tinggi_badan')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Keadaan Umum & Kesadaran</h5>
                        <div class="row patient-info-grid">
                            <div class="col-md-4 mb-3">
                                <label for="kesadaran" class="form-label">Tingkat Kesadaran</label>
                                <select class="form-select" id="kesadaran" name="kesadaran" wire:model="kesadaran">
                                    <option value="Compos Mentis">Compos Mentis</option>
                                    <option value="Apatis">Apatis</option>
                                    <option value="Somnolen">Somnolen</option>
                                    <option value="Sopor">Sopor</option>
                                    <option value="Koma">Koma</option>
                                </select>
                                @error('kesadaran')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="kesan_sakit" class="form-label">Kesan Sakit</label>
                                <select class="form-select" id="kesan_sakit" name="kesan_sakit"
                                    wire:model="kesan_sakit">
                                    <option value="Tidak Tampak Sakit">Tidak Tampak Sakit</option>
                                    <option value="Sakit Ringan">Sakit Ringan</option>
                                    <option value="Sakit Sedang">Sakit Sedang</option>
                                    <option value="Sakit Berat">Sakit Berat</option>
                                </select>
                                @error('kesan_sakit')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="status_gizi" class="form-label">Status Gizi</label>
                                <select class="form-select" id="status_gizi" name="status_gizi"
                                    wire:model="status_gizi">
                                    <option value="Baik">Baik</option>
                                    <option value="Kurang">Kurang</option>
                                    <option value="Buruk">Buruk</option>
                                    <option value="Lebih">Lebih / Obesitas</option>
                                </select>
                            </div>
                        </div>

                        <h5 class="mt-4 mb-3">Pemeriksaan Head to Toe</h5>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <div class="pemeriksaan-item">
                                    <h6>Kepala, Mata, THT, Leher</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="kepala_normal"
                                            wire:model.live="kepala_normal">
                                        <label class="form-check-label" for="kepala_normal">Dalam Batas
                                            Normal</label>
                                    </div>
                                    <textarea class="form-control" id="kepala_temuan" name="kepala_temuan" placeholder="Jelaskan temuan abnormal..."
                                        wire:model="kepala_temuan" @if ($kepala_normal) disabled @endif></textarea>
                                    @if (!$kepala_normal)
                                        @error('kepala_temuan')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="pemeriksaan-item">
                                    <h6>Thorax - Jantung</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="jantung_normal"
                                            wire:model.live="jantung_normal">
                                        <label class="form-check-label" for="jantung_normal">Dalam Batas
                                            Normal</label>
                                    </div>
                                    <textarea class="form-control" id="jantung_temuan" name="jantung_temuan" placeholder="Jelaskan temuan abnormal..."
                                        wire:model="jantung_temuan" @if ($jantung_normal) disabled @endif></textarea>
                                    @if (!$jantung_normal)
                                        @error('jantung_temuan')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="pemeriksaan-item">
                                    <h6>Thorax - Paru</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="paru_normal"
                                            wire:model.live="paru_normal">
                                        <label class="form-check-label" for="paru_normal">Dalam Batas
                                            Normal</label>
                                    </div>
                                    <textarea class="form-control" id="paru_temuan" name="paru_temuan" placeholder="Jelaskan temuan abnormal..."
                                        wire:model.live="paru_temuan" @if ($paru_normal) disabled @endif></textarea>
                                    @if (!$paru_normal)
                                        @error('paru_temuan')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="pemeriksaan-item">
                                    <h6>Abdomen</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="abdomen_normal"
                                            wire:model.live="abdomen_normal">
                                        <label class="form-check-label" for="abdomen_normal">Dalam Batas
                                            Normal</label>
                                    </div>
                                    <textarea class="form-control" id="abdomen_temuan" name="abdomen_temuan" placeholder="Jelaskan temuan abnormal..."
                                        wire:model="abdomen_temuan" @if ($abdomen_normal) disabled @endif></textarea>
                                    @if (!$abdomen_normal)
                                        @error('abdomen_temuan')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                            <div class="col-md-12 mb-3">
                                <div class="pemeriksaan-item">
                                    <h6>Ekstremitas & Kulit</h6>
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" id="ekstremitas_normal"
                                            wire:model.live="ekstremitas_normal">
                                        <label class="form-check-label" for="ekstremitas_normal">Dalam Batas
                                            Normal</label>
                                    </div>
                                    <textarea class="form-control" id="ekstremitas_temuan" name="ekstremitas_temuan"
                                        placeholder="Jelaskan temuan abnormal..." wire:model="ekstremitas_temuan"
                                        @if ($ekstremitas_normal) disabled @endif></textarea>
                                    @if (!$ekstremitas_normal)
                                        @error('ekstremitas_temuan')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="panel panel-inverse bg-gray-100">
                    <div class="panel-heading">
                        <h4 class="panel-title">Diagnosis & Perencanaan (Assessment & Plan)</h4>
                    </div>
                    <div class="panel-body">
                        <div class="mb-3">
                            <label for="diagnosis_kerja" class="form-label">Diagnosis Kerja</label>
                            <textarea class="form-control" id="diagnosis_kerja" name="diagnosis_kerja"
                                placeholder="Tuliskan diagnosis kerja berdasarkan anamnesis dan pemeriksaan fisik." wire:model="diagnosis_kerja"></textarea>
                            @error('diagnosis_kerja')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="rencana_awal" class="form-label">Rencana Awal</label>
                            <textarea class="form-control" id="rencana_awal" name="rencana_awal"
                                placeholder="Tuliskan rencana awal, meliputi:&#10;- Terapi/Tindakan&#10;- Pemeriksaan Penunjang (jika perlu)&#10;- Edukasi Pasien&#10;- Rencana Rujukan (jika perlu)"
                                wire:model="rencana_awal"></textarea>
                            @error('rencana_awal')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <hr>
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                @if ($data->pemeriksaanAwal)
                    <button type="button" class="btn btn-info m-r-3" wire:loading.attr="disabled"
                        onclick="window.location.href='/klinik/diagnosis/form/{{ $data->id }}'">
                        <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                        Lanjut Diagnosis
                    </button>
                @endif
                <button type="button" class="btn btn-warning m-r-3" wire:loading.attr="disabled"
                    onclick="window.location.href='/klinik/pemeriksaanawal'">
                    <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                    Data
                </button>
            </form>
        </div>
        <div class="tab-pane fade" id="default-tab-1" role="tabpanel" wire:ignore.self>
            <form wire:submit.prevent="submitTug">
                <div class="note alert-secondary mb-2">
                    <div class="note-content">
                        <h3>Hasil Tes</h3>
                        <div class="mb-3">
                            <label class="form-label">Waktu yang Dibutuhkan:</label>
                            <div class="input-group"> <input type="number" id="waktu_tes_detik"
                                    name="waktu_tes_detik" placeholder="Contoh: 11.5" wire:model="waktu_tes_detik"
                                    step="0.01" class="form-control" required>
                                <div class="input-group-append">
                                    <span class="input-group-text bg-info text-white">detik</span>
                                </div>
                            </div>
                            <small class="text-muted">Waktu > 14 detik dapat mengindikasikan peningkatan risiko
                                jatuh.</small>
                            @error('waktu_tes_detik')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <label class="form-label">Observasi Kualitatif Gerakan (Pilih yang sesuai):</label>
                        @php
                            // Daftar observasi kualitatif sebagai array agar lebih mudah di-maintain dan menghindari duplikasi value
                            $observasiOptions = [
                                [
                                    'id' => 'observasi_lambat_ragu',
                                    'label' => 'Mulai dengan lambat/ragu-ragu',
                                    'value' => 'Mulai dengan lambat/ragu-ragu',
                                    'col' => 12,
                                ],
                                [
                                    'id' => 'observasi_tidak_seimbang',
                                    'label' => 'Kehilangan keseimbangan saat berjalan',
                                    'value' => 'Kehilangan keseimbangan saat berjalan',
                                    'col' => 12,
                                ],
                                [
                                    'id' => 'observasi_langkah_pendek',
                                    'label' => 'Langkah pendek dan tidak normal',
                                    'value' => 'Langkah pendek dan tidak normal',
                                    'col' => 12,
                                ],
                                [
                                    'id' => 'observasi_berhenti_saat_jalan',
                                    'label' => 'Berhenti saat sedang berjalan',
                                    'value' => 'Berhenti saat sedang berjalan',
                                    'col' => '6 col-md-3',
                                ],
                                [
                                    'id' => 'observasi_bergoyang',
                                    'label' => 'Badan tampak bergoyang (swaying)',
                                    'value' => 'Badan tampak bergoyang (swaying)',
                                    'col' => '6 col-md-3',
                                ],
                                [
                                    'id' => 'observasi_berbalik_tidak_stabil',
                                    'label' => 'Berbalik tidak stabil',
                                    'value' => 'Berbalik tidak stabil',
                                    'col' => '6 col-md-3',
                                ],
                                [
                                    'id' => 'observasi_berpegangan',
                                    'label' => 'Berpegangan pada objek sekitar untuk bantuan',
                                    'value' => 'Berpegangan pada objek sekitar untuk bantuan',
                                    'col' => '6 col-md-3',
                                ],
                            ];
                        @endphp
                        <div class="row g-2">
                            @foreach ($observasiOptions as $idx => $opt)
                                @if ($idx === 3)
                                    <div class="col-12">
                                        <hr>
                                    </div>
                                @endif
                                <div class="col-{{ $opt['col'] }}">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="{{ $opt['id'] }}"
                                            value="{{ $opt['value'] }}" wire:model="observasi" />
                                        <label class="form-check-label" for="{{ $opt['id'] }}">
                                            {{ $opt['label'] }}
                                        </label>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        @error('observasi')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
                <div class="note alert-secondary mb-2">
                    <div class="note-content">
                        <h3>Penilaian & Rekomendasi</h3>
                        <div class="mb-3">
                            <label class="form-label">Penilaian Risiko Jatuh:</label>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="risiko_jatuh" value="Rendah"
                                    wire:model="risiko_jatuh" id="risiko_jatuh_rendah">
                                <label class="form-check-label" for="risiko_jatuh_rendah">Risiko Rendah
                                    (Mobilitas
                                    Normal)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="risiko_jatuh"
                                    id="risiko_jatuh_sedang" value="Sedang" wire:model="risiko_jatuh">
                                <label class="form-check-label" for="risiko_jatuh_sedang">Risiko Sedang (Perlu
                                    observasi_kualitatif lanjut)</label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="risiko_jatuh"
                                    id="risiko_jatuh_tinggi" value="Tinggi" wire:model="risiko_jatuh">
                                <label class="form-check-label" for="risiko_jatuh_tinggi">Risiko Tinggi (Perlu
                                    intervensi)</label>
                            </div>
                            @error('risiko_jatuh')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Tambahan / Rekomendasi:</label>
                            <textarea id="catatan" rows="4" wire:model="catatan" class="form-control"
                                placeholder="Tulis catatan observasi_kualitatif lain atau rencana tindak lanjut..."></textarea>
                            @error('catatan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                </div>
                <hr>
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                @if ($data->pemeriksaanAwal)
                    <button type="button" class="btn btn-info m-r-3" wire:loading.attr="disabled"
                        onclick="window.location.href='/klinik/diagnosis/form/{{ $data->id }}'">
                        <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                        Lanjut Diagnosis
                    </button>
                @endif
                <button type="button" class="btn btn-warning m-r-3" wire:loading.attr="disabled"
                    onclick="window.location.href='/klinik/pemeriksaanawal'">
                    <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                    Data
                </button>
            </form>
        </div>
    </div>

    <x-alert />
</div>
