<div>
    @push('css')
        <style>
            .vertical-input-group .input-group:first-child {
                padding-bottom: 0;
            }

            .vertical-input-group .input-group:first-child * {
                border-bottom-left-radius: 0;
                border-bottom-right-radius: 0;
            }

            .vertical-input-group .input-group:last-child {
                padding-top: 0;
            }

            .vertical-input-group .input-group:last-child * {
                border-top-left-radius: 0;
                border-top-right-radius: 0;
            }

            .vertical-input-group .input-group:not(:last-child):not(:first-child) {
                padding-top: 0;
                padding-bottom: 0;
            }

            .vertical-input-group .input-group:not(:last-child):not(:first-child) * {
                border-radius: 0;
            }

            .vertical-input-group .input-group:not(:first-child) * {
                border-top: 0;
            }
        </style>
    @endpush
    <div class="row">
        @foreach ($fileDiupload as $index => $row)
            <div class="col-md-6 col-lg-4 col-xl-4">
                @if ($row['link'])
                    <div class="card">
                        @if ($row['extensi'] == 'pdf')
                            <a href="{{ Storage::url($row['link']) }}" class="btn btn-success m-20" target="_blank"><i
                                    class="far fa-file-pdf"></i> Buka PDF</a>
                        @else
                            <a href="{{ Storage::url($row['link']) }}" target="_blank">
                                <img class="card-img-top img-fluid" src="{{ Storage::url($row['link']) }}">
                            </a>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title mb-2">{{ $row['judul'] }}</h5>
                            <p class="card-text">
                                {{ $row['keterangan'] }}
                            </p>
                            @if (collect($fileDihapus)->contains($row['link']))
                                <button class="btn btn-warning w-100" type="button"
                                    wire:click="batalFileDihapus({{ $row['id'] }})">Batal Dihapus
                                </button>
                            @else
                                <button class="btn btn-danger w-100" type="button"
                                    wire:click="tambahFileDihapus({{ $row['id'] }}, '{{ $row['link'] }}')">Hapus</button>
                            @endif
                        </div>
                    </div>
                @else
                    <div class="vertical-input-group">
                        <div class="input-group">
                            <input class="form-control" type="file" autocomplete="off"
                                accept="image/jpeg,image/jpg,image/png,application/pdf"
                                wire:model.defer="fileDiupload.{{ $index }}.file"
                                @error('fileDiupload.' . $index . '.file') style="border-color: #ff5b57!important" @enderror />
                        </div>
                        <div class="input-group">
                            <input class="form-control" type="text" autocomplete="off" placeholder="Judul"
                                wire:model.defer="fileDiupload.{{ $index }}.judul"
                                @error('fileDiupload.' . $index . '.judul') style="border-color: #ff5b57!important" @enderror />
                        </div>
                        <div class="input-group">
                            <input class="form-control" type="text" autocomplete="off" placeholder="Keterangan"
                                wire:model.defer="fileDiupload.{{ $index }}.keterangan"
                                @error('fileDiupload.' . $index . '.keterangan') style="border-color: #ff5b57!important" @enderror />
                        </div>
                        <div class="input-group">
                            <a href="javascript:;" wire:click="hapusFileDiupload({{ $index }})"
                                wire:loading.attr="disabled" class="btn btn-danger w-100"> Hapus</a>
                        </div>
                    </div>
                @endif
            </div>
        @endforeach
        <div class="col-md-6 col-lg-4 col-xl-4">
            <a href="javascript:;" wire:click="tambahFileDiupload" class="btn btn-secondary w-100 p-0"
                style="height:135px">
                <div class="card border-0 w-100 h-100">
                    <div class="card-body text-dark pt-40px">Upload
                        File<br><small>(jpeg, jpg, png, pdf)</small>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>
