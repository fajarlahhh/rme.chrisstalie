<div>
    @section('title', 'Jurnal Keuangan')

    @section('breadcrumb')
        <li class="breadcrumb-item">Jurnal Keuangan</li>
        <li class="breadcrumb-item active">Tambah</li>
    @endsection

    <h1 class="page-header">Jurnal Keuangan <small>Tambah</small></h1>

    @livewire('jurnalkeuangan.' . $jenis, ['data' => $data])
    
    <div wire:loading>
        <x-loading />
    </div>
</div>
