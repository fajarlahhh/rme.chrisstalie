<div>
    @section('title', 'Tidak Hadir')

    @section('breadcrumb')
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item">Izin</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Izin <small>Tambah</small></h1>

    <form wire:submit="submit">
        <div class="panel panel-inverse" data-sortable-id="table-basic-2">
            <div class="panel-heading">
                <h4 class="panel-title">Form</h4>
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-default" data-toggle="panel-expand"><i
                            class="fa fa-expand"></i></a>
                </div>
            </div>
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label" for="pegawai">Pegawai</label>
                    <div wire:ignore>
                        <select wire:model="kepegawaian_pegawai_id" id="kepegawaian_pegawai_id" class="form-control">
                            <option value="" selected hidden>-- Pilih Pegawai --</option>
                            @foreach ($dataPegawai as $row)
                                <option value="{{ $row['id'] }}">
                                    {{ $row['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @error('kepegawaian_pegawai_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="tanggal">Tanggal</label>
                    <input type="date" autocomplete="off" wire:model="tanggal" id="tanggal" class="form-control">
                    @error('tanggal')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="izin">Jenis Izin</label>
                    <select wire:model="izin" id="izin" class="form-control">
                        <option value="">-- Pilih Jenis Izin --</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Izin">Izin</option>
                    </select>
                    @error('izin')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="keterangan">Keterangan</label>
                    <input type="text" autocomplete="off" wire:model="keterangan" id="keterangan"
                        class="form-control">
                    @error('keterangan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="panel-footer">
                @unlessrole(config('app.name') . '-guest')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endunlessrole
                <button type="button" onclick="window.location.href='/kepegawaian/izin'" class="btn btn-danger"
                    wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
                <x-alert />
            </div>
        </div>
    </form>
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
