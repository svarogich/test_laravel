@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">  {{ __('You are logged in!') }}</div>

                    <div class="card-body">
                        @if (session('status'))
                            <div class="alert alert-success" role="alert">
                                {{ session('status') }}
                            </div>
                        @endif
                        @if (session('status-alert'))
                            <div class="alert alert-danger" role="alert">
                                {{ session('status-danger') }}
                            </div>
                        @endif

                        @if($account)
                            <p>
                                My account number: {{ $account->id  }}
                            </p>
                            <p>
                                My
                                balance: {{ \App\Helpers\Amount::minorToMajor($account->balance)  }} {{ $account->currency  }}
                                <a class="float-right" href="{{ route('addBalance') }}">Add money (debug)</a>
                            </p>
                            <div>
                                <a href="{{ route('transaction') }}" class="btn btn-primary my-2">Make transaction</a>
                            </div>
                        @else
                            <div>
                                <a href="{{ route('newAccount') }}" class="btn btn-primary my-2">Create and assign
                                    account</a>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header">Transaction</div>

                    <div class="card-body">

{{--                        <div>--}}
{{--                            filters @ TODO--}}
{{--                        </div>--}}
                        <table class="table table-striped table-sm">
                            <thead>
                            <tr>
                                <th>ID</th>
                                <th>Amount</th>
                                <th>Currency</th>
                                <th>Counterparty account number</th>
                                <th>Description</th>
                                <th>Date</th>
                            </tr>
                            </thead>
                            <tbody>

                            @foreach ($transactions as $transaction)
                                <tr>
                                    <td>{{$transaction->id}}</td>
                                    <td class="
                                    @if (\App\Managers\TransactionExecutor::TYPE_CREDIT === $transaction->type) text-success
                                    @elseif (\App\Managers\TransactionExecutor::TYPE_DEBIT === $transaction->type) text-danger
                                    @endif
                                        ">
                                        @if (\App\Managers\TransactionExecutor::TYPE_DEBIT === $transaction->type)
                                            -  @endif
                                        {{ \App\Helpers\Amount::minorToMajor($transaction->amount) }}</td>
                                    <td>{{$transaction->currency}}</td>
                                    <td>{{$transaction->counterparty_account_id}}</td>
                                    <td>{{$transaction->description}}</td>
                                    <td>{{$transaction->created_at}}</td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
