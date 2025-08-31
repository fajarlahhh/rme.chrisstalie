<div>
    @section('title', 'Pembelian')

    @section('breadcrumb')
        <li class="breadcrumb-item">Apotek</li>
        <li class="breadcrumb-item">Pembelian</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Pembelian <small>Tambah</small></h1>

    <x-alert />

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Permintaan Pembelian</label>
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
                        <option selected value="">-- Pilih Permintaan Pembelian --</option>
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
                    <input class="form-control" type="date" wire:model="date" required />
                    @error('date')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Uraian</label>
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
                                <option selected value="" hidden>-- Pilih Supplier --</option>
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
                                <option value="Lunas">Lunas</option>
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
                                    <th>Satuan</th>
                                    <th class="w-100px">Qty</th>
                                    <th class="w-150px">Harga Beli</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barang as $index => $row)
                                    <tr>
                                        <td class="with-btn">
                                            <input type="text" class="form-control" min="0" step="1"
                                                value="{{ $row['nama'] }}" disabled autocomplete="off">
                                        </td>
                                        <td class="with-btn">
                                            <input type="text" class="form-control" min="0" step="1"
                                                value="{{ $row['satuan'] }}" disabled autocomplete="off">
                                        </td>
                                        <td class="with-btn">
                                            <input type="number" class="form-control" min="0" step="1"
                                                value="{{ $row['qty'] }}" disabled autocomplete="off">
                                        </td>
                                        <td class="with-btn">
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
            <div class="panel-footer">
                @unlessrole('guest')
                    <input type="submit" value="Simpan" class="btn btn-success" />
                @endunlessrole
                <a href="{{ $previous }}" class="btn btn-danger" wire:ignore>Batal</a>
            </div>
        </form>
    </div>
</div>
