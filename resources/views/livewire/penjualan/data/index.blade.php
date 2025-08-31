<div>
    @section('title', 'Penjualan Data')

    @section('breadcrumb')
        <li class="breadcrumb-item">Penjualan</li>
        <li class="breadcrumb-item active">Data</li>
    @endsection

    <h1 class="page-header">Penjualan Data</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input class="form-control" type="date" wire:model.lazy="date" />&nbsp;
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>Date</th>
                        <th>Deskripsi</th>
                        <th>Keterangan Bayar</th>
                        <th>Barang</th>
                        <th>Pasien</th>
                        <th>Total</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>
                                {{ $index + 1 }}
                            </td>
                            <td>{{ $row->date }}</td>
                            <td>{{ $row->uraian }}</td>
                            <td>{{ $row->kasir_description }}</td>
                            <td>
                                <ul>
                                    @foreach ($row->saleDetail as $subRow)
                                        <li>{{ $subRow->goods->nama }} ({{ $subRow->qty }})</li>
                                    @endforeach
                                </ul>
                            </td>
                            <td>{{ $row->pasien?->rm }} - {{ $row->pasien?->nama }}</td>
                            <td class="text-end">
                                {{ number_format($row->saleDetail->sum(fn($q) => $q->qty * ($q->harga - ($q->harga * $q->discount) / 100)), 2) }}
                            </td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor')
                                    @if ($row->kasir_id == null)
                                        <x-action :row="$row" custom="" :detail="false" :edit="false"
                                            :print="true" :permanentDelete="false" :restore="false" :delete="true" />
                                    @endif
                                @endrole
                            </td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="7" class="text-end">
                            <strong>Total :
                                {{ number_format($data->sum(fn($q) => $q->saleDetail->sum(fn($q) => $q->qty * ($q->harga - ($q->harga * $q->discount) / 100))), 2) }}</strong>
                        </td>
                        <td></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
    <x-alert />
    <x-modal.cetak judul='Nota' />
</div>
