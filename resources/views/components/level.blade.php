<div>
    @switch($level)
        @case('Bronze')
            <span class="badge" style="background-color: #A0522D; color: #fff;">Bronze</span>
        @break

        @case('Silver')
            <span class="badge" style="background-color: #b3b3b3; color: #fff;">Silver</span>
        @break

        @case('Gold')
            <span class="badge" style="background-color: #c19223; color: #fff;">Gold</span>
        @break

        @case('Diamond')
            <span class="badge" style="background-color: #a0ebf7; color: #000000;">Diamond</span>
        @break

        @default
    @endswitch

</div>
