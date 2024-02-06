@if(count($messages))
    @foreach($messages as $message)
        <div class="w-full mt-5 container max-w-7xl mx-auto px-5 lg:px-40 space-y-5">
            <div class="alert alert-{{ $message['level'] }}">{{ $message['message'] }}</div>
        </div>
    @endforeach
@endif
