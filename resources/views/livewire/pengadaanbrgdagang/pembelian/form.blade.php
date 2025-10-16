<div>
    @section('title', 'Pembelian')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Pembelian</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Pembelian <small>Tambah</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Cari Permintaan Pembelian</label>
                    <select data-container="body" class="form-control" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    })"
                        wire:model.live="permintaan_pembelian_id" data-width="100%" required>
                        <option selected value="">-- Tidak Ada Permintaan Pembelian --</option>
                        @foreach ($dataPermintaanPembelian as $row)
                            <option value="{{ $row['id'] }}">
                                {{ $row['deskripsi'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('permintaan_pembelian_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input class="form-control" type="date" wire:model="tanggal" max="{{ now()->format('Y-m-d') }}"
                        required />
                    @error('tanggal')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Uraian/No. Faktur</label>
                    <input class="form-control" type="text" wire:model="uraian" required />
                    @error('uraian')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="note alert-primary mb-2">
                    <div class="note-content">
                        <div class="mb-3">
                            <label class="form-label">Supplier</label>
                            <select data-container="body" class="form-control" x-init="$($el).selectpicker({
                                liveSearch: true,
                                width: 'auto',
                                size: 10,
                                container: 'body',
                                style: '',
                                showSubtext: true,
                                styleBase: 'form-control'
                            })"
                                wire:model="supplier_id" data-width="100%" required>
                                <option selected value="" hidden>-- Tidak Ada Supplier --</option>
                                @foreach ($dataSupplier as $row)
                                    <option value="{{ $row['id'] }}">
                                        {{ $row['nama'] }}
                                    </option>
                                @endforeach
                            </select>
                            @error('supplier_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pembayaran</label>
                            <select data-container="body" class="form-control" wire:model.live="pembayaran"
                                data-width="100%">
                                <option selected value="Jatuh Tempo">Jatuh Tempo</option>
                                @foreach ($dataKodeAkun as $row)
                                    <option value="{{ $row['id'] }}">{{ $row['nama'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        @if ($pembayaran == 'Jatuh Tempo')
                            <div class="mb-3">
                                <label class="form-label">Tanggal Jatuh Tempo</label>
                                <input class="form-control" type="date" wire:model="jatuh_tempo" required />
                                @error('jatuh_tempo')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>
                <div class="note alert-secondary mb-0">
                    <div class="note-content table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Barang/Item</th>
                                    <th class="w-200px">Satuan</th>
                                    <th class="w-150px">Qty</th>
                                    <th class="w-150px">Harga Beli</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barang as $index => $row)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" min="0" step="1"
                                                value="{{ $row['nama'] }}" disabled autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" min="0" step="1"
                                                value="{{ $row['satuan'] }}" disabled autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="0" step="1"
                                                value="{{ $row['qty'] }}" disabled autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="0" step="1"
                                                wire:model.lazy="barang.{{ $index }}.harga_beli"
                                                autocomplete="off">
                                            @error('barang.' . $index . '.harga_beli')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                <br>
                <div class="mb-3">
                    <label class="form-label">Diskon</label>
                    <input class="form-control" type="number" wire:model.lazy="diskon" required />
                    @error('diskon')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">PPN</label>
                    <input class="form-control" type="number" wire:model.lazy="ppn" required />
                    @error('ppn')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Total</label>
                    <input class="form-control" type="text"
                        value="{{ number_format($totalHargaBeli - ($diskon ?: 0) + ($ppn ?: 0)) }}" disabled
                        autocomplete="off" />
                </div>
            </div>
            <div class="panel-footer" wire:loading.remove wire:target="submit">
                @unlessrole('guest')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endunlessrole
                <a href="{{ $previous }}" class="btn btn-danger" wire:ignore wire:loading.remove>Batal</a>
            </div>
        </form>
    </div>
    <x-alert />
</div>
