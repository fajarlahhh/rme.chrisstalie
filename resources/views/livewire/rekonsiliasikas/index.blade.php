<div>
    @section('title', 'Rekonsiliasi Kas')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Rekonsiliasi Kas</li>
    @endsection

    <h1 class="page-header">Rekonsiliasi Kas</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <select class="form-control" x-init="$($el).select2({
                        width: '100%',
                        dropdownAutoWidth: true
                    });
                    $($el).on('change', function(e) {
                        $wire.set('tanggal', e.target.value);
                    });" wire:model="tanggal" data-width="100%">
                        <option hidden selected>-- Pilih Tanggal --</option>
                        @foreach ($data->unique('tanggal') as $item)
                            <option value="{{ $item['tanggal'] }}">{{ $item['tanggal'] }} </option>
                        @endforeach
                    </select>
                    @error('tanggal')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                @if ($tanggal)
                    <table class="table table-hover table-bordered">
                        <thead>
                            <tr>
                                <th rowspan="2" class="w-10px">No</th>
                                <th rowspan="2">Jenis</th>
                                <th colspan="4">Pembayaran</th>
                            </tr>
                            <tr>
                                <th class="w-200px">Cash<br>
                                    <small>{{ collect($dataMetodeBayar)->where('nama', 'Cash')->first()['kode_akun_id'] }}</small>
                                </th>
                                <th class="w-200px">Transfer BCA<br>
                                    <small>{{ collect($dataMetodeBayar)->where('nama', 'Transfer BCA')->first()['kode_akun_id'] }}</small>
                                </th>
                                <th class="w-200px">QRIS<br>
                                    <small>{{ collect($dataMetodeBayar)->where('nama', 'QRIS')->first()['kode_akun_id'] }}</small>
                                </th>
                                <th class="w-200px">Debit<br>
                                    <small>{{ collect($dataMetodeBayar)->where('nama', 'Debit')->first()['kode_akun_id'] }}</small>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>1</td>
                                <td>Total Penjualan Barang/Obat</td>
                                <td class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'Cash')->sum(fn($q) => $q['total_harga_barang'])) }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'Transfer BCA')->sum(fn($q) => $q['total_harga_barang'])) }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'QRIS')->sum(fn($q) => $q['total_harga_barang'])) }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'Debit')->sum(fn($q) => $q['total_harga_barang'])) }}
                                </td>
                            </tr>
                            <tr>
                                <td>2</td>
                                <td>Total Penjualan Resep</td>
                                <td class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'Cash')->sum(fn($q) => $q['total_resep'])) }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'Transfer BCA')->sum(fn($q) => $q['total_resep'])) }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'QRIS')->sum(fn($q) => $q['total_resep'])) }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'Debit')->sum(fn($q) => $q['total_resep'])) }}
                                </td>
                            </tr>
                            <tr>
                                <td>3</td>
                                <td>Total Penjualan Tindakan</td>
                                <td class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'Cash')->sum(fn($q) => $q['total_tindakan'])) }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'Transfer BCA')->sum(fn($q) => $q['total_tindakan'])) }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'QRIS')->sum(fn($q) => $q['total_tindakan'])) }}
                                </td>
                                <td class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'Debit')->sum(fn($q) => $q['total_tindakan'])) }}
                                </td>
                            </tr>
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                <th class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'Cash')->sum(fn($q) => $q['total_tagihan'])) }}
                                </th>
                                <th class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'Transfer BCA')->sum(fn($q) => $q['total_tagihan'])) }}
                                </th>
                                <th class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'QRIS')->sum(fn($q) => $q['total_tagihan'])) }}
                                </th>
                                <th class="text-end">
                                    {{ number_format($data->where('tanggal', $tanggal)->where('metode_bayar', 'Debit')->sum(fn($q) => $q['total_tagihan'])) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>
                @endif
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Posting
                    </button>
                @endrole
                <x-alert />
            </div>
        </form>
    </div>
</div>
