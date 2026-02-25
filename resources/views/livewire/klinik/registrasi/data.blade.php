<div>
    @section('title', 'Data Pendaftaran')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item">Pendaftaran</li>
        <li class="breadcrumb-item active">Data</li>
    @endsection

    <h1 class="page-header">Pendaftaran <small>Data</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading overflow-auto d-flex">
            <div class="ms-auto d-flex align-items-center">
                <select class="form-control w-auto" wire:model.lazy="status">
                    <option value="1">Belum Bayar</option>
                    <option value="2">Sudah Bayar</option>
                </select>&nbsp;
                @if ($status == 2)
                    <input type="date" class="form-control w-auto" wire:model.lazy="tanggal" />&nbsp;
                @endif
                <input type="text" class="form-control w-auto" placeholder="Cari" autocomplete="off"
                    wire:model.lazy="cari">
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th class="w-10px">No.</th>
                        <th>No. Registrasi</th>
                        <th>RM</th>
                        <th>Nama</th>
                        <th>NIK</th>
                        <th>Tanggal Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>Alamat</th>
                        <th>No. Telp.</th>
                        <th>Catatan</th>
                        <th class="w-10px"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($data as $index => $row)
                        <tr>
                            <td>
                                {{ ($data->currentpage() - 1) * $data->perpage() + $loop->index + 1 }}
                            </td>
                            <td>{{ $row->id }}</td>
                            <td>{{ $row->pasien->id }}</td>
                            <td>{{ $row->pasien->nama }}</td>
                            <td>{{ $row->pasien->nik }}</td>
                            <td>{{ $row->pasien->tanggal_lahir->format('d-m-Y') }}</td>
                            <td>{{ $row->pasien->jenis_kelamin }}</td>
                            <td>{{ $row->pasien->alamat }}</td>
                            <td>{{ $row->pasien->no_hp }}</td>
                            <td>{{ $row->catatan }}</td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor|operator')
                                    @php
                                        $custom = "<a href='javascript:;' wire:click=\"hakKewajiban({$row['id']})\" x-on:click=\"setTimeout(() => { $('#modal-hak-kewajiban').modal('show'); }, 100);\" class='dropdown-item'>Hak dan Kewajiban</a>";
                                    @endphp
                                    @if (!$row->pembayaran)
                                        <x-action :row="$row" :custom="$custom" :detail="false" :edit="false"
                                            :print="false" :permanentdelete="false" :restore="false" :delete="true" />
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
    <div wire:ignore.self class="modal fade" id="modal-hak-kewajiban">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <form wire:submit="submitHakKewajiban">
                    <div class="modal-body overflow-auto height-500">
                        <div class="form-group">
                            <label for="hak_kewajiban">Hak dan Kewajiban</label>
                            <textarea class="form-control" wire:model="hak_kewajiban"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" wire:click="submitHakKewajiban">Submit</button>
                    </div>
                
        <x-modal.konfirmasi />
    </form>
            </div>
        </div>
    </div>
    <div wire:loading>
        <x-loading />
    </div>
</div>
