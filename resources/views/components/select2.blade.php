<div>
    <select class="form-control select2" x-init="window.livewireSelect2 = window.livewireSelect2 || [];
    window.livewireSelect2[{{ $index }}] = $($el).select2({
        width: '100%',
        templateResult: function(data) {
            if (!data.id) { return data.text; }
            var $result = $('<span></span>');
            $result.text(data.text);
            if ($(data.element).data('subtext')) {
                $result.append('<span class=\'text-muted ms-2\' style=\'font-size: 0.9em;\'>(' + $(data.element).data('subtext') + ')</span>');
            }
            return $result;
        },
        templateSelection: function(data) {
            if (!data.id) { return data.text; }
            var $result = $('<span></span>');
            $result.text(data.text);
            if ($(data.element).data('subtext')) {
                $result.append('<span class=\'text-muted ms-2\' style=\'font-size: 0.9em;\'>(' + $(data.element).data('subtext') + ')</span>');
            }
            return $result;
        }
    });
    $($el).on('change', function(e) { $wire.set('{{ lcfirst($jenis) }}.{{ $index }}.{{ $key }}', $(this).val()); });" wire:ignore data-index="{{ $index }}" @if (isset($row['{{ $key }}']))
        data-selected="{{ $row[$key] }}"
        @endif>
        <option value="">-- Pilih {{ $jenis }} --</option>
        @foreach ($data as $subRow)
            <option value="{{ $subRow[$key] }}" data-subtext="{{ $subRow[$subtext] }}"
                @if (isset($row[$key]) && $row[$key] == $subRow[$key]) selected @endif>
                {{ $subRow[$label] }}
            </option>
        @endforeach
    </select>
</div>
