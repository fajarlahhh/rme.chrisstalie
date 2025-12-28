<div x-data="pegawaiForm()" x-init="init()" x-ref="alpineRoot">
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Pegawai')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item">Pegawai</li>
        <li class="breadcrumb-item active">{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Pegawai <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">No. KTP</label>
                            <input class="form-control" type="number" step="1" maxlength="16" minlength="16"
                                wire:model="nik" @if ($status == 'Non Aktif') disabled @endif />
                            @error('nik')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input class="form-control" type="text" wire:model="nama"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Panggilan</label>
                            <input class="form-control" type="text" wire:model="panggilan"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('panggilan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <input class="form-control" type="text" wire:model="alamat"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('alamat')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. Hp</label>
                            <input class="form-control" type="text" wire:model="no_hp"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('no_hp')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Jenis Kelamin</label>
                            <select data-container="body" class="form-control " wire:model="jenis_kelamin"
                                data-width="100%" @if ($status == 'Non Aktif') disabled @endif>
                                <option selected hidden>-- Tidak Ada Jenis Kelamin --</option>
                                <option value="Laki-laki">Laki-laki</option>
                                <option value="Perempuan">Perempuan</option>
                            </select>
                            @error('jenis_kelamin')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Lahir</label>
                            <input class="form-control" type="date" wire:model="tanggal_lahir"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('tanggal_lahir')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Tanggal Masuk</label>
                            <input class="form-control" type="date" wire:model="tanggal_masuk"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('tanggal_masuk')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">NPWP</label>
                            <input class="form-control" type="text" wire:model="npwp"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('npwp')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">No. BPJS Kesehatan</label>
                            <input class="form-control" type="text" wire:model="no_bpjs"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('no_bpjs')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Satuan Tugas</label>
                            <input class="form-control" type="text" wire:model="satuan_tugas"
                                @if ($status == 'Non Aktif') disabled @endif />
                            @error('satuan_tugas')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>

                        @if ($data->exists)
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" wire:model="status"
                                    @if ($status == 'Aktif') checked @endif />
                                <label class="form-check-label" for="status">
                                    Aktif
                                </label>
                            </div>
                        @endif
                        <div class="mb-3">
                            <input class="form-check-input" type="checkbox" wire:model="upload"
                                @if ($upload == 1) checked disabled @endif />
                            <label class="form-check-label" for="upload">
                                Upload Ke Mesin
                            </label>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="note alert-secondary mb-0">
                            <div class="note-content">
                                <h4>Gaji & Tunjangan</h4>
                                <hr>
                                <div class="table-responsive" x-data="{
                                    add() {
                                            this.unsurGaji.push({
                                                id: '',
                                                sifat: '+',
                                                kode_akun_id: null,
                                            });
                                        },
                                        hapus(index) {
                                            const filteredIndexes = this.unsurGaji
                                                .map((item, i) => ({ item, i }))
                                                .map(({ i }) => i);
                                            // Remove the right index from filtered items
                                            if (filteredIndexes[index] !== undefined) {
                                                this.unsurGaji.splice(filteredIndexes[index], 1);
                                            }
                                        },
                                }" x-ref="unsurGaji">
                                    <table class="table">
                                        <thead>
                                            <tr>
                                                <th>Kode Akun</th>
                                                <th>Nilai</th>
                                                <th class="w-50px">Sifat</th>
                                                <th class="w-5px"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <template x-for="(row, index) in unsurGaji" :key="index">
                                                <tr>
                                                    <td>
                                                        <select class="form-control"
                                                            :name="'unsurGaji[' + index + '][kode_akun_id]'"
                                                            x-model="row.kode_akun_id">
                                                            <option value="">-- Tidak Ada Kode Akun --</option>
                                                            @foreach ($dataKodeAkun as $subRow)
                                                                <option value="{{ $subRow['id'] }}">
                                                                    {{ $subRow['id'] }} - {{ $subRow['nama'] }}
                                                                </option>
                                                            @endforeach
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <input type="text" class="form-control"
                                                            :name="'unsurGaji[' + index + '][nilai]'" x-model="row.nilai"
                                                            autocomplete="off">
                                                    </td>
                                                    <td>
                                                        <select class="form-control"
                                                            :name="'unsurGaji[' + index + '][sifat]'"
                                                            x-model="row.sifat">
                                                            <option value="+">+</option>
                                                            <option value="-">-</option>
                                                        </select>
                                                    </td>
                                                    <td>
                                                        <button type="button" class="btn btn-danger"
                                                            @click="hapus(index)">
                                                            <i class="fa fa-times"></i>
                                                        </button>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                        <tfoot>
                                            <tr>
                                                <td colspan="5">
                                                    <div class="text-center">
                                                        <button type="button" class="btn btn-secondary"
                                                            @click="add">
                                                            Tambah Unsur Gaji
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <button type="button" onclick="window.location.href='/datamaster/pegawai'" class="btn btn-danger"
                    wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
                <x-alert />
            </div>
        </form>
    </div>
    
    <div wire:loading>
        <x-loading />
    </div>
</div>

@push('scripts')
    <script>
        function pegawaiForm() {
            return {
                unsurGaji: @js($unsurGaji),
                syncToLivewire() {
                    if (window.Livewire && window.Livewire.find) {
                        let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            let $wire = window.Livewire.find(componentId);
                            if ($wire && typeof $wire.set === 'function') {
                                console.log(JSON.parse(JSON.stringify(this.unsurGaji)));
                                $wire.set('unsurGaji', JSON.parse(JSON.stringify(this.unsurGaji)), true);
                            }
                        }
                    }
                },
                init() {}
            }
        }
    </script>
@endpush
