<div>
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Tarif Tindakan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item">Tarif Tindakan</li>
        <li class="breadcrumb-item active">{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Tarif Tindakan <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-4">
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input class="form-control" type="text" wire:model="nama" />
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ICD 9 CM</label>
                            <input class="form-control" type="text" wire:model="icd_9_cm" />
                            @error('icd_9_cm')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tarif</label>
                            <input class="form-control" type="number" step="1" min="0"
                                wire:model.live="tarif" />
                            @error('tarif')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kategori</label>
                            <select class="form-control" wire:model.live="kode_akun_id" data-width="100%">
                                <option hidden selected>-- Pilih Kode Akun --</option>
                                @foreach ($dataKodeAkun as $item)
                                    <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('kode_akun_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="alert alert-secondary">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Alat</th>
                                        <th class="w-100px">Qty</th>
                                        <th class="w-150px">Sub Total</th>
                                        <th class="w-5px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (collect($alatBahan)->where('jenis', 'Alat') as $index => $row)
                                        <tr>
                                            <td class="with-btn">
                                                <select class="form-control" x-init="$($el).selectpicker({
                                                    liveSearch: true,
                                                    width: 'auto',
                                                    size: 10,
                                                    container: 'body',
                                                    style: '',
                                                    showSubtext: true,
                                                    styleBase: 'form-control'
                                                })"
                                                    wire:model.live="alatBahan.{{ $index }}.id">
                                                    <option value="">-- Pilih Alat --</option>
                                                    @foreach ($dataAset as $subRow)
                                                        <option value="{{ $subRow['id'] }}">
                                                            {{ $subRow['nama'] }} @if ($subRow['metode_penyusutan'] == 'Satuan Hasil Produksi')
                                                                (Rp.
                                                                {{ number_format($subRow['harga_perolehan'] / $subRow['masa_manfaat']) }})
                                                            @endif
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('alatBahan.' . $index . '.id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="with-btn">
                                                <input type="number" class="form-control" min="0" step="1"
                                                    min="0" wire:model.live="alatBahan.{{ $index }}.qty"
                                                    autocomplete="off">
                                                @error('alatBahan.' . $index . '.qty')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="with-btn">
                                                <input type="text" class="form-control text-end"
                                                    value="{{ number_format((int) ($row['harga_jual'] ?? 0) * (int) ($row['qty'] ?? 0)) }}"
                                                    disabled autocomplete="off">
                                            </td>
                                            <td class="with-btn">
                                                <button type="button" class="btn btn-danger"
                                                    wire:click="hapusAlatBahan({{ $index }})"
                                                    wire:loading.attr="disabled">
                                                    <span wire:loading>
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                    </span>
                                                    <span wire:loading.remove>
                                                        <i class="fa fa-times"></i>
                                                    </span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4">
                                            <div class="text-center">
                                                <button type="button" class="btn btn-secondary"
                                                    wire:click="tambahAlatBahan('Alat')" wire:loading.attr="disabled">
                                                    <span wire:loading>
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                    </span>
                                                    <span wire:loading.remove>
                                                        Tambah Alat
                                                    </span>
                                                </button>
                                                <br>
                                                @error('alatBahan')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="alert alert-secondary">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th> Bahan</th>
                                        <th class="w-150px">Satuan</th>
                                        <th class="w-100px">Qty</th>
                                        <th class="w-150px">Sub Total</th>
                                        <th class="w-5px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach (collect($alatBahan)->where('jenis', 'Bahan') as $index => $row)
                                        <tr>
                                            <td class="with-btn">
                                                <select class="form-control" x-init="$($el).selectpicker({
                                                    liveSearch: true,
                                                    width: 'auto',
                                                    size: 10,
                                                    container: 'body',
                                                    style: '',
                                                    showSubtext: true,
                                                    styleBase: 'form-control'
                                                })"
                                                    wire:model.live="alatBahan.{{ $index }}.id">
                                                    <option value="">-- Pilih Barang --</option>
                                                    @foreach ($dataBarang as $subRow)
                                                        <option value="{{ $subRow['id'] }}">
                                                            {{ $subRow['nama'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('alatBahan.' . $index . '.id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="with-btn">
                                                <select class="form-control"
                                                    wire:model.live="alatBahan.{{ $index }}.barang_satuan_id">
                                                    <option value="">-- Pilih Satuan --</option>
                                                    @foreach ($row['barangSatuan'] as $subRow)
                                                        <option value="{{ $subRow['id'] }}"
                                                            data-subtext="{{ $subRow['konversi_satuan'] }}">
                                                            {{ $subRow['nama'] }} (Rp.
                                                            {{ number_format($subRow['harga_jual']) }})
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('alatBahan.' . $index . '.barang_satuan_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="with-btn">
                                                <input type="number" class="form-control" min="0"
                                                    step="1" min="0"
                                                    wire:model.live="alatBahan.{{ $index }}.qty"
                                                    autocomplete="off">
                                                @error('alatBahan.' . $index . '.qty')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="with-btn">
                                                <input type="text" class="form-control text-end"
                                                    value="{{ number_format((int) ($row['harga_jual'] ?? 0) * (int) ($row['qty'] ?? 0)) }}"
                                                    disabled autocomplete="off">
                                            </td>
                                            <td class="with-btn">
                                                <button type="button" class="btn btn-danger"
                                                    wire:click="hapusAlatBahan({{ $index }})"
                                                    wire:loading.attr="disabled">
                                                    <span wire:loading>
                                                        <span class="spinner-border spinner-border-sm" role="status"
                                                            aria-hidden="true"></span>
                                                    </span>
                                                    <span wire:loading.remove>
                                                        <i class="fa fa-times"></i>
                                                    </span>
                                                </button>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="3" class="text-end align-middle">Total Biaya Bahan
                                        </th>
                                        <th>
                                            <input type="text" class="form-control text-end"
                                                value="{{ number_format($biaya_alat_bahan) }}" disabled
                                                autocomplete="off">
                                        </th>
                                        <th></th>
                                    </tr>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="4" class="text-center">
                                            <button type="button" class="btn btn-secondary"
                                                wire:click="tambahAlatBahan('Bahan')" wire:loading.attr="disabled">
                                                <span wire:loading>
                                                    <span class="spinner-border spinner-border-sm" role="status"
                                                        aria-hidden="true"></span>
                                                </span>
                                                <span wire:loading.remove>
                                                    Tambah Bahan
                                                </span>
                                            </button>
                                            @error('alatBahan')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                        <div class="alert alert-info">
                            <div class="mb-3">
                                <label class="form-label">Biaya Jasa Dokter</label>
                                <input class="form-control" type="number" step="1" min="0"
                                    wire:model.live="biaya_jasa_dokter" />
                                @error('biaya_jasa_dokter')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Biaya Jasa Perawat</label>
                                <input class="form-control" type="number" step="1" min="0"
                                    wire:model.live="biaya_jasa_perawat" />
                                @error('biaya_jasa_perawat')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Biaya Tidak Langsung</label>
                                <input class="form-control" type="number" step="1" min="0"
                                    wire:model.live="biaya_tidak_langsung" />
                                @error('biaya_tidak_langsung')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Keuntungan</label>
                                <input class="form-control" type="text"
                                    value="{{ number_format(
                                        ($tarif === '' ? 0 : $tarif ?? 0) -
                                            ($biaya_jasa_dokter === '' ? 0 : $biaya_jasa_dokter ?? 0) -
                                            ($biaya_jasa_perawat === '' ? 0 : $biaya_jasa_perawat ?? 0) -
                                            ($biaya_tidak_langsung === '' ? 0 : $biaya_tidak_langsung ?? 0) -
                                            ($biaya_alat_bahan === '' ? 0 : $biaya_alat_bahan ?? 0),
                                    ) }}"
                                    disabled />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <button type="button" onclick="window.location.href='{{ $previous }}'" class="btn btn-danger"
                    wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
            </div>
        </form>
    </div>

    <x-alert />

</div>
