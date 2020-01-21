@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-7">
            @if ($message = Session::get('success'))
            <div class="alert alert-success alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
            @if ($message = Session::get('error'))
            <div class="alert alert-danger alert-block">
                <button type="button" class="close" data-dismiss="alert">×</button>
                <strong>{{ $message }}</strong>
            </div>
            @endif
            <ul class="list-group list-unstyled">
                <li class="list-group-item">
                    <h5 class="m-0">Withdraw Money</h5>
                </li>
                <li class="list-group-item">
                    <div class="row">
                        <div class="col-12">
                            <form method="POST" action="{{ route('saveWithdraw') }}">
                                @csrf
                                <div class="form-group">
                                    <label for="" class="col-form-label text-md-right font-weight-bold mx-3">Amount</label>
                                    <div class="col-md-12">
                                        <input type="hidden" name="user_id" value="{{ Auth::user()->id }}">
                                        <input id="amount" min="1" placeholder="Enter amount to withdraw" step="0.1" type="number" class="form-control @error('amount') is-invalid @enderror" name="amount" value="{{ old('amount') }}" required autocomplete="amount" autofocus>

                                        @error('amount')
                                        <span class="invalid-feedback" role="alert">
                                            <strong>{{ $message }}</strong>
                                        </span>
                                        @enderror
                                    </div>
                                </div>
                                <div class="form-group">
                                    <div class="col-md-12">
                                        <button type="submit" class="btn btn-block btn-primary">Withdraw</button>

                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </li>
            </ul>
        </div>
    </div>
</div>
@endsection