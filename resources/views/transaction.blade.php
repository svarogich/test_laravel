@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Make transaction form</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('status-danger'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('status-danger') }}
                            </div>
                        @endif

                            <form method="POST" action="{{ route('executeTransaction') }}">
                            @csrf

                            <div class="form-group">
                                <label for="amount">Amount</label>
                                <input id="amount" name="amount" type="number" min="0.01" step="any"
                                       value="{{old('amount')}}"
                                       class="form-control @error('amount') is-invalid @enderror" required>
                                @error('amount')
                                <div class="alert alert-danger">Invalid amount</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="currency">Currency</label>
                                <input id="currency" name="currency" type="text"
                                       class="form-control @error('currency') is-invalid @enderror"
                                       value="{{ $account->currency }}" readonly required>
                                @error('currency')
                                <div class="alert alert-danger">Invalid currency</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="counterparty">Counterparty (account number)</label>
                                <select multiple  name="counterparty[]" class="form-control @error('currency') is-invalid @enderror"
                                        id="counterparty" required>
                                    @foreach ($counterpartyAccounts as $counterpartyAccount)
                                        <option value="{{ $counterpartyAccount->id }}"
                                            {{ (collect(old('options'))->contains($counterpartyAccount)) ? 'selected':'' }}
                                        >{{ $counterpartyAccount->id }}</option>
{{--  @TODO addrelated name--}}
                                    @endforeach
                                </select>
                                @error('counterparty')
                                <div class="alert alert-danger">Invalid counterparty</div>
                                @enderror
                            </div>
                            <div class="form-group">
                                <label for="description">Description</label>
                                <input id="description" name="description" type="text"
                                       value="{{old('description')}}"
                                       class="form-control @error('description') is-invalid @enderror" required>
                                @error('description')
                                <div class="alert alert-danger">Invalid description</div>
                                @enderror
                            </div>
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
