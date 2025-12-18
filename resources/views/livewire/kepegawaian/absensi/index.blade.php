<div>
    @section('title', 'Absensi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item active">Absensi</li>
    @endsection

    <!-- BEGIN page-header -->
    <h1 class="page-header">Absensi</h1>
    <!-- END page-header -->

    @php
        $connected = false;
        $output = null;
        $return_var = null;

        // Only check if the function exists for security and hosting compatibility
        if (function_exists('exec')) {
            $pingCommand = (stripos(PHP_OS, 'WIN') === 0)
                ? 'ping -n 1 192.168.110.36'
                : 'ping -c 1 192.168.110.36';
            @exec($pingCommand, $output, $return_var);
            $connected = ($return_var === 0);
        }
    @endphp

    @if ($connected)
        <div class="alert alert-success">
            Terhubung ke 192.168.110.36
        </div>
    @else
        <div class="alert alert-danger">
            Tidak terhubung ke 192.168.110.36
        </div>
    @endif
    
    <div class="panel panel-inverse" data-sortable-id="table-basic-2">
        <!-- BEGIN panel-heading -->
        <div class="panel-heading">
            <div class="row w-100">
                <div class="col-md-2">
                    @unlessrole(config('app.name') . '-guest')
                        <a href="javascript:;" wire:click="download" class="btn btn-outline-secondary btn-block"
                            wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                            Download
                        </a>
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
                            <th>Masuk</th>
                            <th>Pulang</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($data as $row)
                            <tr @if ($row->izin) class="table-warning" @endif>
                                <td class=" w-5px">
                                    {{ ($data->currentpage() - 1) * $data->perpage() + $loop->index + 1 }}
                                </td>
                                <td>{{ $row->tanggal }}</td>
                                <td>{{ $row->pegawai->nama }}</td>
                                <td>{{ $row->izin ? $row->izin . ' (' . $row->keterangan . ')' : null }}</td>
                                <td>{{ $row->masuk }}</td>
                                <td>{{ $row->pulang }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <!-- END table-responsive -->
        </div>
        <!-- END panel-body -->
        <div class="panel-footer" wire:loading.remove>
            {{ $data->links() }}
        </div>
    </div>
    <x-alert />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
