<div>
    @section('title', 'Kasir')

    @section('breadcrumb')
        <li class="breadcrumb-item">Klinik</li>
        <li class="breadcrumb-item">Kasir</li>
        <li class="breadcrumb-item active">Input</li>
    @endsection


    <h1 class="page-header">Kasir <small>Input</small></h1>

    <x-alert />

    <div class="note alert-primary mb-2">
        <div class="note-content">
            <h5>Data Pasien</h5>
            <hr>
            <table class="w-100">
                <tr>
                    <td class="w-200px">No. RM</td>
                    <td class="w-10px">:</td>
                    <td>{{ $data->pasien_id }}</td>
                </tr>
                <tr>
                    <td>Nama</td>
                    <td class="w-10px">:</td>
                    <td>{{ $data->pasien->nama }}</td>
                </tr>
                <tr>
                    <td>Usia</td>
                    <td class="w-10px">:</td>
                    <td>{{ $data->pasien->umur }} Tahun</td>
                </tr>
                <tr>
                    <td>Jenis Kelamin</td>
                    <td class="w-10px">:</td>
                    <td>{{ $data->pasien->jenis_kelamin }}</td>
                </tr>
            </table>
        </div>
    </div>
    <form wire:submit.prevent="submit">
        <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
            <div class="panel-heading ui-sortable-handle">
                <h4 class="panel-title">Form</h4>
            </div>
            <div class="panel-body">
                <table class="table table-striped p-0">
                    <thead>
                        <tr>
                            <th>No.</th>
                            <th>Jenis</th>
                            <th>Item</th>
                            <th class=" w-150px">Diskon <small class="text-muted">(Rp.)</small></th>
                            <th class="bg-info-subtle text-end">Sub Total</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $no = 1;
                        @endphp
                        @foreach ($tindakan as $index => $row)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>Tindakan</td>
                                <td>{{ $row['nama'] }} <br> <b>
                                        <small class="text-muted">&nbsp;&nbsp;&nbsp;- Rp.
                                            {{ number_format($row['biaya'], 0, ',', '.') }} x
                                            {{ $row['qty'] }}</b><br>
                                    &nbsp;&nbsp;&nbsp;- Catatan : {{ $row['catatan'] }}
                                    <br>
                                    &nbsp;&nbsp;&nbsp;- Dokter &nbsp;&nbsp;&nbsp;: <select
                                        class="form-control @if ($errors->has('tindakan.' . $index . '.dokter_id')) is-invalid @endif "
                                        x-init="$($el).selectpicker({
                                            liveSearch: true,
                                            width: '300',
                                            size: 10,
                                            container: 'body',
                                            style: '',
                                            showSubtext: true,
                                            styleBase: 'form-control'
                                        })" wire:model="tindakan.{{ $index }}.dokter_id">
                                        <option value="">-- Pilih Dokter --</option>
                                        @foreach (collect($dataNakes)->where('dokter', 1)->toArray() as $nakes)
                                            <option value="{{ $nakes['id'] }}">{{ $nakes['nama'] }}</option>
                                        @endforeach
                                    </select> <br>
                                    &nbsp;&nbsp;&nbsp;- Perawat : <select
                                        class="form-control @if ($errors->has('tindakan.' . $index . '.perawat_id')) is-invalid @endif "
                                        x-init="$($el).selectpicker({
                                            liveSearch: true,
                                            width: '300',
                                            size: 10,
                                            container: 'body',
                                            style: '',
                                            showSubtext: true,
                                            styleBase: 'form-control'
                                        })" wire:model="tindakan.{{ $index }}.perawat_id">
                                        <option value="">-- Pilih Perawat --</option>
                                        @foreach (collect($dataNakes)->toArray() as $nakes)
                                            <option value="{{ $nakes['id'] }}">{{ $nakes['nama'] }}</option>
                                        @endforeach
                                    </select></small>
                                </td>
                                <td class="align-middle">
                                    <input type="number" class="form-control"
                                        wire:model.live="tindakan.{{ $index }}.diskon">
                                    @error('tindakan.' . $index . '.diskon')
                                        <span class="text-danger">{{ $message }}</span>
                                    @enderror
                                </td>
                                <th class="align-middle bg-info-subtle text-end">
                                    {{ number_format($row['biaya'] * $row['qty'] - $row['diskon'], 0, ',', '.') }}
                                </th>
                            </tr>
                        @endforeach
                        @foreach ($resep as $index => $row)
                            <tr>
                                <td>{{ $no++ }}</td>
                                <td>Resep Obat</td>
                                <td>
                                    Resep {{ $row['resep'] }} : {{ $row['catatan'] }} <br>
                                    <ul>
                                        @foreach ($row['barang'] as $y => $barang)
                                            <li>{{ $barang['nama'] }} ({{ $barang['qty'] }} {{ $barang['satuan'] }})
                                                x {{ number_format($barang['biaya'], 0, ',', '.') }}</li>
                                        @endforeach
                                    </ul>
                                </td>
                                <td class="align-middle">
                                    &nbsp;
                                </td>
                                <th class="align-middle bg-info-subtle text-end">
                                    {{ number_format(collect($row['barang'])->sum(fn($q) => $q['biaya'] * $q['qty']), 0, ',', '.') }}
                                </th>
                            </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr>
                            <th colspan="4" class="text-end">Total Tagihan</th>
                            <th class="bg-info-subtle text-end">
                                {{ number_format(collect($tindakan)->sum('biaya') * collect($tindakan)->sum('qty') - collect($tindakan)->sum('diskon') + collect($resep)->sum(fn($q) => collect($q['barang'])->sum(fn($q) => $q['biaya'] * $q['qty'])), 0, ',', '.') }}
                            </th>
                        </tr>
                    </tfoot>
                </table>
                <hr>
                <div class="note alert-secondary mb-2">
                    <div class="note-content">
                        <h4>Pembayaran</h4>
                        <hr>
                        <div class="mb-3">
                            <label class="form-label">Metode Bayar</label>
                            <select class="form-control" wire:model.lazy="metode" data-width="100%">
                                <option hidden selected>-- Pilih Metode Bayar --</option>
                                @foreach ($dataMetodeBayar as $item)
                                    <option value="{{ $item['nama'] }}">{{ $item['nama'] }}</option>
                                @endforeach
                            </select>
                            @error('metode')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        @if ($metode == 'Cash')
                            <div class="mb-3">
                                <label class="form-label">Cash</label>
                                <input class="form-control" type="number" wire:model="cash" id="cash" />
                                @error('cash')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Uang Kembali</label>
                                <input class="form-control text-end" type="text" disabled
                                    value="{{ number_format(($cash ?: 0) - (collect($tindakan)->sum('biaya') * collect($tindakan)->sum('qty') - collect($tindakan)->sum('diskon') + collect($resep)->sum(fn($q) => collect($q['barang'])->sum(fn($q) => $q['biaya'] * $q['qty'])))) }}" />
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Keterangan </label>
                                <input class="form-control" type="text" wire:model.live="keterangan" />
                                @error('keterangan')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                    </div>
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
                <button type="button" class="btn btn-warning m-r-3" wire:loading.attr="disabled"
                    onclick="window.location.href='/klinik/tindakan'">
                    <span wire:loading class="spinner-border spinner-border-sm"></span>
                    Data
                </button>
            </div>
        </div>
    </form>
    <x-alert />
</div>

@push('script')
    <script>
        $('#cash').on('change', function() {
            console.log($(this).val().replace(/\./g, '').replace(/,/g, ''));
        });
    </script>
@endpush
