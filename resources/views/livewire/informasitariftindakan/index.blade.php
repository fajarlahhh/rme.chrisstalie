<div>
    @section('title', 'Informasi Tarif Tindakan')

    @section('breadcrumb')
        <li class="breadcrumb-item active">Informasi Tarif Tindakan</li>
    @endsection

    <h1 class="page-header">Informasi Tarif Tindakan</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <div wire:ignore class="w-100">
                <select class="form-control" x-init="$($el).select2({
                    width: '100%',
                    dropdownAutoWidth: true,
                    placeholder: 'Ketik Nama Tarif Tindakan',
                    minimumInputLength: 1,
                    dataType: 'json',
                    ajax: {
                        url: '/cari/tariftindakan',
                        data: function(params) {
                            var query = {
                                cari: params.term
                            }
                            return query;
                        },
                        processResults: function(data, params) {
                            return {
                                results: data,
                            };
                        },
                        cache: true
                    }
                });
                
                $($el).on('change', function(element) {
                    $wire.set('tarifTindakanId', $($el).val());
                });">
                </select>
            </div>
        </div>
        <div class="panel-body table-responsive">
            @if ($dataTarifTindakan)
                <div class="row">
                    <div class="col-md-6">
                        <div class="note alert-primary mb-2">
                            <div class="note-content">
                                <h5>Data Tarif Tindakan</h5>
                                <hr>
                                <table class="w-100">
                                    <tr>
                                        <td>Nama</td>
                                        <td class="w-10px">:</td>
                                        <td>{{ $dataTarifTindakan->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td>Tarif</td>
                                        <td class="w-10px">:</td>
                                        <td>Rp. {{ number_format($dataTarifTindakan->tarif) }}</td>
                                    </tr>
                                    <tr>
                                        <td>Catatan</td>
                                        <td class="w-10px">:</td>
                                        <td nowrap>{!! nl2br(e($dataTarifTindakan->catatan)) !!}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" x-data="{
                        diskon1: 0,
                        diskon2: 0,
                        diskon3: 0,
                        get tarif() { return {{ $dataTarifTindakan->tarif ?? 0 }} },
                        get nominalDiskon1() {
                            return Math.round(this.tarif * this.diskon1 / 100)
                        },
                        get tarifSetelahDiskon1() {
                            return this.tarif - this.nominalDiskon1
                        },
                        get nominalDiskon2() {
                            return Math.round(this.tarifSetelahDiskon1 * this.diskon2 / 100)
                        },
                        get tarifSetelahDiskon2() {
                            return this.tarifSetelahDiskon1 - this.nominalDiskon2
                        },
                        get nominalDiskon3() {
                            return Math.round(this.tarifSetelahDiskon2 * this.diskon3 / 100)
                        },
                        get tarifAkhir() {
                            return this.tarifSetelahDiskon2 - this.nominalDiskon3
                        },
                        formatRupiah(amount) {
                            return 'Rp. ' + amount.toLocaleString('id-ID');
                        }
                    }">
                        <h5>Simulasi Diskon</h5>
                        <hr>
                        <table class="table table-bordered">
                            <tr>
                                <td>Diskon 1 (%)</td>
                                <td class="w-10px">:</td>
                                <td>
                                    <input type="number" step="1" min="0" max="100"
                                        class="form-control" x-model="diskon1">
                                </td>
                                <td class="text-end" x-text="formatRupiah(nominalDiskon1)"></td>
                            </tr>
                            <tr>
                                <td>Diskon 2 (%)</td>
                                <td class="w-10px">:</td>
                                <td>
                                    <input type="number" step="1" min="0" max="100"
                                        class="form-control" x-model="diskon2">
                                </td>
                                <td class="text-end" x-text="formatRupiah(nominalDiskon2)"></td>
                            </tr>
                            <tr>
                                <td>Diskon 3 (%)</td>
                                <td class="w-10px">:</td>
                                <td>
                                    <input type="number" step="1" min="0" max="100"
                                        class="form-control" x-model="diskon3">
                                </td>
                                <td class="text-end" x-text="formatRupiah(nominalDiskon3)"></td>
                            </tr>
                            <tr>
                                <td colspan="3" class="pt-2 pb-2"><strong>Total Diskon</strong></td>
                                <td class="pt-2 pb-2 text-end"><strong
                                        x-text="formatRupiah(nominalDiskon1 + nominalDiskon2 + nominalDiskon3)"></strong>
                                </td>
                            </tr>
                            <tr>
                                <td colspan="3" class="pt-2 pb-2"><strong>Total Setelah Diskon</strong></td>
                                <td class="pt-2 pb-2 text-end"><strong x-text="formatRupiah(tarifAkhir)"></strong></td>
                            </tr>
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
