<div>
    @section('title', 'Unsur Gaji')

    @section('breadcrumb')
        <li class="breadcrumb-item">Data Master</li>
        <li class="breadcrumb-item active">Unsur Gaji</li>
    @endsection

    <h1 class="page-header">Unsur Gaji</h1>
    <ul class="nav nav-tabs" role="tablist">
        @foreach (\App\Enums\UnitBisnisEnum::cases() as $key => $item)
            <li class="nav-item" role="presentation" wire:ignore>
                <a href="#default-tab-{{ $key }}" data-bs-toggle="tab"
                    class="nav-link {{ $key == 0 ? 'active' : '' }}" aria-selected="true" role="tab">
                    <span class="d-sm-none">{{ $item->label() }}</span>
                    <span class="d-sm-block d-none">{{ $item->label() }}</span>
                </a>
            </li>
        @endforeach
    </ul>
    <div class="tab-content panel rounded-0 p-3 m-0">
        @foreach (\App\Enums\UnitBisnisEnum::cases() as $key => $item)
            <div class="tab-pane fade {{ $key == 0 ? 'active show' : '' }}" id="default-tab-{{ $key }}"
                role="tabpanel" wire:ignore.self>
                <form wire:submit.prevent="submit('{{ $item->value }}')">
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
                            @foreach (collect($unsurGaji)->where('unit_bisnis', $item->value) as $index => $row)
                                <tr>
                                    <td class="with-btn">
                                        <input type="text" class="form-control"
                                            wire:model.live="unsurGaji.{{ $index }}.nama" autocomplete="off">
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
                                                    {{ $subRow['id'] }} - {{ $subRow['nama'] }}
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
                                            wire:click="tambahUnsurGaji('{{ $item->value }}')">Tambah
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
                    @role('administrator|supervisor')
                        <input wire:loading.remove type="submit" value="Simpan" class="btn btn-success" />
                    @endrole
                </form>
            </div>
        @endforeach
        <!-- END tab-pane -->
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
