<div>
    @section('title', 'Informasi Barang Dagang')

    @section('breadcrumb')
        <li class="breadcrumb-item">Informasi</li>
        <li class="breadcrumb-item active">Barang Dagang</li>
    @endsection

    <h1 class="page-header">Informasi Barang Dagang</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading">
            <div wire:ignore class="w-100">
                <select class="form-control" x-init="$($el).select2({
                    width: '100%',
                    dropdownAutoWidth: true,
                    placeholder: 'Ketik Nama Barang',
                    minimumInputLength: 1,
                    dataType: 'json',
                    ajax: {
                        url: '/cari/barang',
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
                    $wire.set('barangId', $($el).val());
                });">
                </select>
            </div>
        </div>
        <div class="panel-body table-responsive">
            <x-alert />
            @if ($dataBarang)
                <div class="row">
                    <div class="col-md-6">
                        <div class="note alert-primary mb-2">
                            <div class="note-content">
                                <h5>Data Barang Dagang</h5>
                                <hr>
                                <table class="w-100">
                                    <tr>
                                        <td>Nama</td>
                                        <td class="w-10px">:</td>
                                        <td>{{ $dataBarang->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td>Persediaan</td>
                                        <td class="w-10px">:</td>
                                        <td>{{ $dataBarang->persediaan }}</td>
                                    </tr>
                                    <tr>
                                        <td>Kategori</td>
                                        <td class="w-10px">:</td>
                                        <td>{{ $dataBarang->kodeAkun?->nama }}</td>
                                    </tr>
                                    <tr>
                                        <td>KFA</td>
                                        <td class="w-10px">:</td>
                                        <td>{{ $dataBarang->kfa }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            {!! $dataBarang->perlu_resep == 1 ? '<span class="badge bg-warning">Perlu Resep</span>' : '' !!}
                                            {!! $dataBarang->khusus == 1 ? '<span class="badge bg-danger">Barang Dagang Khusus</span>' : '' !!}
                                        </td>
                                    </tr>
                                    <tr>
                                        <td colspan="3">
                                            <hr>
                                        </td>
                                    </tr>
                                    <tr>
                                        <th>Stok</th>
                                        <th>:</th>
                                        <th>{{ $dataBarang->stokTersedia->count() / $dataBarang->barangSatuanUtama->rasio_dari_terkecil }}
                                            {{ $dataBarang->barangSatuanUtama?->nama }}
                                            {{ $dataBarang->barangSatuanUtama->konversi_satuan }}</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-bordered table-hover">
                            <tr>
                                <th class="bg-secondary-subtle">No.</th>
                                <th class="bg-secondary-subtle">Satuan</th>
                                <th class="bg-secondary-subtle">Harga Jual</th>
                                <th class="bg-secondary-subtle">Status</th>
                            </tr>
                            @foreach ($dataBarang->barangSatuan as $index => $row)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $row->nama }}</td>
                                    <td>{{ number_format($row->harga_jual) }}</td>
                                    <td>{!! $row->rasio_dari_terkecil == 1
                                        ? '<span class="badge bg-success">Terkecil</span>'
                                        : '<span class="badge bg-warning">' . $row->konversi_satuan . '</span>' !!}
                                        {!! $row->utama == 1 ? '<span class="badge bg-info">Utama</span>' : '' !!}
                                    </td>
                                </tr>
                            @endforeach
                        </table>
                    </div>
                </div>
            @endif
        </div>
    </div>
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
