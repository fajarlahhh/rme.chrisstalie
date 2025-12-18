<div x-data="diagnosisForm()" x-ref="alpineRoot">
    @section('title', 'Input Diagnosis')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item">Diagnosis</li>
        <li class="breadcrumb-item active">Input</li>
    @endsection

    <h1 class="page-header">Diagnosis <small>Input</small></h1>

    @include('livewire.klinik.informasipasien', ['data' => $data])

    <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
        <div class="panel panel-inverse">
            <div class="panel-heading">
                <h4 class="panel-title">Assessment (Penilaian)</h4>
            </div>
            <div class="panel-body">
                <div class="alert alert-info table-responsive h-400px">
                    <h5>History Diagnosis</h5>
                    <table class="table">
                        <tr>
                            <th>Tanggal</th>
                            <th>Diagnosis</th>
                            <th>Diagnosis Banding (Differential Diagnosis)</th>
                            <th>Dokumentasi</th>
                        </tr>
                        @foreach ($data->pasien->rekamMedis->where('id', '!=', $data->id) as $row)
                            @if ($row->diagnosis)
                                <tr>
                                    <td nowrap>{{ $row->diagnosis->created_at->format('d M Y') }}</td>

                                    <td nowrap>
                                        @foreach ($row->diagnosis->icd10_uraian as $item)
                                            {{ $item->id }} - {{ $item->uraian }}<br>
                                        @endforeach
                                    <td nowrap>{{ $row->diagnosis->diagnosis_banding }}</td>
                                    <td nowrap>
                                        @foreach ($row->diagnosis->file as $item)
                                            <img src="{{ Storage::url($item->link) }}" class="img-fluid w-250px"><br>
                                            <small>
                                                Judul :{{ $item->judul }}
                                                <br>
                                                {{ $item->keterangan }}
                                            </small><br>
                                        @endforeach
                                    </td>
                                </tr>
                            @endif
                        @endforeach
                    </table>
                </div>
                <table class="table table-borderless p-0">
                    <thead>
                        <tr>
                            <th class="p-0">ICD 10</th>
                            <th class="w-5px p-0"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, index) in icd10" :key="index">
                            <tr>
                                <th class="p-0" wire:ignore>
                                    <select class="form-control" x-model="row.id" x-init="
                                    $($el).select2({ 
                                        width: '100%',
                                        dropdownAutoWidth: true 
                                    });
                                    $($el).on('change', function(e) {
                                        row.id = e.target.value;
                                        updateRow(index);
                                    });
                                    $watch('row.id', (value) => {
                                        if (value !== $($el).val()) {
                                            $($el).val(value).trigger('change');
                                        }
                                    });
                                ">
                                
                                        <option value="" selected>-- Pilih ICD 10 --</option>
                                        <template x-for="item in dataIcd10" :key="item.id">
                                            <option :value="item.id" :selected="row.id == item.id"
                                                x-text="`${item.id} - ${item.uraian}`">
                                            </option>
                                        </template>
                                    </select>
                                </th>
                                <th class="align-middle w-5px pt-0 pb-0 pr-0">
                                    <template x-if="index > 0">
                                        <button type="button" class="btn btn-danger btn-sm"
                                            @click="hapusDiagnosis(index)">
                                            <span x-show="$wire.__instance.loading"
                                                class="spinner-border spinner-border-sm"></span>
                                            <span x-show="!$wire.__instance.loading">x</span>
                                        </button>
                                    </template>
                                </th>
                            </tr>
                        </template>
                    </tbody>
                    <tr class="p-0">
                        <td colspan="3" class="p-0 pt-1 pb-0 pr-0">
                            <button type="button" class="btn btn-primary btn-sm" @click="addDiagnosis"
                                wire:loading.attr="disabled">
                                <span wire:loading class="spinner-border spinner-border-sm"></span>
                                Tambah ICD 10
                            </button>
                        </td>
                    </tr>
                </table>
                <div class="form-group mb-3">
                    <label for="diagnosis_banding">Diagnosis Banding (Differential Diagnosis)</label>
                    <textarea id="diagnosis_banding" class="form-control" wire:model="diagnosis_banding"
                        placeholder="Tuliskan kemungkinan diagnosis lain yang perlu dipertimbangkan..."></textarea>
                    @error('diagnosis_banding')
                        <div class="text-danger">{{ $message }}</div>
                    @enderror
                </div>
                <div class="p-3 bg-light border rounded">
                    Dokumentasi :
                    <x-upload :fileDiupload="$fileDiupload" :fileDihapus="$fileDihapus" />
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                @if (isset($data->diagnosis) && $data->diagnosis->count() > 0)
                    <button type="button" class="btn btn-info m-r-3" wire:loading.attr="disabled"
                        onclick="window.location.href='/klinik/tindakan/form/{{ $data->id }}'">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Lanjut Tindakan
                    </button>
                @endif
                <button type="button" class="btn btn-secondary m-r-3" wire:loading.attr="disabled"
                    onclick="window.location.href='/klinik/diagnosis'">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Data
                </button>
                <x-alert />
            </div>
        </div>
    </form>
    
    <div wire:loading>
        <x-loading />
    </div>
</div>

@push('scripts')
    <script>
        function diagnosisForm() {
            return {
                icd10: @js($icd10).map(row => ({
                    ...row,
                })),
                dataIcd10: @js($dataIcd10),
                diagnosis_banding: @js($diagnosis_banding),
                fileDiupload: @js($fileDiupload),
                addDiagnosis() {
                    this.icd10.push({
                        id: '',
                    }); 
                    this.$nextTick(() => {
                        this.refreshSelect2();
                    });
                },
                hapusDiagnosis(index) {
                    this.icd10.splice(index, 1);
                    this.$nextTick(() => {
                        this.refreshSelect2();
                    });
                },
                updateRow(index) {
                    let row = this.icd10[index];
                    let selectedIcd10 = this.dataIcd10.find(i => i.id == row.id);
                    if (selectedIcd10) {
                        row.uraian = selectedIcd10.uraian;
                    } else {
                        row.uraian = '';
                    }
                },
                refreshSelect2() {
                    let root = this.$root ?? document;
                    $(root).find('select.form-control').each(function(i, el) {
                        if ($(el).hasClass('select2-hidden-accessible')) {
                            $(el).select2('destroy');
                        }
                        $(el).select2({
                            width: '100%'
                        });
                        el.dispatchEvent(new CustomEvent('updateSelect2Value', {
                            bubbles: true
                        }));
                    });
                },
                syncToLivewire() {
                    if (window.Livewire && window.Livewire.find) {
                        let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            let $wire = window.Livewire.find(componentId);
                            if ($wire && typeof $wire.set === 'function') {
                                $wire.set('icd10', JSON.parse(JSON.stringify(this.icd10)), true);
                            }
                        }
                    }
                }
            }
        }
    </script>
@endpush
