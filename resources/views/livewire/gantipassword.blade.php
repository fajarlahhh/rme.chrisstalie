<div>
    @section('title', 'Ganti Password')

    @section('breadcrumb')
        <li class="breadcrumb-item active">Ganti Password</li>
    @endsection

    <h1 class="page-header">Ganti Password</h1>

    <div class="panel panel-inverse" data-sortable-id="form-stuff-1">
        <!-- begin panel-heading -->
        <div class="panel-heading ui-sortable-handle">

            <h4 class="panel-title">Form</h4>
        </div>
        <form wire:submit.prevent="submit">
            <div class="panel-body">
                <div class="mb-3">
                    <label class="form-label">UID</label>
                    <input class="form-control" type="uid" value="{{ auth()->user()->uid }}" disabled />
                    @error('uid')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input class="form-control" type="text"
                        value="{{ auth()->user()->nama }}"
                        disabled />
                    @error('nama')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <hr>
                <div class="mb-3">
                    <label class="form-label">Password Lama</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="oldPassword" wire:model="oldPassword" required
                            autocomplete="off" />
                        <span class="input-group-text" id="toggleOldPassword" style="cursor: pointer;">
                            <i class="fas fa-eye" id="toggleIconOldPassword"></i>
                        </span>
                    </div>
                    @error('oldPassword')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input type="password" class="form-control" id="newPassword" wire:model="newPassword"
                            autocomplete="off" />
                        <span class="input-group-text" id="toggleNewPassword" style="cursor: pointer;">
                            <i class="fas fa-eye" id="toggleIconNewPassword"></i>
                        </span>
                    </div>
                    @error('newPassword')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <div class="panel-footer">
                @role('administrator|supervisor|operator')
                    <button type="submit" class="btn btn-success" wire:loading.attr="disabled">
                        <span wire:loading class="spinner-border spinner-border-sm"></span>
                        Simpan
                    </button>
                @endrole
            </div>
        </form>
    </div>

    <x-alert />

</div>
@push('scripts')
    <script>
        document.getElementById('toggleOldPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('oldPassword');
            const toggleIcon = document.getElementById('toggleIconOldPassword');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-low-vision');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-low-vision');
                toggleIcon.classList.add('fa-eye');
            }
        });
        document.getElementById('toggleNewPassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('newPassword');
            const toggleIcon = document.getElementById('toggleIconNewPassword');
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.classList.remove('fa-eye');
                toggleIcon.classList.add('fa-eye-low-vision');
            } else {
                passwordInput.type = 'password';
                toggleIcon.classList.remove('fa-eye-low-vision');
                toggleIcon.classList.add('fa-eye');
            }
        });
    </script>
@endpush
