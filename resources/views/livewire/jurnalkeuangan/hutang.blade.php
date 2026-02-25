<div>
    <form wire:submit="submit">
        <div class="panel panel-inverse" data-sortable-id="table-basic-2">
            <!-- BEGIN panel-heading -->
            <div class="panel-heading overflow-auto d-flex">
                <h4 class="panel-title">Hutang</h4>
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
                        max="{{ date('Y-m-d') }}" @if ($data->exists) disabled @endif>
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
                    <label class="form-label" for="jenis_hutang_id">Jenis Hutang</label>
                    <select class="form-control" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    })" wire:model="jenis_hutang_id"
                        data-width="100%">
                        <option hidden selected>-- Pilih Jenis Hutang --</option>
                        @foreach ($dataJenisHutang as $row)
                            <option value="{{ $row['id'] }}">{{ $row['id'] }} - {{ $row['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('jenis_hutang_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Kas/Bank</label>
                    <select class="form-control" x-init="$($el).selectpicker({
                        liveSearch: true,
                        width: 'auto',
                        size: 10,
                        container: 'body',
                        style: '',
                        showSubtext: true,
                        styleBase: 'form-control'
                    })" wire:model="kas_bank_id" data-width="100%">
                        <option hidden selected>-- Pilih Kas/Bank --</option>
                        @foreach ($dataKasBank as $item)
                            <option value="{{ $item['id'] }}">{{ $item['id'] }} - {{ $item['nama'] }}</option>
                        @endforeach
                    </select>
                    @error('kas_bank_id')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="nilai">Nilai</label>
                    <input type="number" class="form-control" step="any" wire:model="nilai" id="nilai" min="0">
                    @error('nilai')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <!-- END panel-body -->
            <div class="panel-footer">
                @unlessrole('guest')
                    <button type="button" x-init="$($el).on('click', function() {
                        $('#modal-konfirmasi').modal('show');
                    })" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Submit
                    </button>
                @endunlessrole
                <button type="button" onclick="window.location.href='/jurnalkeuangan'" class="btn btn-warning"
                    wire:loading.attr="disabled">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>Data</button>
                <x-alert />
            </div>
        </div>
        <x-modal.konfirmasi />
    </form>
    <div wire:loading>
        <x-loading />
    </div>
</div>
