<div>
    @section('title', 'Diagnosis')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item active">Inform Consent</li>
    @endsection

    <h1 class="page-header">Inform Consent</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <input class="form-control" type="date" wire:model.lazy="tanggal"
                        max="{{ date('Y-m-d') }}" />&nbsp;
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
                            <td>{{ $row->pasien->id }}</td>
                            <td>{{ $row->pasien->nama }}</td>
                            <td>{{ $row->pasien->nik }}</td>
                            <td>{{ $row->pasien->tanggal_lahir }}</td>
                            <td>{{ $row->pasien->jenis_kelamin }}</td>
                            <td>{{ $row->pasien->alamat }}</td>
                            <td>{{ $row->pasien->no_hp }}</td>
                            <td>{{ $row->catatan }}</td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor|operator')
                                    @if ($row->pembayaran)
                                        <x-action :row="$row"
                                            custom="<li><hr class='dropdown-divider'></li><a href='javascript:;'class='dropdown-item fs-8px'>{{ $row->informConsent->pengguna->nama }}<br>{{ $row->informConsent->updated_at }}</a>"
                                            :detail="false" :edit="false" :information="false" :print="false"
                                            :permanentDelete="false" :restore="false" :delete="false" />
                                    @else
                                        @if ($row->informConsent->status == 1)
                                            @if ($row->informConsent->file == null)
                                                @php
                                                    $custom =
                                                        "<a href='javascript:;' wire:click=\"upload({$row['id']})\" x-on:click=\"setTimeout(() => { $('#modal-upload-inform-consent').modal('show'); }, 100);\" class='dropdown-item'>Upload Inform Consent</a><li><hr class='dropdown-divider'></li><a href='javascript:;'class='dropdown-item fs-8px'>" .
                                                        $row->informConsent->pengguna->nama .
                                                        '<br>' .
                                                        $row->informConsent->updated_at .
                                                        '</a>';
                                                @endphp
                                                <x-action :row="$row" :custom="$custom" :detail="false"
                                                    :edit="false" :information="false" :print="true"
                                                    :permanentDelete="false" :restore="false" :delete="true" />
                                            @else
                                                <x-action :row="$row"
                                                    custom="<li><hr class='dropdown-divider'></li><a href='javascript:;'class='dropdown-item fs-8px'>{{ $row->informConsent->pengguna->nama }}<br>{{ $row->informConsent->updated_at }}</a>"
                                                    :detail="false" :edit="false" :information="false"
                                                    :print="false" :permanentDelete="false" :restore="false"
                                                    :delete="true" />
                                            @endif
                                        @else
                                            <x-action :row="$row"
                                                custom="<li><hr class='dropdown-divider'></li><a href='javascript:;'class='dropdown-item fs-8px'>{{ $row->informConsent->pengguna->nama }}<br>{{ $row->informConsent->updated_at }}</a>"
                                                :detail="false" :edit="false" :information="false" :print="false"
                                                :permanentDelete="false" :restore="false" :delete="true" />
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
    <x-alert />
    <x-modal.cetak judul='Inform Consent' />
    <div wire:ignore.self class="modal fade" id="modal-upload-inform-consent">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <form wire:submit="submitUploadInformConsent">
                    <div class="modal-body overflow-auto height-500">
                        <div class="form-group">
                            <label for="file">File</label>
                            <input type="file" class="form-control" wire:model="file">
                            @error('file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="button" class="btn btn-primary" wire:click="submitUploadInformConsent">Submit</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
