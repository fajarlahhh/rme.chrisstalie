<div>
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Data Barang Konsinyasi')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item">Barang Konsinyasi</li>
        <li class="breadcrumb-item active">{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Barang Konsinyasi <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    <x-alert />

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
                            <label class="form-label">Jenis Barang</label>
                            <select class="form-control" wire:model.live="jenis" data-width="100%">
                                <option hidden selected>-- Pilih Jenis Barang --</option>
                                <option value="Alat Kesehatan">Alat Kesehatan</option>
                                <option value="Obat">Obat</option>
                                <option value="Produk Kecantikan">Produk Kecantikan</option>
                            </select>
                            @error('jenis')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input class="form-control" type="text" wire:model="nama" />
                            @error('nama')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Satuan</label>
                            <input class="form-control" type="text" wire:model="satuan" />
                            @error('satuan')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Harga Jual</label>
                            <input class="form-control" type="number" wire:model="harga_jual" />
                            @error('harga_jual')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Konsinyator</label>
                            <select class="form-control" wire:model="konsinyator_id" data-width="100%">
                                <option hidden selected>-- Pilih Konsinyator --</option>
                                @foreach ($dataSupplier as $item)
                                    <option value="{{ $item->id }}">{{ $item->nama }}</option>
                                @endforeach
                            </select>
                            @error('konsinyator_id')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        @if ($jenis == 'Obat')
                            <div class="mb-3">
                                <label class="form-label">Bentuk</label>
                                <select class="form-control" wire:model="bentuk" data-width="100%">
                                    <option hidden selected>-- Pilih Bentuk --</option>
                                    <option value="Tablet">Tablet</option>
                                    <option value="Kapsul">Kapsul</option>
                                    <option value="Sirup">Sirup</option>
                                    <option value="Salep">Salep</option>
                                    <option value="Lainnya">Lainnya</option>
                                </select>
                                @error('bentuk')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" wire:model="perlu_resep"
                                    @if ($perlu_resep) checked @endif />
                                <label class="form-check-label" for="perlu_resep">
                                    Perlu Resep
                                </label>
                            </div>
                        @endif
                        @if ($jenis == 'Produk Kecantikan')
                            <div class="form-check mb-3">
                                <input class="form-check-input" type="checkbox" wire:model="perlu_resep"
                                    @if ($perlu_resep) checked @endif />
                                <label class="form-check-label" for="perlu_resep">
                                    Perlu Resep
                                </label>
                            </div>
                        @endif
                    </div>
                    <div class="col-md-6">
                        <div class="alert alert-info">
                            @if ($jenis == 'Obat' || $jenis == 'Produk Kecantikan')
                                <div class="mb-3">
                                    <label class="form-label">Golongan</label>
                                    <select class="form-control" wire:model="golongan" data-width="100%">
                                        <option hidden selected>-- Pilih Golongan --</option>
                                        <option value="Obat Bebas">Obat Bebas</option>
                                        <option value="Obat Bebas Terbatas">Obat Bebas Terbatas</option>
                                        <option value="Obat Keras">Obat Keras</option>
                                        <option value="Psikotropika">Psikotropika</option>
                                        <option value="Narkotika">Narkotika</option>
                                    </select>
                                    @error('golongan')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Indikasi</label>
                                    <input class="form-control" type="text" wire:model="indikasi" />
                                    @error('indikasi')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Kontraindikasi</label>
                                    <input class="form-control" type="text" wire:model="kontraindikasi" />
                                    @error('kontraindikasi')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Efek Samping</label>
                                    <input class="form-control" type="text" wire:model="efek_samping" />
                                    @error('efek_samping')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @elseif ($jenis == 'Alat Kesehatan')
                                <div class="mb-3">
                                    <label class="form-label">Garansi <small>(Bulan)</small></label>
                                    <input class="form-control" type="number" wire:model="garansi" />
                                    @error('garansi')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <input type="submit" value="Simpan" class="btn btn-success" />
                @endrole
                <a href="{{ $previous }}" class="btn btn-danger" wire:ignore>Batal</a>
            </div>
        </form>
    </div>
</div>
