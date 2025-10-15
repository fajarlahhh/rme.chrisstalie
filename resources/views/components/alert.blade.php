<div>
    @if (Session::has('danger'))
        <div class="alert alert-danger alert-dismissible fade show h-100 mb-3 mt-3">
            {!! Session::get('danger') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (Session::has('warning'))
        <div class="alert alert-warning alert-dismissible fade show h-100 mb-3 mt-3">
            {!! Session::get('warning') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (Session::has('info'))
        <div class="alert alert-info alert-dismissible fade show h-100 mb-3 mt-3">
            {!! Session::get('info') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
    @if (Session::has('success'))
        <div class="alert alert-success alert-dismissible fade show h-100 mb-3 mt-3">
            {!! Session::get('success') !!}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger alert-dismissible fade show h-100 mb-3 mt-3">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif
</div>
