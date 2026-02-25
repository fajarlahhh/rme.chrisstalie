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
                <div class="mb-3" wire:ignore>
                    <label class="form-label">No. Pemesanan</label>
                    <select class="form-control" x-init="$($el).select2({
                        templateResult: formatState,
                        width: '100%',
                        dropdownAutoWidth: true,
                        placeholder: '-- Cari Pemesanan --'
                    });
                    
                    function formatState(state) {
                        if (!state.id) {
                            return state.text;
                        }
                        return state.text;
                    }
                    $($el).on('change', function(e) {
                        $wire.set('pengadaan_pemesanan_id', e.target.value);
                    });" wire:model="pengadaan_pemesanan_id">
                        <option selected value="" hidden>-- Cari Data Pembelian --</option>
                        @foreach ($dataPemesanan as $row)
                            <option value="{{ $row['id'] }}">
                                {{ $row['nomor'] }}, {{ $row['supplier']['nama'] }}
                            </option>
                        @endforeach
                    </select>
                </div>
                @if ($pengadaan_pemesanan_id)
                    <div class="alert alert-info">
                        <h4>Data Permintaan</h4>
                        <ul>
                            <li>Nomor: {{ $data->pengadaanPermintaan?->nomor }}</li>
                            <li>Deskripsi: {{ $data->pengadaanPermintaan?->deskripsi }}</li>
                            <li>Tanggal: {{ $data->pengadaanPermintaan?->created_at }}</li>
                            <li>Jenis Barang: {{ $data->pengadaanPermintaan?->jenis_barang }}</li>
                        </ul>
                    </div>
                @endif
                <div class="note alert-secondary mb-0">
                    <div class="note-content table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Barang/Item</th>
                                    <th class="w-150px">Satuan</th>
                                    <th class="w-100px">Qty Belum Masuk</th>
                                    <th class="w-100px">Qty Masuk</th>
                                    <th class="w-150px">No. Batch</th>
                                    <th class="w-150px">Tanggal Kedaluarsa</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barang as $index => $brg)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $brg['nama'] }}"
                                                disabled autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $brg['satuan'] }}"
                                                disabled autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="0" step="1"
                                                value="{{ $brg['qty'] }}" disabled autocomplete="off">
                                        </td>
                                        <td>
                                            <input type="number" class="form-control" min="0"
                                                max="{{ $brg['qty'] }}" step="1"
                                                wire:model="barang.{{ $index }}.qty_masuk" autocomplete="off">
                                            @error('barang.' . $index . '.qty_masuk')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                wire:model="barang.{{ $index }}.no_batch" autocomplete="off">
                                            @error('barang.' . $index . '.no_batch')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="date" class="form-control"
                                                min="{{ now()->format('Y-m-d') }}"
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
            <div class="panel-footer">
                @unlessrole('guest')
                    <button type="button" x-init="$($el).on('click', function() {
                        $('#modal-konfirmasi').modal('show');
                    })" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Submit
                    </button>
                @endunlessrole
                <button type="button" onclick="window.location.href='/manajemenstok/pengadaanbrgdagang/stokmasuk'"
                    class="btn btn-danger" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Kembali
                </button>
                <x-alert />
            </div>

            <x-modal.konfirmasi />
        </form>
    </div>

    <div wire:loading>
        <x-loading />
    </div>
</div>
