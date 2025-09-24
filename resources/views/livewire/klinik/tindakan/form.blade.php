<div>
    @section('title', 'Input Tindakan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item">Tindakan</li>
        <li class="breadcrumb-item active">Input</li>
    @endsection

    @section('css')<style>
            body {
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                background-color: #f4f7f6;
                color: #333;
                line-height: 1.6;
                margin: 0;
                padding: 20px;
            }

            .form-container {
                max-width: 800px;
                margin: 20px auto;
                padding: 25px;
                background-color: #ffffff;
                border-radius: 8px;
                box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            }

            h1 {
                text-align: center;
                color: #2c3e50;
                margin-bottom: 25px;
                font-size: 1.8em;
            }

            fieldset {
                border: 1px solid #ddd;
                border-radius: 5px;
                padding: 20px;
                margin-bottom: 20px;
            }

            legend {
                font-weight: 600;
                color: #3498db;
                padding: 0 10px;
                font-size: 1.2em;
            }

            .form-group {
                margin-bottom: 15px;
            }

            label {
                display: block;
                margin-bottom: 5px;
                font-weight: 500;
            }

            input[type="text"],
            input[type="date"],
            input[type="datetime-local"],
            textarea {
                width: 100%;
                padding: 10px;
                border: 1px solid #ccc;
                border-radius: 4px;
                box-sizing: border-box;
                font-size: 1em;
            }

            textarea {
                resize: vertical;
                min-height: 80px;
            }

            .radio-group label,
            .checkbox-group label {
                font-weight: normal;
                display: inline-block;
                margin-right: 15px;
            }

            .body-diagram-container {
                /* border: 2px dashed #ccc; */
                /* Tidak perlu border putus-putus lagi karena sudah ada gambar */
                padding: 10px;
                text-align: center;
                margin-top: 10px;
                border-radius: 5px;
                background-color: #fafafa;
                display: flex;
                /* Untuk menata gambar secara berdampingan */
                justify-content: space-around;
                /* Memberi jarak antar gambar */
                flex-wrap: wrap;
                /* Agar gambar bisa wrap ke bawah di layar kecil */
                gap: 15px;
                /* Jarak antar gambar */
            }

            .body-diagram-container img {
                max-width: 48%;
                /* Agar dua gambar bisa berdampingan */
                height: auto;
                border: 1px solid #eee;
                /* Sedikit border untuk gambar */
                border-radius: 4px;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
                flex-grow: 1;
                /* Agar gambar bisa sedikit membesar jika ada ruang */
                min-width: 250px;
                /* Lebar minimum agar tidak terlalu kecil */
            }

            @media (max-width: 600px) {
                .body-diagram-container img {
                    max-width: 100%;
                    /* Satu gambar per baris di layar kecil */
                }
            }

            .submit-btn {
                display: block;
                width: 100%;
                padding: 12px;
                background-color: #3498db;
                color: #ffffff;
                border: none;
                border-radius: 5px;
                font-size: 1.1em;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }

            .submit-btn:hover {
                background-color: #2980b9;
            }
        </style>
    @endsection

    <h1 class="page-header">Tindakan <small>Input</small></h1>

    <x-alert />

    <form wire:submit.prevent="submit">
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
        <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
            <div class="panel-heading ui-sortable-handle">
                <h4 class="panel-title">Form</h4>
            </div>
            <div class="panel-body">
                <table class="table table-borderless p-0">
                    <tr>
                        <td class="p-0">
                            @foreach ($tindakan as $index => $row)
                                <div class="border p-3 position-relative @if ($index > 0) mt-3 @endif">
                                    @if ($index > 0)
                                        <button type="button" class="btn btn-danger btn-xs position-absolute"
                                            style="top: 5px; right: 5px; z-index: 10;"
                                            wire:click="hapusTindakan({{ $index }})"
                                            wire:loading.attr="disabled">
                                            &nbsp;x&nbsp;
                                        </button>
                                    @endif
                                    <div class="mb-3">
                                        <label class="form-label">Tindakan {{ $index + 1 }}</label>
                                        <select data-container="body" class="form-control" x-init="$($el).selectpicker({
                                            liveSearch: true,
                                            width: 'auto',
                                            size: 10,
                                            container: 'body',
                                            style: '',
                                            showSubtext: true,
                                            styleBase: 'form-control'
                                        })"
                                            wire:model="tindakan.{{ $index }}.id" data-width="100%">
                                            <option value="" selected hidden>-- Pilih Tindakan --
                                            </option>
                                            @foreach ($dataTindakan as $tindakan)
                                                <option value="{{ $tindakan['id'] }}">
                                                    {{ $tindakan['nama'] }} (Rp.
                                                    {{ number_format($tindakan['biaya_total'], 0, ',', '.') }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('tindakan.' . $index . '.id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                    <div class="mb-3">
                                        <label class="form-label">Deskripsi Singkat</label>
                                        <textarea class="form-control" wire:model="tindakan.{{ $index }}.deskripsi"></textarea>
                                    </div>
                                    <div class="form-check form-switch mb-3">
                                        <input class="form-check-input" type="checkbox" id="membutuhkan_inform_consent"
                                            wire:model.live="tindakan.{{ $index }}.membutuhkan_inform_consent">
                                        <label class="form-check-label" for="membutuhkan_inform_consent">
                                            Butuh Informed Consent</label>
                                    </div>
                                    <hr>
                                    @if ($row['membutuhkan_inform_consent'])
                                        <div class="p-3 bg-light border rounded">
                                            <div class="mb-3">
                                                <label for="tujuan_manfaat" class="form-label">Tujuan & Manfaat <span
                                                        class="text-danger">*</span></label>
                                                <textarea id="tujuan_manfaat" class="form-control" wire:model="tindakan.{{ $index }}.tujuan_manfaat"
                                                    rows="2"></textarea>
                                                @error('tindakan.' . $index . '.tujuan_manfaat')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="risiko_komplikasi" class="form-label">Risiko & Komplikasi
                                                    <span class="text-danger">*</span></label>
                                                <textarea id="risiko_komplikasi" class="form-control" wire:model="tindakan.{{ $index }}.risiko_komplikasi"
                                                    rows="2"></textarea>
                                                @error('tindakan.' . $index . '.risiko_komplikasi')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="alternatif_risiko" class="form-label">Alternatif & Risikonya
                                                    <span class="text-danger">*</span></label>
                                                <textarea id="alternatif_risiko" class="form-control" wire:model="tindakan.{{ $index }}.alternatif_risiko"
                                                    rows="2"></textarea>
                                                @error('tindakan.' . $index . '.alternatif_risiko')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                            <div class="mb-3">
                                                <label for="prognosis" class="form-label">Prognosis <span
                                                        class="text-danger">*</span></label>
                                                <textarea id="prognosis" class="form-control" wire:model="tindakan.{{ $index }}.prognosis" rows="2"></textarea>
                                                @error('tindakan.' . $index . '.prognosis')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </div>
                                    @endif

                                </div>
                            @endforeach
                        </td>
                    </tr>
                    <tr>
                        <td class="text-center">
                            <button type="button" class="btn btn-primary btn-sm" wire:click="tambahTindakan"
                                wire:loading.attr="disabled">
                                <span wire:loading class="spinner-border spinner-border-sm"></span>
                                Tambah Tindakan Lainnya
                            </button>
                        </td>
                    </tr>
                </table>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <button type="button" class="btn btn-warning m-r-3" wire:loading.attr="disabled"
                    onclick="window.location.href='/klinik/tindakan'">
                    <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                    Data
                </button>
            </div>
        </div>
    </form>
    <x-alert />
</div>
