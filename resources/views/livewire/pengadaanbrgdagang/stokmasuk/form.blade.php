<div>
    @section('title', 'Stok Masuk')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Stok Masuk</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Stok Masuk <small>Tambah</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Pembelian</label>
                    <select data-container="body" class="form-control" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    })"
                        wire:model.live="pembelian_id" data-width="100%" required>
                        <option selected value="" hidden>-- Tidak Ada Permintaan Pembelian --</option>
                        @foreach ($dataPembelian as $row)
                            <option value="{{ $row['id'] }}" data-subtext="{{ $row['tanggal'] }}">
                                {{ $row['uraian'] }} (Supplier : {{ $row['supplier']['nama'] }})
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="note alert-secondary mb-0">
                    <div class="note-content table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Barang/Item</th>
                                    <th class="w-150px">Satuan</th>
                                    <th class="w-100px">Qty</th>
                                    <th class="w-100px">Qty Masuk</th>
                                    <th class="w-150px">No. Batch</th>
                                    <th class="w-150px">Tanggal Kedaluarsa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barang as $index => $row)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $row['nama'] }}"
                                                disabled autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $row['satuan'] }}"
                                                disabled autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="0" step="1"
                                                value="{{ $row['qty'] }}" disabled autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="0" max="{{ $row['qty'] }}" step="1"
                                                wire:model="barang.{{ $index }}.qty_masuk"
                                                autocomplete="off">
                                            @error('barang.' . $index . '.qty_masuk')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" min="0" step="1"
                                                wire:model="barang.{{ $index }}.no_batch"
                                                autocomplete="off">
                                            @error('barang.' . $index . '.no_batch')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="date" class="form-control" min="0" step="1"
                                                wire:model="barang.{{ $index }}.tanggal_kedaluarsa"
                                                autocomplete="off">
                                            @error('barang.' . $index . '.tanggal_kedaluarsa')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="panel-footer" wire:loading.remove wire:target="submit">
                @unlessrole('guest')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endunlessrole
                <a href="/pengadaanbrgdagang/stokmasuk" class="btn btn-danger" wire:ignore>Kembali</a>
            </div>
        </form>
    </div>

    <x-alert />
</div>
