<div>
    @section('title', 'Jurnal Keuangan')

    @section('breadcrumb')
        <li class="breadcrumb-item active">Jurnal Keuangan</li>
    @endsection

    <h1 class="page-header">Jurnal Keuangan</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            @role('administrator|supervisor|operator')
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                    class="btn btn-primary">
                    Tambah</a>
            @endrole
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input type="month" class="form-control w-auto" wire:model.lazy="bulan" max="{{ date('Y-m') }}">
                    &nbsp;
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
                        <th>ID</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Uraian</th>
                        <th>Unit Bisnis</th>
                        <th>Detail</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->jenis }}</td>
                            <td>{{ $row->tanggal }}</td>
                            <td>{{ $row->uraian }}</td>
                            <td>{{ $row->unit_bisnis }}</td>
                            <td class="w-400px">
                                <table class="table-bordered fs-10px">
                                    <tr class="bg-gray-100">
                                        <th class="text-nowrap ">Kode Akun</th>
                                        <th class="text-end w-100px p-1">Debet</th>
                                        <th class="text-end w-100px p-1">Kredit</th>
                                    </tr>
                                    @foreach ($row->jurnalDetail as $j => $subRow)
                                        <tr>
                                            <td class="p-1  text-nowrap">
                                                {{ $subRow->kode_akun_id . ' - ' . $subRow->kodeAkun?->nama }}
                                            </td>
                                            <td class="text-end p-1  text-nowrap">
                                                {{ number_format($subRow->debet) }}</td>
                                            <td class="text-end p-1  text-nowrap">
                                                {{ number_format($subRow->kredit) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th>Total</th>
                                        <th class="text-end p-1  text-nowrap" colspan="2">
                                            {{ number_format($row->jurnalDetail->sum(fn($q) => $q->debet)) }}
                                        </th>
                                    </tr>
                                </table>
                            </td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor|operator')
                                    @if ($row->referensi_id != null)
                                        <x-action :row="$row" custom="" :detail="false" :edit="false"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="false" />
                                    @else
                                        <x-action :row="$row" custom="" :detail="false" :edit="true"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="true" />
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
    <x-alert />
</div>
