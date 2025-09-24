<div>
    @section('title', 'Diagnosis')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item active">Informed Consent</li>
    @endsection

    <h1 class="page-header">Informed Consent</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <div class="w-100">
                <div class="panel-heading-btn float-end">
                    <select data-container="body" class="form-control" wire:model.lazy="status">
                        <option value="1">Belum Proses</option>
                        <option value="2">Sudah Proses</option>
                    </select>&nbsp;
                    @if ($status == 2)
                        <input class="form-control" type="date" wire:model.lazy="tanggal"
                            max="{{ date('Y-m-d') }}" />&nbsp;
                    @endif
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
                            <td>{{ $row->pasien->tanggal_lahir->format('d-m-Y') }}</td>
                            <td>{{ $row->pasien->jenis_kelamin }}</td>
                            <td>{{ $row->pasien->alamat }}</td>
                            <td>{{ $row->pasien->no_hp }}</td>
                            <td>{{ $row->catatan }}</td>
                            <td class="with-btn-group text-end" nowrap>
                                @role('administrator|supervisor|operator')
                                    @if ($row->pembayaran)
                                        @php
                                            $custom =
                                                "<a href='" .
                                                Storage::url($row->informedConsent->file) .
                                                "' target='_blank' class='dropdown-item'>File Informed Consent</a><li><hr class='dropdown-divider'></li><a href='javascript:;'class='dropdown-item fs-8px'>" .
                                                $row->informedConsent->pengguna->nama .
                                                '<br>' .
                                                $row->informedConsent->updated_at .
                                                '</a>';
                                        @endphp
                                        <x-action :row="$row" :custom="$custom" :detail="false" :edit="false"
                                            :information="false" :print="false" :permanentDelete="false" :restore="false"
                                            :delete="false" />
                                    @else
                                        @if ($row->informedConsent->status == 1)
                                            @if ($row->informedConsent->file == null)
                                                @php
                                                    $custom =
                                                        "<a href='javascript:;' wire:click=\"uploadInformedConsent({$row['id']})\" x-on:click=\"setTimeout(() => { $('#modal-upload-inform-consent').modal('show'); }, 100);\" class='dropdown-item'>Upload Informed Consent</a><li><hr class='dropdown-divider'></li><a href='javascript:;'class='dropdown-item fs-8px'>" .
                                                        $row->informedConsent->pengguna->nama .
                                                        '<br>' .
                                                        $row->informedConsent->updated_at .
                                                        '</a>';
                                                @endphp
                                                <x-action :row="$row" :custom="$custom" :detail="false"
                                                    :edit="false" :information="false" :print="true"
                                                    :permanentDelete="false" :restore="false" :delete="true" />
                                            @else
                                                @php
                                                    $custom =
                                                        "<a href='javascript:;' wire:click=\"deleteInformedConsent({$row['id']})\" class='dropdown-item'>Hapus Informed Consent</a><a href='" .
                                                        Storage::url($row->informedConsent->file) .
                                                        "' target='_blank' class='dropdown-item'>File Informed Consent</a><li><hr class='dropdown-divider'></li><a href='javascript:;'class='dropdown-item fs-8px'>" .
                                                        $row->informedConsent->pengguna->nama .
                                                        '<br>' .
                                                        $row->informedConsent->updated_at .
                                                        '</a>';
                                                @endphp
                                                <x-action :row="$row" :custom="$custom" :detail="false"
                                                    :edit="false" :information="false" :print="false"
                                                    :permanentDelete="false" :restore="false" :delete="false" />
                                            @endif
                                        @else
                                            <x-action :row="$row"
                                                custom="<li><hr class='dropdown-divider'></li><a href='javascript:;'class='dropdown-item fs-8px'>{{ $row->informedConsent->pengguna->nama }}<br>{{ $row->informedConsent->updated_at }}</a>"
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
    <x-modal.cetak judul='Informed Consent' />
    <div wire:ignore.self class="modal fade" id="modal-upload-inform-consent">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form wire:submit="submitInformedConsent({{ $dataInformConsent ? $dataInformConsent->id : null }})">
                    <div class="modal-body overflow-auto height-500">
                        @if ($dataInformConsent)
                            <div class="note alert-primary mb-2">
                                <div class="note-content">
                                    <h5>Data Pasien</h5>
                                    <hr>
                                    <table class="w-100">
                                        <tr>
                                            <td class="w-200px">No. RM</td>
                                            <td class="w-10px">:</td>
                                            <td>{{ $dataInformConsent->registrasi->pasien_id }}</td>
                                        </tr>
                                        <tr>
                                            <td>Nama</td>
                                            <td class="w-10px">:</td>
                                            <td>{{ $dataInformConsent->registrasi->pasien->nama }}</td>
                                        </tr>
                                        <tr>
                                            <td>Usia</td>
                                            <td class="w-10px">:</td>
                                            <td>{{ $dataInformConsent->registrasi->pasien->umur }} Tahun</td>
                                        </tr>
                                        <tr>
                                            <td>Jenis Kelamin</td>
                                            <td class="w-10px">:</td>
                                            <td>{{ $dataInformConsent->registrasi->pasien->jenis_kelamin }}</td>
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="file">File Informed Consent</label>
                            @if ($file && is_object($file) && method_exists($file, 'temporaryUrl'))
                                <img src="{{ $file->temporaryUrl() }}" class="w-100">
                            @endif
                            <input type="file" class="form-control" accept="image/*" wire:model="file">
                            @error('file')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                            <span wire:loading wire:target="submit" class="spinner-border spinner-border-sm"></span>
                            Simpan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
