@extends('layouts.app')

@section('content')
<div class="container">
  <h3>Authorize {{ $client->name ?? 'Application' }}</h3>
  <p>The application requests the following permissions:</p>
  <form method="POST" action="{{ route('auth.consent.approve') }}">
    @csrf
    <input type="hidden" name="client_id" value="{{ $client->id }}">
    @foreach($scopes as $scope)
      <div>
        <label>
          <input type="checkbox" name="scopes[]" value="{{ $scope }}" checked> {{ $scope }}
        </label>
      </div>
    @endforeach

    <button type="submit" class="btn btn-primary">Approve</button>
  </form>

  <form method="POST" action="{{ route('auth.consent.deny') }}">
    @csrf
    <input type="hidden" name="client_id" value="{{ $client->id }}">
    <button type="submit" class="btn btn-link">Deny</button>
  </form>
</div>
@endsection
