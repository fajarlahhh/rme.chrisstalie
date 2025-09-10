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
                            <label class="form-label">Kategori</label>
                            <select class="form-control" wire:model.live="kategori" data-width="100%">
                                <option hidden selected>-- Pilih Jenis Barang --</option>
                                <option value="Medis">Medis</option>
                                <option value="Non Medis">Non Medis</option>
                            </select>
                            @error('kategori')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ICD 10 CM</label>
                            <input class="form-control" type="text" wire:model="icd_10_cm" />
                            @error('icd_10_cm')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Kode Akun</label>
                            <select class="form-control" wire:model.live="kode_akun_id" data-width="100%">
                                <option hidden selected>-- Pilih Kode Akun --</option>
                                @foreach ($dataKodeAkun as $item)
                                    <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                                @endforeach
                            </select>
                            @error('kode_akun_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="alert alert-secondary">
                            <h4>Biaya Alat Bahan</h4>
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Barang</th>
                                        <th class="w-150px">Satuan</th>
                                        <th class="w-100px">Qty</th>
                                        <th class="w-150px">Sub Total</th>
                                        <th class="w-5px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($alatBahan as $index => $row)
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
                                                            {{ $subRow['harga_jual'] }} / {{ $subRow['nama'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('alatBahan.' . $index . '.barang_satuan_id')
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
                                                    value="{{ number_format((int) ($row['harga'] ?? 0) * (int) ($row['qty'] ?? 0)) }}"
                                                    disabled autocomplete="off">
                                            </td>
                                            <td class="with-btn">
                                                <a href="javascript:;" class="btn btn-danger"
                                                    wire:click="hapusAlatBahan({{ $index }})">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th colspan="3" class="text-end align-middle">Total Biaya Alat & Bahan</th">
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
                                        <td colspan="4">
                                            <div class="text-center">
                                                <a class="btn btn-secondary" href="javascript:;"
                                                    wire:click="tambahAlatBahan">Tambah
                                                    Alat Bahan</a>
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
                        <div class="alert alert-info">
                            <h4>Biaya</h4>
                            <hr>
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
                            <div class="mb-3">
                                <label class="form-label">Biaya Keuntungan Klinik</label>
                                <input class="form-control" type="number" step="1" min="0"
                                    wire:model.live="biaya_keuntungan_klinik" />
                                @error('biaya_keuntungan_klinik')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <hr>
                            <div class="mb-3">
                                <label class="form-label">Tarif</label>
                                <input class="form-control" type="text"
                                    value="{{ number_format(
                                        ($biaya_jasa_dokter === '' ? 0 : $biaya_jasa_dokter ?? 0) +
                                            ($biaya_jasa_perawat === '' ? 0 : $biaya_jasa_perawat ?? 0) +
                                            ($biaya_tidak_langsung === '' ? 0 : $biaya_tidak_langsung ?? 0) +
                                            ($biaya_alat_bahan === '' ? 0 : $biaya_alat_bahan ?? 0) +
                                            ($biaya_keuntungan_klinik === '' ? 0 : $biaya_keuntungan_klinik ?? 0),
                                    ) }}"
                                    disabled />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <input wire:loading.remove type="submit" value="Simpan" class="btn btn-success" />
                @endrole
                <a href="{{ $previous }}" class="btn btn-danger" wire:ignore wire:loading.remove >Batal</a>
            </div>
        </form>
    </div>

    <x-alert />

</div>
