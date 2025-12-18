<div>
    @section('title', 'Tambah Penggajian')

    @section('breadcrumb')
        <li class="breadcrumb-item">Kepegawaian</li>
        <li class="breadcrumb-item">Penggajian</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Penggajian <small>Tambah</small></h1>


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
                    <label class="form-label">Periode</label>
                    <input class="form-control" type="month" wire:model.live="periode" />
                    @error('periode')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                @if ($detail != [])
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th class="w-5px" rowspan="2">No.</th>
                                <th rowspan="2">Nama</th>
                                <th colspan="{{ collect($dataUnsurGaji)->count() }}">
                                    Unsur Gaji
                                </th>
                                <th rowspan="2">Total</th>
                            </tr>
                            <tr>
                                @foreach ($dataUnsurGaji as $unsurGaji)
                                    <th> {{ $unsurGaji['nama'] }}
                                    </th>
                                @endforeach
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                            </tr>
                            @foreach ($detail as $index => $item)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td> {{ $item['nama'] }}
                                    </td>
                                    @foreach ($item['pegawai_unsur_gaji'] as $subIndex => $subItem)
                                        <td>
                                            <input class="form-control text-end" type="text"
                                                wire:model.lazy="detail.{{ $index }}.pegawai_unsur_gaji.{{ $subIndex }}.nilai" />
                                        </td>
                                    @endforeach
                                    <td>
                                        <input class="form-control text-end" type="text"
                                            value="{{ number_format(collect($item['pegawai_unsur_gaji'])->sum('nilai')) }}"
                                            disabled />
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr>
                                <th colspan="{{ collect($dataUnsurGaji)->count() + 2 }}">Total</th>
                                @foreach ($maxPegawai['pegawai_unsur_gaji'] ?? [] as $subItem)
                                    <th>
                                        <input class="form-control text-end" type="text"
                                            value="{{ number_format(collect($detail)->sum(fn($p) => collect($p['pegawai_unsur_gaji'])->firstWhere('kode_akun_id', $subItem['kode_akun_id'])['nilai'] ?? 0)) }}"
                                            disabled />
                                    </th>
                                @endforeach
                                <th>
                                    <input class="form-control text-end" type="text"
                                        value="{{ number_format(collect($detail)->sum(fn($p) => collect($p['pegawai_unsur_gaji'])->sum('nilai'))) }}"
                                        disabled />
                                </th>
                            </tr>
                        </tfoot>
                    </table>

                    <div class="mb-3">
                        <label class="form-label">Metode Bayar</label>
                        <select class="form-control" wire:model="metode_bayar">
                            <option selected hidden>-- Pilih Metode Bayar --</option>
                            @foreach ($dataKodeAkun as $item)
                                <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="alert alert-warning text-center">
                        <h5>Penggajian pada periode {{ $periode }} sudah pernah dibuat</h5>
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
                <button type="button" onclick="window.location.href='/kepegawaian/penggajian'" class="btn btn-danger"
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
