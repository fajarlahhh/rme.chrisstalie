<div>
    @section('title', 'Jadwal Shift')

    @section('breadcrumb')
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item">Jadwal Shift</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Jadwal Shift <small>Tambah</small></h1>

    <form wire:submit="submit">
        <div class="panel panel-inverse" data-sortable-id="table-basic-2">
            <!-- BEGIN panel-heading -->
            <div class="panel-heading">
                <h4 class="panel-title">Form</h4>
                <div class="panel-heading-btn">
                    <a href="javascript:;" class="btn btn-xs btn-icon btn-default" data-toggle="panel-expand"><i
                            class="fa fa-expand"></i></a>
                </div>
            </div>
            <!-- END panel-heading -->
            <!-- BEGIN panel-body -->
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label" for="pegawai_id">Pegawai</label>
                    <select wire:model="pegawai_id" id="pegawai_id" class="form-control">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach ($dataPegawai as $row)
                            <option value="{{ $row['id'] }}">
                                {{ $row['nama'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('pegawai_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="bulan">Bulan</label>
                    <input type="month" class="form-control" wire:model.live="bulan" id="bulan">
                    @error('bulan')
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
                <div class="note alert-secondary mb-2">
                    <div class="note-content">
                        <h5>Tanggal Shift</h5>
                        @error('detail')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                        <hr>
                        <div class="row">
                            @foreach ($detail as $i => $row)
                                <div class="col-xl-2 col-lg-3 col-md-4">
                                    <div
                                        class="card text-center  @if ($detail[$i]['masuk'] == 1) border-2 border-primary @else border-1 border-secondary @endif mb-3">
                                        <div class="card-body" x-data="{
                                            get isChecked() {
                                                return $wire.detail[{{ $i }}].masuk == 1 || $wire.detail[{{ $i }}].masuk === true;
                                            }
                                        }">
                                            <input class="form-check-input mb-1" type="checkbox" value="1"
                                                wire:model.live="detail.{{ $i }}.masuk"
                                                x-model="$wire.detail[{{ $i }}].masuk" />
                                            <p class="card-text">
                                                {{ \Carbon\Carbon::parse($row['tanggal'])->format('d M Y') }}
                                            </p>
                                            <template x-if="isChecked">
                                                <div>
                                                    <select class="form-control" wire:model="detail.{{ $i }}.shift_id">
                                                        <option value="">-- Pilih Shift --</option>
                                                        @foreach ($dataShift as $row)
                                                            <option value="{{ $row['id'] }}">{{ $row['nama'] }} ({{ $row['jam_masuk'] }} s/d {{ $row['jam_pulang'] }})</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
            <!-- END panel-body -->
            <div class="panel-footer">
                @unlessrole(config('app.name') . '-guest')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endunlessrole
                <a href="/kepegawaian/jadwalshift" class="btn btn-warning">Data</a>
                <x-alert />
            </div>
        </form>
    </div>
</div>
