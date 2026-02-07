<div>
    @section('title', 'Penambahan Opname')

    @section('breadcrumb')
        <li class="breadcrumb-item">Manajemen Stok</li>
        <li class="breadcrumb-item">Opname</li>
        <li class="breadcrumb-item">Penambahan</li>
        <li class="breadcrumb-item active">Data</li>
    @endsection

    <h1 class="page-header">Penambahan <small>Opname</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-heading">
            @role('administrator|supervisor')
                <a href="/manajemenstok/opname/penambahan/form" class="btn btn-primary">
                    Tambah</a>
            @endrole
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input class="form-control" type="month" wire:model.lazy="bulan" min="2025-09"
                        max="{{ date('Y-m') }}" />&nbsp;
                    <input type="text" class="form-control w-200px" placeholder="Cari"
                        aria-label="Sizing example input" autocomplete="off" aria-describedby="basic-addon2"
                        wire:model.lazy="cari" placeholder="Cari">
                </div>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>Tanggal</th>
                        <th>Catatan</th>
                        <th>Barang</th>
                        <th>Qty</th>
                        <th>Harga Beli</th>
                        <th>No. Batch</th>
                        <th>Tgl. Kedaluarsa</th>
                        <th>No. Jurnal</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td class="text-nowrap w-100px">{{ $row->created_at }}</td>
                            <td class="text-nowrap w-100px">{{ $row->catatan }}</td>
                            <td class="text-nowrap w-100px">{{ $row->barang->nama }}</td>
                            <td class="text-nowrap w-100px">{{ $row->qty }}</td>
                            <td class="text-nowrap w-100px">{{ $row->harga_beli }}</td>
                            <td class="text-nowrap w-100px">{{ $row->no_batch }}</td>
                            <td class="text-nowrap w-100px">{{ $row->tanggal_kedaluarsa }}</td>
                            <td class="text-nowrap w-100px"><a
                                    href="/jurnalkeuangan?bulan={{ substr($row->created_at, 0, 7) }}&cari={{ $row->keuanganJurnal?->nomor }}"
                                    target="_blank">{{ $row->keuanganJurnal?->nomor }}</a></td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor')
                                    @if ($row->keuanganJurnal->waktu_tutup_buku)
                                        <x-action :row="$row" :detail="false" :edit="false" :print="false"
                                            :permanentdelete="false" :restore="false" :delete="false" />
                                    @else
                                        @if ($row->keluar->count() == 0)
                                            <x-action :row="$row" :detail="false" :edit="false"
                                                :print="false" :permanentdelete="false" :restore="false"
                                                :delete="true" />
                                        @else
                                            <x-action :row="$row" :detail="false" :edit="false"
                                                :print="false" :permanentdelete="false" :restore="false"
                                                :delete="false" />
                                        @endif
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
