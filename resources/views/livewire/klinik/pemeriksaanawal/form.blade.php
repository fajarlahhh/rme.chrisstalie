<div>
    @section('title', 'Tambah Pemeriksaan Awal')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pelayanan</li>
        <li class="breadcrumb-item">Pemeriksaan Awal</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Pemeriksaan Awal <small>Tambah</small></h1>

    <x-alert />
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
    <form wire:submit.prevent="submit">
        <div class="tab-content panel rounded-0 p-3 m-0">
            <div class="tab-pane fade active show" id="default-tab-0" role="tabpanel" wire:ignore.self>
                <div class="row">
                    <div class="col-md-4">
                        <div class="note alert-primary mb-2">
                            <!-- BEGIN tab-pane -->
                            <div class="note-content">
                                @if ($data->note)
                                    <div class="mb-3">
                                        <label class="form-label">Catatan</label>
                                        <textarea class="form-control" rows="5" disabled>
                                            {{ $data->catatan }}"
                                        </textarea>
                                    </div>
                                    <hr>
                                @endif
                                <div class="mb-3">
                                    <label class="form-label">No. RM</label>
                                    <input class="form-control" type="text" value="{{ $data->pasien_id }}"
                                        disabled />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No. KTP</label>
                                    <input class="form-control" type="text" value="{{ $data->pasien->nik }}"
                                        disabled />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Nama</label>
                                    <input class="form-control" type="text" value="{{ $data->pasien->nama }}"
                                        disabled />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Alamat</label>
                                    <input class="form-control" type="text" value="{{ $data->pasien->alamat }}"
                                        disabled />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Usia</label>
                                    <input class="form-control" type="text" value="{{ $data->pasien->umur }} Tahun"
                                        disabled />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Jenis Kelamin</label>
                                    <input class="form-control" type="text"
                                        value="{{ $data->pasien->jenis_kelamin }}" disabled />
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">No. Telpon</label>
                                    <input class="form-control" type="text" value="{{ $data->pasien->no_hp }}"
                                        disabled />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="mb-3">
                            <label class="form-label">Keluhan Pasien</label>
                            <textarea class="form-control" wire:model="keluhan" rows="3" required></textarea>
                            @error('keluhan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <hr>
                        <div class="note alert-secondary mb-2">
                            <div class="note-content">
                                <h3>Pemeriksaan Fisik</h3>
                                <div class="row">
                                    @foreach ($pemeriksaanFisik as $key => $row)
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ $key }}</label>
                                                <input class="form-control" type="text"
                                                    wire:model="pemeriksaanFisik.{{ $key }}" />
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                        <div class="note alert-secondary mb-2">
                            <div class="note-content">
                                <h3>Tanda-Tanda Vital</h3>
                                <div class="row">
                                    @foreach ($pemeriksaanTtv as $key => $row)
                                        <div class="col-md-6">
                                            <div class="mb-3">
                                                <label class="form-label">{{ $key }}</label>
                                                @if ($key != 'Fungsi Penciuman' && $key != 'Kesadaran')
                                                    <input class="form-control" type="number" required
                                                        wire:model="pemeriksaanTtv.{{ $key }}" />
                                                @endif
                                                @if ($key == 'Fungsi Penciuman')
                                                    <select data-container="body" class="form-control"
                                                        wire:model="pemeriksaanTtv.{{ $key }}"
                                                        data-width="100%">
                                                        <option value="Normal">Normal</option>
                                                        <option value="Tidak Normal">Tidak Normal</option>
                                                    </select>
                                                @endif
                                                @if ($key == 'Kesadaran')
                                                    <select data-container="body" class="form-control"
                                                        wire:model="pemeriksaanTtv.{{ $key }}"
                                                        data-width="100%">
                                                        <option value="01">Compos mentis</option>
                                                        <option value="02">Somnolence</option>
                                                        <option value="03">Sopor</option>
                                                        <option value="04">Coma</option>
                                                    </select>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="tab-pane fade" id="default-tab-1" role="tabpanel" wire:ignore.self>
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
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Catatan Tambahan / Rekomendasi:</label>
                            <textarea id="catatan" rows="4" wire:model="catatan" class="form-control"
                                placeholder="Tulis catatan observasi_kualitatif lain atau rencana tindak lanjut..."></textarea>
                        </div>
                    </div>
                </div>
                <hr>
                <div wire:loading.remove>
                    @role('administrator|supervisor|operator')
                        <input type="submit" value="Simpan" class="btn btn-success" />
                    @endrole
                    <a href="/klinik/pemeriksaanawal" class="btn btn-warning m-r-3">Data</a>
                </div>
            </div>
        </div>
    </form>
</div>
