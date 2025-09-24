<div>
    @section('title', 'Input Informed Consent')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item">Informed Consent</li>
        <li class="breadcrumb-item active">Input</li>
    @endsection

    <h1 class="page-header">Informed Consent <small>Input</small></h1>

    <x-alert />

    @if ($data)
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
    @endif
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        @if ($data)
            <form wire:submit.prevent="submit">
                <div class="panel-body">
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
                    <p>Setelah mendapatkan penjelasan yang lengkap, saya menyatakan telah mengerti
                        dan dengan sadar membuat keputusan:</p>
                    <div class="d-flex justify-content-center my-4">
                        <div class="form-check form-check-inline">
                            <input class="form-check-input @error('status') is-invalid @enderror" type="radio"
                                name="status" id="menyetujui" value="menyetujui" wire:model.live="status">
                            <label class="form-check-label fs-5" for="menyetujui">
                                MENYETUJUI
                            </label>
                        </div>
                        <div class="form-check form-check-inline ms-5">
                            <input class="form-check-input @error('status') is-invalid @enderror" type="radio"
                                name="status" id="menolak" value="menolak" wire:model.live="status">
                            <label class="form-check-label fs-5 text-danger" for="menolak">
                                MENOLAK
                            </label>
                        </div>
                    </div>
                    @error('status')
                        <div class="text-danger text-center mb-3">{{ $message }}</div>
                    @enderror

                    @if ($status === 'menyetujui')
                        <div class="mt-4 p-3 bg-light rounded border">
                            <h5 class="mb-3 border-bottom pb-2">TANDA TANGAN</h5>
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="ttd_pasien" class="form-label">Tanda Tangan
                                        Pasien/Wali</label>
                                    <input type="text" id="ttd_pasien" class="form-control form-control-lg"
                                        placeholder="Ketik nama lengkap Anda di sini" wire:model="ttd_pasien">
                                    @error('ttd_pasien')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="ttd_saksi" class="form-label">Nama Saksi
                                        (Perawat/Staf)</label>
                                    <input type="text" id="ttd_saksi" class="form-control form-control-lg"
                                        placeholder="Ketik nama lengkap saksi" wire:model="ttd_saksi">
                                    @error('ttd_saksi')
                                        <div class="text-danger">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="panel-footer">
                    @role('administrator|supervisor|operator')
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                            <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                            Simpan
                        </button>
                    @endrole
                    <button type="button" class="btn btn-warning m-r-3" wire:loading.attr="disabled"
                        onclick="window.location.href='/klinik/informedconsent/data'">
                        <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                        Data
                    </button>
                    <button type="button" class="btn btn-secondary m-r-3"
                        onclick="window.location.href='/klinik/informedconsent'" wire:loading.attr="disabled">
                        <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                        Reset
                    </button>
                </div>
            </form>
        @else
            <div class="panel-body">
                <div class="row">
                    <div class="form-group">
                        <label class="form-label">Cari Data Registrasi</label>
                        <select class="form-control" x-init="$($el).selectpicker({
                            liveSearch: true,
                            width: 'auto',
                            size: 10,
                            container: 'body',
                            style: '',
                            showSubtext: true,
                            styleBase: 'form-control'
                        })" wire:model.live="registrasi_id"
                            data-width="100%" @if (isset($updating) && $updating === 'registrasi_id') disabled @endif
                            wire:loading.attr="disabled" wire:target="registrasi_id">
                            <option selected value="">-- Pilih Data Registrasi --</option>
                            @foreach ($dataRegistrasi as $row)
                                <option value="{{ $row->id }}">
                                    {{ $row->pasien_id }} - {{ $row->pasien->nama }}
                                </option>
                            @endforeach
                        </select>
                        @error('registrasi_id')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                <button type="button" class="btn btn-warning m-r-3" wire:loading.attr="disabled"
                    onclick="window.location.href='/klinik/informedconsent/data'">
                    <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                    Data
                </button>
            </div>
        @endif
    </div>
    <x-alert />
</div>
