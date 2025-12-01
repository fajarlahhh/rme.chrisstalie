<form wire:submit="submit">
    <div class="panel panel-inverse" data-sortable-id="table-basic-2">
        <!-- BEGIN panel-heading -->
        <div class="panel-heading">
            <h4 class="panel-title">Pindah Saldo Kas</h4>
            <div class="panel-heading-btn">
                <a href="javascript:;" class="btn btn-xs btn-icon btn-default" data-toggle="panel-expand"><i
                        class="fa fa-expand"></i></a>
            </div>
        </div>
        <!-- END panel-heading -->
        <!-- BEGIN panel-body -->
        <div class="panel-body">
            <div class="mb-3">
                <label class="form-label" for="tanggal">Tanggal</label>
                <input type="date" class="form-control" wire:model="tanggal" id="tanggal"
                    max="{{ date('Y-m-d') }}">
                @error('tanggal')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="uraian">Uraian</label>
                <textarea wire:model="uraian" id="uraian" class="form-control" rows="3"></textarea>
                @error('uraian')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label">Sumber Dana</label>
                <select class="form-control" x-init="$($el).selectpicker({
                    liveSearch: true,
                    width: 'auto',
                    size: 10,
                    container: 'body',
                    style: '',
                    showSubtext: true,
                    styleBase: 'form-control'
                })" wire:model="sumber_dana_id" data-width="100%">
                    <option hidden selected>-- Pilih Sumber Dana --</option>
                    @foreach ($dataKodeAkun as $item)
                        <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                    @endforeach
                </select>
                @error('sumber_dana_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="tujuan_dana_id">Tujuan Dana</label>
                <select class="form-control" x-init="$($el).selectpicker({
                    liveSearch: true,
                    width: 'auto',
                    size: 10,
                    container: 'body',
                    style: '',
                    showSubtext: true,
                    styleBase: 'form-control'
                })" wire:model="tujuan_dana_id" data-width="100%">
                    <option hidden selected>-- Pilih Tujuan Dana --</option>
                    @foreach ($dataKodeAkun as $item)
                        <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                    @endforeach
                </select>
                @error('tujuan_dana_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
            </div>
            <div class="mb-3">
                <label class="form-label" for="nilai">Nilai</label>
                <input type="number" class="form-control" wire:model="nilai" id="nilai" min="0">
                @error('nilai')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
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
            <button type="button" onclick="window.location.href='/jurnalkeuangan'" class="btn btn-warning"
                wire:loading.attr="disabled">
                <span wire:loading class="spinner-border spinner-border-sm"></span>Data</button>
            <x-alert />
        </div>
    </div>
</form>
