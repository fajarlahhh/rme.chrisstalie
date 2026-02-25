<div>
    @section('title', 'Pelunasan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item active">Pelunasan</li>
    @endsection

    <h1 class="page-header">Pelunasan</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            @role('administrator|supervisor')
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                    class="btn btn-outline-secondary btn-block">Tambah</a>&nbsp;
            @endrole
            <div class="ms-auto d-flex align-items-center">
                <input type="month" class="form-control w-auto" wire:model.lazy="bulan" min="2025-11"
                    max="{{ date('Y-m') }}">
                &nbsp;
                <input type="text" class="form-control w-auto" placeholder="Cari" autocomplete="off"
                    wire:model.lazy="cari">
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>Data Tagihan</th>
                        <th>Tanggal Bayar</th>
                        <th>Catatan</th>
                        <th>Total Bayar</th>
                        <th>Metode Pembayaran</th>
                        <th>Bukti</th>
                        <th>No. Jurnal</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td nowrap class="fs-11px">
                                <ul>
                                    @foreach ($row->pengadaanPelunasanDetail as $subRow)
                                        <li>No. Tagihan : {{ $subRow->pengadaanTagihan->no_faktur }},
                                             <strong>Rp.
                                                {{ number_format($subRow->tagihan) }}</strong></li>
                                    @endforeach
                                </ul>
                            </td>
                            <td nowrap>{{ $row->tanggal }}</td>
                            <td>{{ $row->catatan }}</td>
                            <td>{{ number_format($row->jumlah) }}</td>
                            <td>{{ $row->kodeAkunPembayaran->nama }}</td>
                            <td>{{ $row->bukti }}</td>
                            <td><a href="/jurnalkeuangan?bulan={{ substr($row->keuanganJurnal?->tanggal, 0, 7) }}&cari={{ $row->keuanganJurnal?->nomor }}"
                                    target="_blank">{{ $row->keuanganJurnal?->nomor }}</a></td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator')
                                    @if ($row->keuanganJurnal->waktu_tutup_buku)
                                        <x-action :row="$row" custom="" :detail="false" :edit="false"
                                            :print="false" :permanentdelete="false" :restore="false" :delete="false" />
                                    @else
                                        <x-action :row="$row" custom="" :detail="false" :edit="false"
                                            :print="false" :permanentdelete="false" :restore="false" :delete="true" />
                                    @endif
                                @endrole
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="panel-footer">
            {{ $data->links() }}
        </div>
    </div>

    <div wire:loading>
        <x-loading />
    </div>
</div>
