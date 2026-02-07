<div>
    @section('title', 'Izin')

    @section('breadcrumb')
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item active">Izin</li>
    @endsection

    <h1 class="page-header">Izin</h1>

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
                            <input class="form-control w-auto" type="date" autocomplete="off"
                                wire:model="tanggal1" />
                            <input class="form-control w-auto" type="date" autocomplete="off"
                                wire:model="tanggal2" />
                            <input type="text" class="form-control w-auto" placeholder="Cari" aria-label="Cari"
                                wire:model="cari" aria-describedby="button-addon2">
                            <button class="btn btn-primary" type="button" wire:click="$commit">Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- END panel-heading -->
        <!-- BEGIN panel-body -->
        <div class="panel-body">
            <!-- BEGIN table-responsive -->
            <div class="table-responsive">
                <table class="table table-hover mb-0 text-dark">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Tanggal</th>
                            <th>Nama</th>
                            <th>Izin</th>
                            <th>Keterangan</th>
                            <th class="w-10px"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                            <tr>
                                <td class=" w-5px">
                                    {{ ($data->currentpage() - 1) * $data->perpage() + $loop->index + 1 }}
                                </td>
                                <td>
                                    {{ $row->tanggal }}
                                </td>
                                <td>{{ $row->kepegawaianPegawai->nama }}</td>
                                <td>{{ $row->izin }}</td>
                                <td>{{ $row->keterangan }}</td>
                                <td class="text-end  text-nowrap">
                                    @unlessrole('guest')
                                        <x-action :row="$row" custom="" :detail="false" :edit="false"
                                            :print="false" :permanentdelete="false" :restore="false" :delete="true" />
                                    @endunlessrole
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- END table-responsive -->
        </div>
        <!-- END panel-body -->
        <div class="panel-footer">
            {{ $data->links() }}
        </div>
    </div>

    <x-alert />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
