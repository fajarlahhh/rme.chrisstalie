<div>
    @section('title', 'Jadwal Shift')

    @section('breadcrumb')
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item active">Jadwal Shift</li>
    @endsection

    <h1 class="page-header">Jadwal Shift </h1>

    <form wire:submit.prevent="submit">
        <div class="panel panel-inverse" data-sortable-id="table-basic-2">
            <div class="panel-heading overflow-auto d-flex">
                <h4 class="panel-title">Form</h4>
            </div>
            <div class="panel-body">
                <x-alert />
                <div class="mb-3">
                    <label class="form-label" for="bulan">Bulan</label>
                    <input type="month" class="form-control" wire:model.live="bulan" id="bulan">
                    @error('bulan')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="kepegawaian_pegawai_id">Pegawai</label>
                    <select wire:model.live="kepegawaian_pegawai_id" id="kepegawaian_pegawai_id" class="form-control">
                        <option value="">-- Pilih Pegawai --</option>
                        @foreach ($dataPegawai as $row)
                            <option value="{{ $row['id'] }}">
                                {{ $row['nama'] }}
                            </option>
                        @endforeach
                    </select>
                    @error('kepegawaian_pegawai_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="row">
                    @foreach ($detail as $i => $row)
                        <div class="col-xl-3 col-lg-4 col-md-6">
                            <div
                                class="card text-center  @if ($detail[$i]['absen'] !== false) border-1 bg-cyan-100 border-secondary @else border-1 bg-yellow-100 border-secondary @endif mb-3">
                                <div class="card-body" x-data="{
                                    get isChecked() {
                                        return $wire.detail[{{ $i }}].absen !== false;
                                    }
                                }">
                                    <input class="form-check-input mb-1" type="checkbox" value="1"
                                        wire:model.live="detail.{{ $i }}.absen"
                                        x-model="$wire.detail[{{ $i }}].absen"
                                        @if (!$kepegawaian_pegawai_id) disabled @endif />
                                    <p class="card-text">
                                        {{ \Carbon\Carbon::parse($row['tanggal'])->format('d M Y') }}
                                    </p>
                                    <template x-if="isChecked">
                                        <div>
                                            <select class="form-control"
                                                wire:model="detail.{{ $i }}.shift_id" required>
                                                <option value="">-- Pilih Shift --</option>
                                                @foreach ($dataShift as $row)
                                                    <option value="{{ $row['id'] }}">
                                                        {{ $row['nama'] }}
                                                        ({{ substr($row['jam_masuk'], 0, 5) }} s/d
                                                        {{ substr($row['jam_pulang'], 0, 5) }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('detail.' . $i . '.shift_id')
                                                <span class="text-danger">{{ $message }}</span>
                                            @enderror
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            <div class="panel-footer">
                @unlessrole('guest')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Submit
                    </button>
                @endunlessrole
                <x-alert />
            </div>
        </div>
    </form>
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
