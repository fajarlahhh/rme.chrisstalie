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
                <div class="row">
                    <div class="col-md-6">
                        <div class="mb-3">
                            <label class="form-label">Tanggal</label>
                            <input class="form-control" type="date" wire:model="tanggal" max="{{ date('Y-m-d') }}" />
                            @error('tanggal')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Periode</label>
                            <input class="form-control" type="month" wire:model.live="periode" max="{{ date('Y-m-d') }}" />
                            @error('periode')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Pegawai</label>
                            <select class="form-control" wire:model.live="pegawai_id" x-init="$($el).selectpicker({
                                liveSearch: true,
                                width: 'auto',
                                size: 10,
                                container: 'body',
                                style: '',
                                showSubtext: true,
                                styleBase: 'form-control'
                            });">
                                <option selected hidden>-- Pilih Pegawai --</option>
                                @foreach ($dataPegawai as $item)
                                    <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Metode Pembayaran</label>
                            <select class="form-control" wire:model="metode_bayar">
                                <option selected hidden>-- Pilih Metode Pembayaran --</option>
                                @foreach (collect($dataKodeAkun) as $item)
                                    <option value="{{ $item['id'] }}">{{ $item['id'] . ' - ' . $item['nama'] }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            <h4>Komponen Gaji</h4>
                            <hr>
                            <table class="table">
                                <tbody>
                                    @foreach ($detail as $index => $row)
                                        <tr>
                                            <td>
                                                <input type="text" class="form-control"
                                                    value="{{ $row['kode_akun_id'] . ' - ' . $row['kode_akun_nama'] }}"
                                                    disabled>
                                            </td>
                                            <td>
                                                <input type="text" class="form-control" required
                                                    wire:model="detail.{{ $index }}.debet" autocomplete="off"
                                                    disabled>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="button" x-init="$($el).on('click', function() {
                        $('#modal-konfirmasi').modal('show');
                    })" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Submit
                    </button>
                @endrole
                <button type="button" onclick="window.location.href='/kepegawaian/penggajian'" class="btn btn-danger"
                    wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Batal
                </button>
                <x-alert />
            </div>
        
        <x-modal.konfirmasi />
    </form>
    </div>

    <div wire:loading>
        <x-loading />
    </div>
</div>
