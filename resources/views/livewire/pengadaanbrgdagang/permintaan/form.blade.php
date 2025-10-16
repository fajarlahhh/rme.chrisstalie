<div>
    @section('title', 'Permintaan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Permintaan</li>
        <li class="breadcrumb-item active">Form</li>
    @endsection

    <h1 class="page-header">Permintaan</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea class="form-control" wire:model="deskripsi"></textarea>
                    @error('deskripsi')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="note alert-secondary mb-3">
                    <div class="note-content table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th class="w-200px">Satuan</th>
                                    <th class="w-200px">Qty</th>
                                    <th class="w-5px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barang as $index => $row)
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
                                                wire:model.live="barang.{{ $index }}.id">
                                                <option value="">-- Tidak Ada Barang --</option>
                                                @foreach ($dataBarang as $subRow)
                                                    <option value="{{ $subRow['id'] }}">
                                                        {{ $subRow['nama'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('barang.' . $index . '.id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
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
                                                wire:model="barang.{{ $index }}.satuan">
                                                <option value="">-- Tidak Ada Satuan --</option>
                                                @foreach ($row['barangSatuan'] as $subRow)
                                                    <option value="{{ $subRow['id'] }}" data-subtext="{{ $subRow['konversi_satuan'] }}">
                                                        {{ $subRow['nama'] }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('barang.' . $index . '.satuan')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="0" step="1"
                                                min="1"
                                                wire:model="barang.{{ $index }}.qty" autocomplete="off">
                                            @error('barang.' . $index . '.qty')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <a href="javascript:;" class="btn btn-danger"
                                                wire:click="hapusbarang({{ $index }})">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="4">
                                        <div class="text-center">
                                            <a class="btn btn-secondary" href="javascript:;"
                                                wire:click="tambahBarang">Tambah
                                                Barang</a>
                                            <br>
                                            @error('barang')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kirim Ke Verifikator</label>
                    <select class="form-control" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    })" wire:model="verifikator_id">
                        <option value="">-- Tidak Ada Verifikator --</option>
                        @foreach ($dataPengguna as $subRow)
                            <option value="{{ $subRow['id'] }}">
                                {{ $subRow['nama'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('verifikator_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="panel-footer" wire:loading.remove wire:target="submit">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <a href="{{ $previous }}" class="btn btn-danger" wire:ignore wire:loading.remove >Batal</a>
            </div>
        </form>
    </div>
    <x-alert />
    <x-modal.cetak judul='Nota' />
</div>
