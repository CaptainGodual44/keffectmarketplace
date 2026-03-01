@extends('layouts.app')

@section('content')
    <h1>Marketplace Home</h1>
    <p>Browse virtual goods, manage your account, and complete secure purchases in Linden Dollars (L$).</p>

    <div class="grid grid-3">
        @foreach($featuredProducts as $item)
            <div class="card">
                <h3>{{ $item['name'] }}</h3>
                <p><span class="badge">L${{ $item['price_l$'] }}</span></p>
            </div>
        @endforeach
    </div>
@endsection
