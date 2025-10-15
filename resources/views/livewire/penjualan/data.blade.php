<div>
    @section('title', 'Penjualan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Apotek</li>
        <li class="breadcrumb-item">Penjualan</li>
        <li class="breadcrumb-item active">Data</li>
    @endsection

    <h1 class="page-header">Penjualan <small>Data</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading">
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input class="form-control" type="date" wire:model.lazy="tanggal1" />&nbsp;
                    <input class="form-control" type="date" wire:model.lazy="tanggal2" />&nbsp;
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari" placeholder="Cari">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>ID</th>
                        <th>Waktu Bayar</th>
                        <th>Keterangan</th>
                        <th>Barang</th>
                        <th class="text-end">Total Harga Barang</th>
                        <th class="text-end">Diskon</th>
                        <th class="text-end">Total Tagihan</th>
                        <th>Pembayaran</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($data as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-nowrap w-100px">{{ $row->id }}</td>
                            <td class="text-nowrap w-100px">{{ $row->created_at }}</td>
                            <td>{{ $row->keterangan }}</td>
                            <td>
                                <ul class="mb-0 ps-3">
                                    @foreach ($row->stokKeluar as $subRow)
                                        <li>
                                            {{ $subRow->barang->nama ?? '-' }} ({{ $subRow->qty }}
                                            {{ $subRow->barangSatuan->nama }} x {{ number_format($subRow->harga, 2) }})
                                        </li>
                                    @endforeach
                                </ul>
                            </td>
                            <td class="text-end">
                                {{ number_format($row->total_harga_barang, 2) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($row->diskon, 2) }}
                            </td>
                            <td class="text-end">
                                {{ number_format($row->total_tagihan, 2) }}
                            </td>
                            <td>
                                <ul>
                                    <li>Metode Bayar: {{ $row->metode_bayar }}</li>
                                    <li>Keterangan Pembayaran: {{ $row->keterangan_pembayaran }}</li>
                                    <li>Kasir: {{ $row->pengguna->nama }}</li>
                                </ul>
                            </td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator')
                                    <x-action :row="$row" custom="" :detail="false" :edit="false"
                                        :print="true" :permanentDelete="false" :restore="false" :delete="true" />
                                @endrole
                                @role('supervisor')
                                    @if (substr($row->created_at, 0, 10) == date('Y-m-d'))
                                        <x-action :row="$row" custom="" :detail="false" :edit="false"
                                            :print="true" :permanentDelete="false" :restore="false" :delete="true" />
                                    @else
                                        <x-action :row="$row" custom="" :detail="false" :edit="false"
                                            :print="true" :permanentDelete="false" :restore="false" :delete="false" />
                                    @endif
                                @endrole
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center">Tidak ada data.</td>
                        </tr>
                    @endforelse
                    <tr>
                        <td colspan="7" class="text-end">
                            <strong>Total :</strong>
                        </td>
                        <td class="text-end">
                            <strong>
                                {{ number_format($data->sum('total_tagihan'), 2) }}
                            </strong>
                        </td>
                        <td></td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <x-alert />
    <x-modal.cetak judul='Nota' />
</div>
