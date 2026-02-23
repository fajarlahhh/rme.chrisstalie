<div>
    @section('title', 'Jurnal Keuangan')

    @section('breadcrumb')
        <li class="breadcrumb-item active">Jurnal Keuangan</li>
    @endsection

    <h1 class="page-header">Jurnal Keuangan</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            @role('administrator|supervisor|operator')
                <div class="btn-group my-n1">
                    <a class="btn btn-outline-secondary btn-block"
                        href="javascript:window.location.href=window.location.href.split('?')[0] + '/form?jenis=jurnalumum'"
                        @role('operator|guest') disabled @endrole>Umum</a>
                    <button type="button" class="btn btn-outline-secondary btn-block dropdown-toggle"
                        data-bs-toggle="dropdown"><b class="caret"></b></button>
                    <div class="dropdown-menu dropdown-menu-start">
                        <a class="dropdown-item"
                            href="javascript:window.location.href=window.location.href.split('?')[0] + '/form?jenis=pengeluaran'">Pengeluaran</a>
                        @role('administrator|supervisor')
                            <a class="dropdown-item"
                                href="javascript:window.location.href=window.location.href.split('?')[0] + '/form?jenis=pindahsaldokas'">Pindah
                                Saldo Kas</a>
                        @endrole
                    </div>
                </div>
                &nbsp;
            @endrole
            <select class="form-control w-auto" wire:model.lazy="jenis">
                <option value="">Semua Jenis</option>
                @foreach ($dataJenis as $item)
                    <option value="{{ $item['jenis'] }}">{{ $item['jenis'] }}</option>
                @endforeach
            </select>
            &nbsp;
            <input type="month" class="form-control w-auto" wire:model.lazy="bulan" max="{{ date('Y-m') }}">
            &nbsp;
            <input type="text" class="form-control w-200px" placeholder="Cari" aria-label="Sizing example input"
                autocomplete="off" aria-describedby="basic-addon2" wire:model.lazy="cari">
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>ID</th>
                        <th>No. Registrasi</th>
                        <th>Jenis</th>
                        <th>Tanggal</th>
                        <th>Uraian</th>
                        <th>Detail</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>{{ ($data->currentPage() - 1) * $data->perPage() + $loop->iteration }}</td>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->nomor }}</td>
                            <td>{{ $row->jenis }}</td>
                            <td>{{ $row->tanggal }}</td>
                            <td>{{ $row->uraian }}</td>
                            <td class="w-400px">
                                <table class="table table-bordered fs-10px">
                                    <tr class="bg-gray-100">
                                        <th class="text-nowrap ">Kode Akun</th>
                                        <th class="text-end w-100px p-1">Debet</th>
                                        <th class="text-end w-100px p-1">Kredit</th>
                                    </tr>
                                    @foreach ($row->keuanganJurnalDetail as $j => $subRow)
                                        <tr>
                                            <td class="p-1  text-nowrap">
                                                {{ $subRow->kode_akun_id . ' - ' . $subRow->kodeAkun?->nama }}
                                            </td>
                                            <td class="text-end p-1  text-nowrap">
                                                {{ number_format($subRow->debet, 2) }}</td>
                                            <td class="text-end p-1  text-nowrap">
                                                {{ number_format($subRow->kredit, 2) }}</td>
                                        </tr>
                                    @endforeach
                                    <tr>
                                        <th class="p-1">Total</th>
                                        <th class="p-1 text-end">
                                            {{ number_format($row->keuanganJurnalDetail->sum('debet'), 2) }}</th>
                                        <th class="p-1 text-end">
                                            {{ number_format($row->keuanganJurnalDetail->sum('kredit'), 2) }}</th>
                                    </tr>
                                </table>
                            </td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor|operator')
                                    @if ($row->waktu_tutup_buku)
                                        <x-action :row="$row" custom="" :detail="false" :edit="false"
                                            :print="false" :permanentdelete="false" :restore="false" :delete="false" />
                                    @else
                                        @if ($row->system)
                                            <x-action :row="$row" custom="" :detail="false" :edit="false"
                                                :print="false" :permanentdelete="false" :restore="false"
                                                :delete="false" />
                                        @else
                                            <x-action :row="$row" custom="" :detail="false"
                                                :edit="true" :print="false" :permanentdelete="false" :restore="false"
                                                :delete="true" />
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
