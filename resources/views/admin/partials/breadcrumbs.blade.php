<div style="background-color: #dadde1; color: #1b2c41; width: calc(100% + 2em); padding: 0.5em 1em; margin: -0.75em 0em 1em -1em;">
    @if(count($links))
        <a href="/admin">Dashboard</a>  <i class="fas fa-angle-right" style="font-size: 12px;"></i>
    @else
        Dashboard
    @endif
    
    @foreach($links as $pageName => $url)
        @if($loop->last)
            {{ $pageName }}
        @else
            <a href="{{ $url }}">{{ $pageName }}</a> <i class="fas fa-angle-right" style="font-size: 12px;"></i>
        @endif
    @endforeach
</div>