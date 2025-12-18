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
                    @unlessrole(config('app.name') . '-guest')
                        <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                            class="btn btn-outline-secondary btn-block">Tambah</a>
                    @endunlessrole
                </div>
                <div class="col-md-10">
                    <div class="panel-heading-btn float-end">
                        <div class="input-group w-100">
                            <input class="form-control w-auto" type="number" autocomplete="off"
                                wire:model.lazy="tahun" />
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Periode</th>
                            <th>Tanggal Bayar</th>
                            <th>Detail</th>
                            <th>Metode Bayar</th>
                            <th>Total</th>
                            @unlessrole(config('app.name') . '-guest')
                                <th class="w-5px"></th>
                            @endunlessrole
                        </tr>
                    </thead>
                    <tbody>
                        @if ($data->count() > 0)
                            @php
                                $unsurGaji = collect($data->pluck('detail')->flatten(1))
                                    ->sortByDesc(fn($p) => count($p['pegawai_unsur_gaji']))
                                    ->first()['pegawai_unsur_gaji'];
                            @endphp
                            @foreach ($data as $i => $row)
                                <tr>
                                    <td class=" w-5px">
                                        {{ ++$i }}
                                    </td>
                                    <td nowrap>{{ substr($row->periode, 0, 7) }}</td>
                                    <td>{{ $row->tanggal }}</td>
                                    <td>
                                        <table class="table table-bordered fs-10px">
                                            <thead>
                                                <tr class="bg-gray-100">
                                                    <th rowspan="2" class="p-1">Pegawai</th>
                                                    <th class="p-1" colspan="{{ collect($unsurGaji)->count() }}">Unsur Gaji</th>
                                                </tr>
                                                <tr class="bg-gray-100">
                                                    @foreach ($unsurGaji as $subRow)
                                                        <th class="p-1">{{ $subRow['kode_akun_nama'] ?? null }}</th>
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
                                    </td>
                                    <td nowrap>{{ $row->kode_akun_pembayaran_id }} <br> {{ $row->kodeAkunPembayaran->nama ?? null }}
                                    </td>
                                    <td class="text-end">
                                        {{ number_format(collect($row->detail)->sum(fn($q) => collect($q['pegawai_unsur_gaji'])->sum('nilai'))) }}
                                    </td>
                                    @unlessrole(config('app.name') . '-guest')
                                        <td class="text-end text-nowrap">
                                            <x-action :row="$row" custom="" :detail="false" :edit="false"
                                                :print="false" :permanentDelete="false" :restore="false" :delete="true" />
                                        </td>
                                    @endunlessrole
                                </tr>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="5" class="text-center">Tidak ada data</td>
                            </tr>
                        @endif
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <x-alert />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
