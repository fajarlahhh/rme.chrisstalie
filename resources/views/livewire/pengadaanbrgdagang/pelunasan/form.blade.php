<div>
    @section('title', 'Pelunasan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Pengadaan Barang Dagang</li>
        <li class="breadcrumb-item">Pelunasan</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Pelunasan <small>Tambah</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3" wire:ignore>
                    <label class="form-label">Pembelian</label>
                    <select class="form-control" x-init="$($el).select2({ width: '100%', dropdownAutoWidth: true });
                    $($el).on('change', function(e) {
                        $wire.set('pembelian_id', e.target.value);
                    });" wire:model="pembelian_id" required>
                        <option selected value="" hidden>-- Cari Data Pembelian --</option>
                        @foreach ($dataPembelian as $row)
                            <option value="{{ $row['id'] }}">
                                {{ $row['tanggal'] }} - {{ $row['uraian'] }}, Supplier : {{ $row['supplier']['nama'] }}, Total Harga : {{ number_format($row['total_harga']) }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tanggal</label>
                    <input class="form-control" type="date" wire:model="tanggal" max="{{ now()->format('Y-m-d') }}"
                        required />
                    @error('tanggal')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Uraian</label>
                    <input class="form-control" type="text" wire:model="uraian" required />
                    @error('uraian')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label"> Pembayaran</label>
                    <select class="form-control" wire:model="kode_akun_pembayaran_id" data-width="100%">
                        <option hidden selected>-- Tidak Ada Kode Akun --</option>
                        @foreach ($dataKodePembayaran as $item)
                            <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="panel-footer">
                @unlessrole('guest')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endunlessrole
                <button type="button" onclick="window.location.href='/pengadaanbrgdagang/stokmasuk'"
                    class="btn btn-danger" wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Kembali
                </button>
            </div>
        </form>
    </div>

    <x-alert />
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
