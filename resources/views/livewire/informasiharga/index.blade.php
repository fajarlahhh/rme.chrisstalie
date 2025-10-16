<div>
    @section('title', 'Informasi Harga')

    @section('breadcrumb')
        <li class="breadcrumb-item active">Informasi Harga</li>
    @endsection

    <h1 class="page-header">Informasi Harga</h1>

    <x-alert />

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">
            
            <h4 class="panel-title">Form</h4>
        </div>
        <div class="panel-body">
            <div class="mb-3">
                <label class="form-label">Cari Data</label>
                <select x-init="$($el).selectpicker({
                    liveSearch: true,
                        width: 'auto',
                    size: 10,
                    container: 'body',
                    style: '',
                    showSubtext: true,
                    styleBase: 'form-control'
                })" class="form-control" wire:model.live="information_id" data-width="100%">
                    <option hidden selected>-- Tidak Ada Data --</option>
                    @foreach ($informationData as $item)
                        <option value="{{ $item['id'] }}" data-subtext="{{ $item['type'] }}">{{ $item['nama'] }}
                        </option>
                    @endforeach
                </select>
                @error('type')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div >
                @if ($information_id)
                    <div class="mb-3">
                        <label class="form-label">Harga</label>
                        <input class="form-control" type="text" value="{{ number_format($information['harga']) }}"
                            readonly />
                    </div>
                    @if ($information['type'] != 'PelayananTindakan Medis')
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Stok <small>({{ $information['satuan'] }})</small></label>
                            <input class="form-control" type="text" value="{{ $information['stok'] }}" readonly />
                        </div>
                        <div class="mb-3">
                            <label class="form-label">KFA</label>
                            <input class="form-control" type="text" value="{{ $information['kode'] }}" readonly />
                        </div>
                    @else
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">ICD 9 CM</label>
                            <input class="form-control" type="text" value="{{ $information['kode'] }}" readonly />
                        </div>
                    @endif
                @endif
            </div>
        </div>
    </div>
</div>
