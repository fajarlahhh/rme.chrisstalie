<div>
    @section('title', 'Unsur Gaji')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Unsur Gaji</li>
    @endsection

    <h1 class="page-header">Unsur Gaji</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">
            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">Unit Bisnis</label>
                    <select class="form-control" wire:model.live="unit_bisnis" data-width="100%">
                        <option hidden selected>-- Pilih Unit Bisnis --</option>
                        @foreach (\App\Enums\KantorEnum::cases() as $item)
                            <option value="{{ $item->value }}">{{ $item->label() }}</option>
                        @endforeach
                    </select>
                    @error('unit_bisnis')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                @if ($unit_bisnis)
                    <div class="note alert-success mb-2">
                        <div class="note-content">
                            <table class="table table-borderless">
                                <thead>
                                    <tr>
                                        <th>Unsur Gaji</th>
                                        <th class="w-50px">Sifat</th>
                                        <th>Kode Akun</th>
                                        <th class="w-5px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach ($unsurGaji as $index => $row)
                                        <tr>
                                            <td class="with-btn">
                                                <input type="text" class="form-control"
                                                    wire:model.live="unsurGaji.{{ $index }}.nama"
                                                    autocomplete="off">
                                                @error('unsurGaji.' . $index . '.nama')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="with-btn">
                                                <select class="form-control"
                                                    wire:model.live="unsurGaji.{{ $index }}.sifat">
                                                    <option value="+">+</option>
                                                    <option value="-">-</option>
                                                </select>
                                                @error('unsurGaji.' . $index . '.sifat')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="with-btn">
                                                <select class="form-control"
                                                    wire:model.live="unsurGaji.{{ $index }}.kode_akun_id">
                                                    <option value="">-- Pilih Kode Akun --</option>
                                                    @foreach ($dataKodeAkun as $subRow)
                                                        <option value="{{ $subRow['id'] }}">
                                                            {{ $subRow['nama'] }}
                                                        </option>
                                                    @endforeach
                                                </select>
                                                @error('unsurGaji.' . $index . '.kode_akun_id')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </td>
                                            <td class="with-btn">
                                                <a href="javascript:;" class="btn btn-danger"
                                                    wire:click="hapusBarang({{ $index }})">
                                                    <i class="fa fa-times"></i>
                                                </a>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="5">
                                            <div class="text-center">
                                                <a class="btn btn-secondary" href="javascript:;"
                                                    wire:click="tambahUnsurGaji">Tambah
                                                    Unsur Gaji</a>
                                                <br>
                                                @error('unsurGaji')
                                                    <span class="text-danger">{{ $message }}</span>
                                                @enderror
                                            </div>
                                        </td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                @endif
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor')
                    <input wire:loading.remove type="submit" value="Simpan" class="btn btn-success" />
                @endrole
            </div>
        </form>
    </div>

    <x-alert />
</div>

{{-- @push('scripts') --}}
{{-- <script>
        document.addEventListener('livewire:init', () => {
            Livewire.on('calculation', (data) => {
                calculation(data.index, data.harga);
            });
        });

        function calculation(index, harga = null) {
            let harga = harga ?? document.getElementById('barang-harga-' + index).value.replace(/\,/g, '');
            let qty = document.getElementById('barang-qty-' + index).value;
            let discount = document.getElementById('barang-discount-' + index).value;
            let total = (harga * qty) - (harga * qty * discount / 100);
            document.getElementById('barang-total-' + index).value = numberFormat(total);
        }
    </script> --}}
{{-- @endpush --}}
