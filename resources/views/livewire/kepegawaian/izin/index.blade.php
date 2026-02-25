<div>
    @section('title', 'Izin')

    @section('breadcrumb')
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item active">Izin</li>
    @endsection

    <h1 class="page-header">Izin</h1>

    <div class="panel panel-inverse" data-sortable-id="table-basic-2">
        <div class="panel-heading overflow-auto d-flex">
            @unlessrole('guest')
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form'"
                    class="btn btn-outline-secondary btn-block">Tambah</a>&nbsp;
            @endunlessrole
            <div class="ms-auto d-flex align-items-center">
                <input class="form-control w-auto" type="date" autocomplete="off" wire:model="tanggal1" />&nbsp;
                <input class="form-control w-auto" type="date" autocomplete="off" wire:model="tanggal2" />&nbsp;
                <input type="text" class="form-control w-auto" placeholder="Cari" autocomplete="off"
                    wire:model="cari">
                &nbsp;
                <button class="btn btn-primary" type="button" wire:click="$commit">Filter</button>
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

    <div wire:loading>
        <x-loading />
    </div>
</div>
