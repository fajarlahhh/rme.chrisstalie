<div>
    @section('title', (!$data->exists ? 'Tambah' : 'Edit') . ' Hak Akses')

    @section('breadcrumb')
        <li class="breadcrumb-item">Hak Akses</li>
        <li class="breadcrumb-item active" wire:ignore.self>{{ !$data->exists ? 'Tambah' : 'Edit' }}</li>
    @endsection

    <h1 class="page-header">Hak Akses <small>{{ !$data->exists ? 'Tambah' : 'Edit' }}</small></h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="row w-100">
                    <div class="col-md-4">
                        @if ($data->exists)
                            <div class="mb-3">
                                <label class="form-label">Nama</label>
                                @if (!$this->data->pegawai_id)
                                    <input class="form-control" type="text" wire:model="nama"
                                        @if ($data->exists) disabled @endif />
                                @else
                                    <input class="form-control" type="text" value="{{ $data->pegawai->nama }}"
                                        @if ($data->exists) disabled @endif />
                                @endif
                                @error('nama')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @else
                            <div class="mb-3">
                                <label class="form-label">Pegawai</label>
                                <select x-init="$($el).selectpicker({
                                    liveSearch: true,
                                    width: 'auto',
                                    size: 10,
                                    container: 'body',
                                    style: '',
                                    showSubtext: true,
                                    styleBase: 'form-control'
                                })" class="form-control" wire:model="pegawai_id"
                                    data-width="100%">
                                    <option selected value="">-- Tidak Ada Pegawai --</option>
                                    @foreach ($pegawaiData as $item)
                                        <option value="{{ $item['id'] }}">{{ $item['nama'] }}</option>
                                    @endforeach
                                </select>
                                @error('pegawai_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="form-label">UID</label>
                            <input class="form-control" type="uid" wire:model="uid"
                                @if ($data->exists) disabled @endif />
                            @error('uid')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Role</label>
                            <select data-container="body" class="form-control " wire:model="role"
                                x-init="$($el).on('change', function() {
                                    $wire.changeRole($($el).val())
                                })" data-width="100%">
                                <option selected hidden>-- Tidak Ada Role --</option>
                                @foreach ($dataRole as $row)
                                    <option value="{{ $row['name'] }}">{{ ucfirst($row['name']) }}</option>
                                @endforeach
                            </select>
                            @error('role')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                    </div>
                    <div class="col-md-8">
                        <div class="note alert-secondary mb-0">
                            <div class="note-content">
                                <h4><b>Hak Akses</b></h4>
                                @error('akses')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                                <hr>
                                <div class="row">
                                    @php
                                        function renderSub($data, $parent, $disabled)
                                        {
                                            $subMenu = '';
                                            foreach ($data as $i => $row) {
                                                $menu =
                                                    $parent .
                                                    str_replace([' ', '/', '\''], '', strtolower($row['title']));
                                                $subMenu .=
                                                    "<div class='form-check mt-1'><input type='checkbox' class='form-check-input' wire:model='hakAkses' id='" .
                                                    $menu .
                                                    "' '. $disabled .' value='" .
                                                    $menu .
                                                    "'/><label class='form-check-label' for='" .
                                                    $menu .
                                                    "'>" .
                                                    $row['title'] .
                                                    '</label>' .
                                                    (!empty($row['sub_menu'])
                                                        ? renderSub($row['sub_menu'], $menu, $disabled)
                                                        : '') .
                                                    '</div>';
                                            }
                                            return $subMenu;
                                        }
                                    @endphp
                                    @foreach (collect(config('sidebar.menu'))->sortBy('title')->all() as $subKey => $subRow)
                                        @php
                                            $menu = str_replace([' ', '/', '\''], '', strtolower($subRow['title']));
                                        @endphp
                                        <div class="col-lg-6 mb-3">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="{{ $menu }}" value="{{ $menu }}"
                                                    @if ((!empty($role) && (strpos($role, 'administrator') !== false || $role == '')) || empty($role)) disabled @endif autocomplete="on"
                                                    wire:model="hakAkses" />
                                                <label class="form-check-label" for="{{ $menu }}">
                                                    {{ $subRow['title'] }}
                                                </label>
                                                @if (!empty($subRow['sub_menu']))
                                                    {!! renderSub(
                                                        $subRow['sub_menu'],
                                                        $menu,
                                                        (!empty($role) && (strpos($role, 'administrator') !== false || $role == '')) || empty($role) ? 'disabled' : '',
                                                    ) !!}
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
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
                <a href="{{ $previous }}" class="btn btn-danger" wire:ignore>Batal</a>
            </div>
        </form>
    </div>

    <x-alert />

</div>
