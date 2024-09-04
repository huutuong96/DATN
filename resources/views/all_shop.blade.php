<div>
    <h2>Danh sách cửa hàng</h2>
    <ul>
        @foreach($shop as $s)
            <li>
                <a href="{{ url('chat/' . $s->id) }}">{{ $s->name }}</a>
            </li>
        @endforeach
    </ul>
</div>
