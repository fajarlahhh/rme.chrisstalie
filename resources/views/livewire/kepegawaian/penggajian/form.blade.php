<div>
    @section('title', 'Tambah Penggajian')

    @section('breadcrumb')
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item">Penggajian</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Penggajian <small>Tambah</small></h1>

    <x-alert />

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input class="form-control" type="date" wire:model="tanggal" />
                    @error('tanggal')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Pegawai</label>
                    <select data-container="body" class="form-control" wire:model.lazy="pegawai_id" data-width="100%">
                        <option selected value="">-- Bukan Pegawai --</option>
                        @foreach ($dataPegawai as $item)
                            <option value="{{ $item['id'] }}">
                                {{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('pegawai_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                @if ($pegawai_id)
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="w-5px">No.</th>
                                <th>Unsur Gaji</th>
                                <th>Nilai</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($unsurGaji as $index => $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td> {{ $item['unsur_gaji_nama'] }}
                                    </td>
                                    <td> <input class="form-control text-end" type="text"
                                            value="{{ number_format($item['nilai']) }}" disabled />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="2">Total</th>
                                <th> <input class="form-control text-end" type="text"
                                        value="{{ number_format(collect($unsurGaji)->sum('nilai')) }}" disabled />
                                </th>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="mb-3">
                        <label class="form-label">Metode Bayar</label>
                        <select class="form-control" wire:model="metode_bayar">
                            <option selected hidden>-- Pilih Metode Bayar --</option>
                            @foreach ($dataKodeAkun as $item)
                                <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                            @endforeach
                        </select>
                    </div>
                @endif
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <button type="button" onclick="window.location.href='kepegawaian/penggajian'" class="btn btn-danger"
                    wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
            </div>
        </form>
    </div>
</div>
