<div>
    @section('title', 'Resep Obat')

    @section('breadcrumb')
        <li class="breadcrumb-item ">Klinik</li>
        <li class="breadcrumb-item active">Resep Obat</li>
    @endsection

    <h1 class="page-header">Resep Obat</h1>

    <x-alert />
    <div class="note alert-primary mb-2">
        <div class="note-content">
            <h5>Data Pasien</h5>
            <hr>
            <table class="w-100">
                <tr>
                    <td class="w-200px">No. Registrasi</td>
                    <td class="w-10px">:</td>
                    <td>{{ $data->urutan }}</td>
                </tr>
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
    <form wire:submit.prevent="submit">
        <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
            <!-- begin panel-heading -->
            <div class="panel-heading ui-sortable-handle">
                <h4 class="panel-title">Form</h4>
            </div>
            <div class="panel-body">
                @foreach ($resep as $x => $rsp)
                    <table class="table table-bordered bg-gray-100">
                        <thead>
                            <tr>
                                <td colspan="3">
                                    <h5>Resep {{ $x + 1 }}</h5>
                                </td>
                                <td class="text-end">
                                    @if ($x > 0)
                                        <button type="button" class="btn btn-danger btn-xs "
                                            wire:click="hapusResep({{ $x }})" wire:loading.attr="disabled">
                                            &nbsp;x&nbsp;
                                        </button>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <th>Barang</th>
                                <th class="w-150px">Satuan</th>
                                <th class="w-100px">Qty</th>
                                <th class="w-5px">
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($rsp['barang'] as $y => $row)
                                <tr>
                                    <td>
                                        <select class="form-control" x-init="$($el).selectpicker({
                                            liveSearch: true,
                                            width: 'auto',
                                            size: 10,
                                            container: 'body',
                                            style: '',
                                            showSubtext: true,
                                            styleBase: 'form-control'
                                        })"
                                            wire:model.lazy="resep.{{ $x }}.barang.{{ $y }}.id">
                                            <option value="">-- Pilih Barang --</option>
                                            @foreach ($dataBarang as $subRow)
                                                <option value="{{ $subRow['id'] }}"
                                                    data-subtext="{{ $subRow['kategori'] }}">
                                                    {{ $subRow['nama'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('resep.' . $x . '.barang.' . $y . '.id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <select class="form-control"
                                            wire:model="resep.{{ $x }}.barang.{{ $y }}.barang_satuan_id">
                                            <option value="">-- Pilih Satuan --</option>
                                            @foreach ($row['barangSatuan'] as $subRow)
                                                <option value="{{ $subRow['id'] }}"
                                                    data-subtext="{{ $subRow['konversi_satuan'] }}">
                                                    {{ $subRow['nama'] }} (Rp.
                                                    {{ number_format($subRow['harga_jual']) }})
                                                </option>
                                            @endforeach
                                        </select>
                                        @error('resep.' . $x . '.barang.' . $y . '.barang_satuan_id')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <input type="number" class="form-control" min="0" step="1"
                                            min="0"
                                            wire:model="resep.{{ $x }}.barang.{{ $y }}.qty"
                                            autocomplete="off">
                                        @error('resep.' . $x . '.barang.' . $y . '.qty')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </td>
                                    <td>
                                        <a href="javascript:;" class="btn btn-warning"
                                            wire:click="hapusBarang({{ $x }}, {{ $y }})">
                                            <i class="fa fa-times"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            <tr>
                                <td colspan="4">
                                    <div class="text-center">
                                        <button class="btn btn-secondary" type="button"
                                            wire:click="tambahBarang({{ $x }})"
                                            wire:loading.attr="disabled">
                                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                                            Tambah
                                            Barang</button>
                                        <br>
                                        @error('resep.' . $x . '.barang')
                                            <span class="text-danger">{{ $message }}</span>
                                        @enderror
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="4">
                                    <textarea class="form-control" wire:model="resep.{{ $x }}.catatan" placeholder="Catatan"></textarea>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                @endforeach
                <div class="text-center">
                    <button type="button" class="btn btn-info" wire:click="tambahResep" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Tambah Resep
                    </button>
                    @error('resep')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <a href="/penjualan/data" class="btn btn-warning">Data</a>
            </div>
        </div>
    </form>
    <x-modal.cetak judul='Nota' />
</div>
