<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Account;
use App\Transaction;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // @TODO move to repo
        $account = Account::query()->where('user_id', '=', auth()->user()->id)->first();

        // @TODO move to repo
        $transactions = Transaction::query()->where('account_id', '=', $account->id)->get();
        return view('home', ['account' => $account, 'transactions' => $transactions]);
    }


}
