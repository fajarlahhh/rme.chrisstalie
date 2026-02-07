<div>
    @section('title', 'Persetujuan Pemesanan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Persetujuan Pemesanan</li>
        <li class="breadcrumb-item active">Form</li>
    @endsection

    <h1 class="page-header">Persetujuan Pemesanan <small>Pengadaan Barang Dagang</small></h1>

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
                        <textarea class="form-control" disabled>{{ $data->pengadaanPermintaan->deskripsi }}</textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input class="form-control" type="text" value="{{ $data->pengadaanPermintaan->created_at }}"
                            disabled />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Operator</label>
                        <input class="form-control" type="text"
                            value="{{ $data->pengadaanPermintaan->pengguna?->nama }}" disabled />
                    </div>
                    <hr>
                    <h4 class="alert-heading">Data Pemesanan</h4>
                    <div class="mb-3">
                        <label class="form-label">Tanggal</label>
                        <input class="form-control" type="text" value="{{ $data->tanggal }}" disabled />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Supplier</label>
                        <input class="form-control" type="text" value="{{ $data->supplier?->nama }}" disabled />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Catatan</label>
                        <input class="form-control" type="text" value="{{ $data->catatan }}" disabled />
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Operator</label>
                        <input class="form-control" type="text" value="{{ $data->pengguna?->nama }}" disabled />
                    </div>
                </div>
                <div class="note alert-secondary mb-3">
                    <div class="note-content table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Barang</th>
                                    <th class="w-200px">Satuan</th>
                                    <th class="w-200px">Harga Satuan</th>
                                    <th class="w-100px">Qty</th>
                                    <th class="w-200px">Harga Beli</th>
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
                                            <input type="text" class="form-control" value="{{ $row['satuan'] }}"
                                                autocomplete="off" disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control w-200px text-end"
                                                value="{{ number_format($row['harga_beli']) }}" autocomplete="off"
                                                disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control w-100px text-end"
                                                value="{{ number_format($row['qty']) }}" autocomplete="off" disabled>
                                        </td>
                                        <td>
                                            <input type="text" class="form-control w-200px text-end"
                                                value="{{ number_format($row['harga_beli'] * $row['qty']) }}"
                                                autocomplete="off" disabled>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="4" class="text-end">Total Harga Barang</th>
                                    <td colspan="2">
                                        <input type="text" class="form-control text-end"
                                            value="{{ number_format($data->pengadaanPemesananDetail->sum(fn($q) => $q->harga_beli * $q->qty)) }}"
                                            disabled>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    @if (auth()->user()->kepegawaian_pegawai_id)
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                            Buat SP
                        </button>
                    @endif
                @endrole
                <button type="button"
                    onclick="window.location.href='/manajemenstok/pengadaanbrgdagang/persetujuanpemesanan'"
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
