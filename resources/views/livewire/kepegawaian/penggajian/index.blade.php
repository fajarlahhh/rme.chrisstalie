<div>
    @section('title', 'Penggajian')

    @section('breadcrumb')
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item active">Penggajian</li>
    @endsection

    <h1 class="page-header">Penggajian </h1>

    <div class="panel panel-inverse" data-sortable-id="table-basic-2">
        <div class="panel-heading">
            <div class="row w-100">
                <div class="col-md-2">
                    @unlessrole('guest')
                        <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                            class="btn btn-outline-secondary btn-block">Tambah</a>
                    @endunlessrole
                </div>
                <div class="col-md-10">
                    <div class="panel-heading-btn float-end">
                        <div class="input-group w-100">
                            <input class="form-control w-auto" type="month" autocomplete="off"
                                wire:model.lazy="bulan" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <x-alert />
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Periode</th>
                            <th>Tanggal Bayar</th>
                            <th>Pegawai</th>
                            <th>Detail</th>
                            <th>Metode Bayar</th>
                            <th>Total</th>
                            <th>No. Jurnal</th>
                            @unlessrole('guest')
                                <th class="w-5px"></th>
                            @endunlessrole
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $i => $row)
                            <tr>
                                <td class=" w-5px">
                                    {{ ++$i }}
                                </td>
                                <td nowrap>{{ substr($row->periode, 0, 7) }}</td>
                                <td>{{ $row->tanggal }}</td>
                                <td>{{ $row->kepegawaianPegawai?->nama }}</td>
                                <td>
                                    @if ($row->kepegawaian_pegawai_id)
                                        <table class="table table-bordered fs-10px">
                                            <thead>
                                                <tr>
                                                    <th class="p-1">Kode Akun</th>
                                                    <th class="p-1">Nilai</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($row->detail as $item)
                                                    <tr>
                                                        <td class="p-1" nowrap>{{ $item['kode_akun_id'] ?? null }}
                                                        </td>
                                                        <td class="text-end p-1" nowrap>
                                                            {{ number_format($item['debet'] ?? 0) }}</td>
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @else
                                        @php
                                            $unsurGaji = collect($data->pluck('detail')->flatten(1))
                                                ->sortByDesc(fn($p) => count($p['pegawai_unsur_gaji']))
                                                ->first()['pegawai_unsur_gaji'];
                                        @endphp
                                        <table class="table table-bordered fs-10px">
                                            <thead>
                                                <tr class="bg-gray-100">
                                                    <th rowspan="2" class="p-1">Pegawai</th>
                                                    <th class="p-1" colspan="{{ collect($unsurGaji)->count() }}">
                                                        Unsur Gaji</th>
                                                </tr>
                                                <tr class="bg-gray-100">
                                                    @foreach ($unsurGaji as $subRow)
                                                        <th class="p-1">{{ $subRow['kode_akun_nama'] ?? null }}
                                                        </th>
                                                    @endforeach
                                                </tr>
                                            </thead>
                                            <tbody>
                                                @foreach ($row->detail as $item)
                                                    <tr>
                                                        <td class="p-1" nowrap>{{ $item['nama'] }}</td>
                                                        @foreach ($item['pegawai_unsur_gaji'] as $subRow)
                                                            <td class="text-end p-1">
                                                                {{ number_format($subRow['nilai'] ?? 0) }}</td>
                                                        @endforeach
                                                    </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    @endif
                                </td>
                                <td nowrap>{{ $row->kode_akun_pembayaran_id }} -
                                    {{ $row->kodeAkunPembayaran->nama ?? null }}
                                </td>
                                <td class="text-end">
                                    @if ($row->kepegawaian_pegawai_id)
                                        {{ number_format(collect($row->detail)->sum('debet')) }}
                                    @else
                                        {{ number_format(collect($row->detail)->sum(fn($q) => collect($q['pegawai_unsur_gaji'])->sum('nilai'))) }}
                                    @endif
                                </td>
                                <td><a href="/jurnalkeuangan?bulan={{ substr($row->keuanganJurnal?->tanggal, 0, 7) }}&cari={{ $row->keuanganJurnal?->nomor }}"
                                        target="_blank">{{ $row->keuanganJurnal?->nomor }}</a></td>
                                <td class="text-end text-nowrap">
                                    @unlessrole('guest')
                                        @if ($row->keuanganJurnal?->waktu_tutup_buku)
                                            <x-action :row="$row" custom="" :detail="false" :edit="false"
                                                :print="false" :permanentdelete="false" :restore="false" :delete="false" />
                                        @else
                                            <x-action :row="$row" custom="" :detail="false" :edit="false"
                                                :print="false" :permanentdelete="false" :restore="false"
                                                :delete="true" />
                                        @endif
                                    @endunlessrole
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div wire:loading>
        <x-loading />
    </div>
</div>
