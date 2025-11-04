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
                            <input class="form-control w-auto" type="month" autocomplete="off"
                                wire:model.lazy="bulan" />
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
                            <th>Uraian</th>
                            <th>Tanggal</th>
                            <th>Rincian</th>
                            @unlessrole(config('app.name') . '-guest')
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
                                <td>{{ $row->uraian }}</td>
                                <td>{{ $row->tanggal }}</td>
                                <td>
                                    @foreach ($row->jurnalDetail->where('debet', '>', 0) as $j => $subRow)
                                        <div class="d-flex justify-content-between">
                                            <span>{{ $subRow->kodeAkun?->nama }}</span>
                                            <span>{{ number_format($subRow->debet) }}</span>
                                        </div>
                                    @endforeach
                                </td>
                                @unlessrole(config('app.name') . '-guest')
                                    <td class="text-end text-nowrap">
                                        <x-action :row="$row" custom="" :detail="false" :edit="false"
                                            :print="false" :permanentDelete="false" :restore="false" :delete="true" />
                                    </td>
                                @endunlessrole
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <x-alert />
</div>
