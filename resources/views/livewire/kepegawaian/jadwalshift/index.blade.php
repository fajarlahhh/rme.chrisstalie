<div>
    @section('title', 'Jadwal Shift')

    @section('breadcrumb')
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item active">Jadwal Shift</li>
    @endsection

    <h1 class="page-header">Jadwal Shift </h1>

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
                            <select class="form-control w-auto" wire:model.lazy="pegawai_id">
                                <option value="">-- Pilih Pegawai --</option>
                                @foreach ($dataPegawai as $pegawai)
                                    <option value="{{ $pegawai['id'] }}">{{ $pegawai['nama'] }}</option>
                                @endforeach
                            </select>
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
                            <th>Tanggal</th>
                            <th>Jam Masuk</th>
                            <th>Jam Pulang</th>
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
                                <td>{{ $row->tanggal }}</td>
                                <td>{{ $row->jam_masuk }}</td>
                                <td>{{ $row->jam_pulang }}</td>
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
