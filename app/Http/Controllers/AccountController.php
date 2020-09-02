<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Account;
use App\Helpers\Currencies;

class AccountController extends Controller
{
    /**
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function new()
    {

        // @TODO move to some factory
        $account = new Account();

        // no getters/setters rly?!
        $account->currency = Currencies::EUR;
        $account->user_id = auth()->user()->id;
        $account->value = 0;

        $account->save();

        return redirect()->route('home');
    }

}
