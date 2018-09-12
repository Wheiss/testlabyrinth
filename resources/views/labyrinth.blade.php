<head>
    <link href="/css/styles.css" rel="stylesheet"/>
</head>
<body>
@for($vIndex = $height; $vIndex >= 0; $vIndex-- )
    <?php ($line = $labyrinth->getLine($vIndex)) ?>
    @foreach($line as $node)
        @php
            $class = '';
            foreach (\App\Classes\Position::SIDES as $side) {
                if(is_a($node->{$side}, \App\Classes\Wall::class)) {
                    $class .= ' cell-' . $side;
                } elseif (is_a($node->{$side}, \App\Classes\Door::class)) {
                    $class .= ' cell-door cell-' . $side . '-door';
                }
            }
        @endphp
        <div class="cell {{ $class }}"></div>
    @endforeach
    <br>
@endfor
</body>