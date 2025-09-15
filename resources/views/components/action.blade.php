<div>
    <div class="btn-group btn-group-sm">
        @if ($delete)
            <a href="javascript:;" wire:click="delete('{{ $row['id'] }}')" wire:loading.remove style="display: none"
                class="delete{{ $row['id'] }} delete btn btn-warning">Hapus</a>
        @endif
        @if ($permanentDelete)
            <a href="javascript:;" wire:click="permanentDelete('{{ $row['id'] }}')" wire:loading.remove
                style="display: none" class="delete{{ $row['id'] }} delete btn btn-danger">Hapus Permanen</a>
        @endif
        <a href="javascript:;" onclick="deleteOrCancel('{{ $row['id'] }}')" wire:loading.remove style="display: none"
            class="delete{{ $row['id'] }} delete btn btn-secondary">Batal</a>
        @if ($edit)
            <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/form/{{ $row['id'] }}'"
                class="btn btn-white action">
                <i class="fas fa-pencil-alt"></i>
            </a>
        @endif
        <a href="javascript:;" class="btn btn-white dropdown-toggle w-30px no-caret action" x-init="[...document.querySelectorAll('.dropdown-toggle')].map((dropdownToggleEl) => new bootstrap.Dropdown(
            dropdownToggleEl, {
                popperConfig(defaultBsPopperConfig) {
                    return {
                        ...defaultBsPopperConfig,
                        strategy: 'fixed'
                    };
                }
            }))"
            data-bs-toggle="dropdown">
            <span class="caret"></span>
        </a>
        <div class="dropdown-menu dropdown-menu-end">
            @if ($print)
                <a href="javascript:;" wire:click="print('{{ $row['id'] }}')" x-init="$($el).on('click', function() {
                    setTimeout(() => {
                        $('#modal-cetak').modal('show')
                    }, 1000)
                })"
                    wire:loading.remove class="dropdown-item">Cetak</a>
            @endif
            @if ($restore)
                <a href="javascript:;" wire:click="restore('{{ $row['id'] }}')" wire:loading.remove class="dropdown-item">Restore</a>
            @endif
            @if ($detail)
                <a href="javascript:window.location.href=window.location.href.split('?')[0] + '/detail/{{ $row['id'] }}'"
                    wire:loading.remove class="dropdown-item">Detail</a>
            @endif
            @if ($delete)
                <a href="javascript:;" onclick="deleteOrCancel('{{ $row['id'] }}')" wire:loading.remove class="dropdown-item">Hapus</a>
            @endif
            @if ($custom)
                {!! $custom !!}
            @endif
            @if ($information)
                <li>
                    <hr class="dropdown-divider">
                </li>
                <a href="javascript:;"
                    class="dropdown-item fs-8px">{{ $row->pengguna?->nama }}<br>{{ $row->updated_at }}</a>
            @endif
        </div>
    </div>
</div>
