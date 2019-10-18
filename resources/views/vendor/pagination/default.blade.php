@if ($paginator->hasPages())
    <ul>
        @if ($paginator->onFirstPage())
            <li><a href="" class="disable">&lt;前页</a></li>
        @else
            <li><a href="{{ $paginator->previousPageUrl() }}">&lt;前页</a></li>
        @endif

        @foreach ($elements as $element)
            @if (is_string($element))
                <li class="disable"><span>{{ $element }}</span></li>
            @endif
            @if (is_array($element))
                @foreach ($element as $page => $url)
                    @if ($page == $paginator->currentPage())
                        <li><a href="" class="active">{{ $page }}</a></li>
                    @else
                        <li><a href="{{ $url }}">{{ $page }}</a></li>
                    @endif
                @endforeach
            @endif
        @endforeach
        @if ($paginator->hasMorePages())
            <li><a href="{{ $paginator->nextPageUrl() }}">后页&gt;</a></li>
        @else
            <li><a href="" class="disable">后页&gt;</a></li>
        @endif
        <li>(共{{ $paginator->total() }}个结果)</li>
    </ul>
@endif