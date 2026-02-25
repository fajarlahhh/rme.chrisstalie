<div x-data="jurnalumumForm()" x-init="init()" x-ref="alpineRoot">
    <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire()">
        <div class="panel panel-inverse" data-sortable-id="table-basic-2">
            <!-- BEGIN panel-heading -->
            <div class="panel-heading overflow-auto d-flex">
                <h4 class="panel-title">Jurnal Umum</h4>
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
                    <input type="date" class="form-control" x-model="tanggal"
                        @if ($data->exists) disabled @endif id="tanggal" max="{{ date('Y-m-d') }}">
                    <template x-if="errors.tanggal">
                        <span class="text-danger" x-text="errors.tanggal"></span>
                    </template>
                    @error('tanggal')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="uraian">Uraian</label>
                    <textarea x-model="uraian" id="uraian" class="form-control" rows="3"></textarea>
                    <template x-if="errors.uraian">
                        <span class="text-danger" x-text="errors.uraian"></span>
                    </template>
                    @error('uraian')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label" for="jenis">Jenis</label>
                    <select class="form-control" x-model="jenis" id="jenis">
                        <option value="" selected hidden>-- Pilih Jenis --</option>
                        @foreach ($dataJenis as $item)
                            <option value="{{ $item }}">{{ $item }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="note alert-secondary mb-0">
                    <div class="note-content table-responsive">
                        <table class="table table-borderless">
                            <thead>
                                <tr>
                                    <th>Kode Akun</th>
                                    <th class="w-150px">Debet</th>
                                    <th class="w-150px">Kredit</th>
                                    <th class="w-5px"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <template x-for="(row, index) in detail" :key="index">
                                    <tr>
                                        <td>
                                            <div wire:ignore>
                                                <select class="form-control" x-model="row.id" x-init="$nextTick(() => {
                                                    $($el).select2({ width: '100%', dropdownAutoWidth: true });
                                                    $($el).on('change', function(e) {
                                                        row.id = e.target.value;
                                                    });
                                                    $watch('row.id', (value) => {
                                                        if (value !== $($el).val()) {
                                                            $($el).val(value).trigger('change');
                                                        }
                                                    });
                                                })">
                                                    <option value="">-- Pilih Kode Akun --</option>
                                                    <template x-for="akun in dataKodeAkun" :key="akun.id">
                                                        <option :value="akun.id" :selected="row.id == akun.id"
                                                            x-text="`${akun.id} - ${akun.nama}`"></option>
                                                    </template>
                                                </select>
                                            </div>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control w-150px text-end" min="0"
                                                step="any" x-model.number="row.debet" step="any"
                                                @input="hitungTotal()">
                                            <template x-if="errors['detail.'+index+'.debet']">
                                                <span class="text-danger"
                                                    x-text="errors['detail.'+index+'.debet']"></span>
                                            </template>
                                        </td>
                                        <td>
                                            <input type="number" class="form-control w-150px text-end" min="0"
                                                step="any" x-model.number="row.kredit" step="any"
                                                @input="hitungTotal()">
                                            <template x-if="errors['detail.'+index+'.kredit']">
                                                <span class="text-danger"
                                                    x-text="errors['detail.'+index+'.kredit']"></span>
                                            </template>
                                        </td>
                                        <td>
                                            <a href="javascript:;" class="btn btn-danger" @click="hapusDetail(index)">
                                                <i class="fa fa-times"></i>
                                            </a>
                                        </td>
                                    </tr>
                                </template>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td class="text-end">&nbsp;</td>
                                    <th>
                                        <input type="text" class="form-control text-end w-150px" autocomplete="off"
                                            x-model="sumDebet" disabled>
                                        <template x-if="errors.debet">
                                            <span class="text-danger" x-text="errors.debet"></span>
                                        </template>
                                    </th>
                                    <th>
                                        <input type="text" class="form-control text-end w-150px" autocomplete="off"
                                            x-model="sumKredit" disabled>
                                        <template x-if="errors.kredit">
                                            <span class="text-danger" x-text="errors.kredit"></span>
                                        </template>
                                    </th>
                                    <td>&nbsp;</td>
                                </tr>
                                <tr>
                                    <td colspan="4">
                                        <div class="text-center">
                                            <button type="button" class="btn btn-secondary" @click="tambahDetail()"
                                                wire:loading.attr="disabled">
                                                <span wire:loading class="spinner-border spinner-border-sm"></span>
                                                Tambah Kode Akun
                                            </button>
                                            <br>
                                            <template x-if="errors.detail">
                                                <span class="text-danger" x-text="errors.detail"></span>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
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
                <x-alert />
            </div>
        </div>
    
        <x-modal.konfirmasi />
    </form>

    <div wire:loading>
        <x-loading />
    </div>
</div>


@push('scripts')
    <script>
        function jurnalumumForm() {
            return {
                tanggal: @js($tanggal),
                uraian: @js($uraian),
                dataKodeAkun: @js($dataKodeAkun),
                detail: @js($detail),
                errors: {},
                jenis: @js($jenis),
                sumDebet: 0,
                sumKredit: 0,
                loadingSubmit: false,
                init() {
                    this.hitungTotal();
                },

                hitungTotal() {
                    let totalDebet = 0,
                        totalKredit = 0;
                    this.detail.forEach(row => {
                        totalDebet += parseFloat(row.debet || 0);
                        totalKredit += parseFloat(row.kredit || 0);
                    });
                    this.sumDebet = this.formatNumber(totalDebet);
                    this.sumKredit = this.formatNumber(totalKredit);
                },

                formatNumber(val) {
                    if (val === null || val === undefined || isNaN(val)) return '0';
                    return (val).toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                },

                tambahDetail() {
                    this.detail.push({
                        id: '',
                        debet: '',
                        kredit: ''
                    });
                    this.$nextTick(() => {
                        this.refreshSelect2();
                    });
                    this.hitungTotal();
                },
                hapusDetail(idx) {
                    this.detail.splice(idx, 1);
                    this.hitungTotal();
                    this.$nextTick(() => {
                        this.refreshSelect2();
                    });
                },
                syncToLivewire() {
                    // sinkronkan data ke livewire
                    if (window.Livewire && window.Livewire.find) {
                        let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            let $wire = window.Livewire.find(componentId);
                            if ($wire && typeof $wire.set === 'function') {
                                $wire.set('detail', JSON.parse(JSON.stringify(this.detail)), true);
                                $wire.set('tanggal', this.tanggal, true);
                                $wire.set('uraian', this.uraian, true);
                                $wire.set('jenis', this.jenis, true);
                            }
                        }
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
            }
        }
    </script>
@endpush
