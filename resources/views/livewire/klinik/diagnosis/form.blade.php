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
                <table class="table table-borderless p-0">
                    <thead>
                        <tr>
                            <th class="p-0">ICD 10</th>
                            <th class="w-5px p-0"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <template x-for="(row, index) in diagnosis" :key="index">
                            <tr>
                                <th class="p-0" wire:ignore>
                                    <select class="form-control" x-model="row.icd10" x-init="$($el).select2({
                                        width: '100%',
                                        dropdownAutoWidth: true
                                    });
                                    $($el).on('change', function(e) {
                                        row.icd10 = e.target.value;
                                    });
                                    $watch('row.icd10', (value) => {
                                        if (value !== $($el).val()) {
                                            $($el).val(value).trigger('change');
                                        }
                                    });">
                                        <option value="" selected>-- Pilih ICD 10 --</option>
                                        <template x-for="item in dataIcd10" :key="item.id">
                                            <option :value="item.id" :selected="row.icd10 == item.id"
                                                x-text="`${item.id} - ${item.uraian}`">
                                            </option>
                                        </template>
                                    </select>
                                </th>
                                <th class="align-middle w-5px pt-0 pb-0 pr-0">
                                    <template x-if="index > 0">
                                        <button type="button" class="btn btn-danger btn-sm"
                                            @click="diagnosis.splice(index, 1)">
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
                <button type="button" class="btn btn-warning m-r-3" wire:loading.attr="disabled"
                    onclick="window.location.href='/klinik/diagnosis'">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Data
                </button>
                <x-alert />
            </div>
        </div>
    </form>
</div>

@push('scripts')
    <script>
        function diagnosisForm() {
            return {
                diagnosis: @js($diagnosis).map(row => ({
                    ...row
                })),
                dataIcd10: @js($dataIcd10),
                diagnosis_banding: @js($diagnosis_banding),
                fileDiupload: @js($fileDiupload),
                addDiagnosis() {
                    this.diagnosis.push({
                        icd10: '',
                    });
                },
                hapusDiagnosis(index) {
                    this.diagnosis.splice(index, 1);
                },
                syncToLivewire() {
                    if (window.Livewire && window.Livewire.find) {
                        let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            let $wire = window.Livewire.find(componentId);
                            if ($wire && typeof $wire.set === 'function') {
                                $wire.set('diagnosis', JSON.parse(JSON.stringify(this.diagnosis)), true);
                            }
                        }
                    }
                }
            }
        }
    </script>
@endpush
