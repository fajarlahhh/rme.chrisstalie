<div x-data="unsurGajiForm()" x-init="init()" x-ref="alpineRoot">
    @section('title', 'Unsur Gaji')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Unsur Gaji</li>
    @endsection

    <h1 class="page-header">Unsur Gaji</h1>
    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <div class="panel-body table-responsive">
            <form wire:submit.prevent="submit" @submit.prevent="syncToLivewire">
                <div class="table-responsive" x-data="{
                    add() {
                            this.unsurGaji.push({
                                id: '',
                                sifat: '+',
                                kode_akun_id: null,
                            });
                        },
                        hapus(index) {
                            // Find all items with matching unit_bisnis
                            const filteredIndexes = this.unsurGaji
                                .map((item, i) => ({ item, i }))
                                .map(({ i }) => i);
                            // Remove the right index from filtered items
                            if (filteredIndexes[index] !== undefined) {
                                this.unsurGaji.splice(filteredIndexes[index], 1);
                            }
                        },
                }" x-ref="unsurGaji">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Unsur Gaji</th>
                                <th class="w-50px">Sifat</th>
                                <th>Kode Akun</th>
                                <th class="w-5px"></th>
                            </tr>
                        </thead>
                        <tbody>
                            <template x-for="(row, index) in unsurGaji" :key="index">
                                <tr>
                                    <td>
                                        <input type="text" class="form-control"
                                            :name="'unsurGaji[' + index + '][nama]'" x-model="row.nama"
                                            autocomplete="off">
                                    </td>
                                    <td>
                                        <select class="form-control" :name="'unsurGaji[' + index + '][sifat]'"
                                            x-model="row.sifat">
                                            <option value="+">+</option>
                                            <option value="-">-</option>
                                        </select>
                                    </td>
                                    <td>
                                        <select class="form-control" :name="'unsurGaji[' + index + '][kode_akun_id]'"
                                            x-model="row.kode_akun_id">
                                            <option value="">-- Pilih Kode Akun --</option>
                                            @foreach ($dataKodeAkun as $subRow)
                                                <option value="{{ $subRow['id'] }}">
                                                    {{ $subRow['id'] }} - {{ $subRow['nama'] }}
                                                </option>
                                            @endforeach
                                        </select>
                                    </td>
                                    <td>
                                        <button type="button" class="btn btn-danger" @click="hapus(index)">
                                            <i class="fa fa-times"></i>
                                        </button>
                                    </td>
                                </tr>
                            </template>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="5">
                                    <div class="text-center">
                                        <button type="button" class="btn btn-secondary" @click="add">
                                            Tambah Unsur Gaji
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </tfoot>
                    </table>
                    @role('administrator|supervisor')
                        <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                            <span wire:loading class="spinner-border spinner-border-sm"></span>
                            Simpan
                        </button>
                        <x-alert />
                    @endrole
                </div>
            </form>
        </div>
    </div>
</div>


@push('scripts')
    <script>
        function unsurGajiForm() {
            return {
                unsurGaji: @js($unsurGaji).map(row => ({
                    ...row,
                })),
                syncToLivewire() {
                    if (window.Livewire && window.Livewire.find) {
                        let componentId = this.$root.closest('[wire\\:id]')?.getAttribute('wire:id');
                        if (componentId) {
                            let $wire = window.Livewire.find(componentId);
                            if ($wire && typeof $wire.set === 'function') {
                                $wire.set('unsurGaji', JSON.parse(JSON.stringify(this.unsurGaji)), true);
                            }
                        }
                    }
                },
                init() {}
            }
        }
    </script>
@endpush
