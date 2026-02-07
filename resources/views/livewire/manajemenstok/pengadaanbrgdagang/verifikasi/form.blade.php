<div>
    @section('title', 'Verifikasi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Verifikasi</li>
        <li class="breadcrumb-item active">Form</li>
    @endsection

    <h1 class="page-header">Verifikasi <small>Pengadaan Barang Dagang</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <x-alert />
                <div class="alert alert-info">
                    <h4 class="alert-heading">Data Permintaan</h4>
                    <div class="mb-3">
                        <label class="form-label">Deskripsi</label>
                        <textarea class="form-control" disabled>{{ $data->deskripsi }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input class="form-control" type="text" value="{{ $data->created_at }}" disabled />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Operator</label>
                        <input class="form-control" type="text" value="{{ $data->pengguna?->nama }}" disabled />
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status Verifikasi</label>
                    <select class="form-control" wire:model.live="status">
                        <option value="Disetujui">Setuju</option>
                        <option value="Ditolak">Tolak</option>
                    </select>
                    @error('status')
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
                                    <th class="w-100px">Qty</th>
                                    <th class="w-100px">Qty Disetujui</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($barang as $index => $row)
                                    <tr>
                                        <td>
                                            <input type="text" class="form-control" value="{{ $row['nama'] }}"
                                                autocomplete="off" disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control"
                                                value="{{ $row['satuan'] }}" autocomplete="off" disabled>
                                            @error('barang.' . $index . '.satuan')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control w-100px" min="0"
                                                step="1" min="0" max="100"
                                                value="{{ $row['qty'] }}" autocomplete="off" disabled>
                                            @error('barang.' . $index . '.qty')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                        <td>
                                            <input type="number" class="form-control w-200px" min="0"
                                                step="1" min="0" max="{{ $row['qty'] }}"
                                                wire:model="barang.{{ $index }}.qty_disetujui" autocomplete="off"
                                                @if ($status == 'Ditolak') disabled @endif>
                                            @error('barang.' . $index . '.qty_disetujui')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                @if ($status == 'Ditolak')
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <textarea class="form-control" wire:model="catatan"></textarea>
                        @error('catatan')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                @endif
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <button type="button" onclick="window.location.href='/manajemenstok/pengadaanbrgdagang/verifikasi'"
                    class="btn btn-danger" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
            </div>
        </form>
    </div>

    <x-alert />

    <x-modal.cetak judul='Nota' />

    <div wire:loading>
        <x-loading />
    </div>
</div>
