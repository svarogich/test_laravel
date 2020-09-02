<?php declare(strict_types=1);

namespace App\Http\Controllers;

use App\Account;
use App\Dto\TransactionRequest;
use App\Helpers\Amount;
use App\Http\Middleware\VerifyCsrfToken;
use App\Http\Requests\TransactionRequest as TransactionHttpRequest;
use App\Managers\TransactionExecutor;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Support\Facades\Log;

class TransactionController extends Controller
{
    /**
     * @var TransactionExecutor
     */
    private $transactionExecutor;

    public function __construct(TransactionExecutor $transactionExecutor)
    {
        $this->transactionExecutor = $transactionExecutor;
    }

    /**
     * @return Renderable
     */
    public function index()
    {
        $account = Account::query()
            ->where('user_id', '=', auth()->user()->id)
            ->firstOrFail();
        $counterpartyAccounts = Account::query()
            ->where('user_id', '<>', auth()->user()->id)
            ->get();

        return view('transaction', ['account' => $account, 'counterpartyAccounts' => $counterpartyAccounts]);
    }

    /**
     * @param TransactionHttpRequest $request
     * @return Renderable
     */
    public function execute(TransactionHttpRequest $request)
    {
        session()->regenerate();
        $successMessage = '';
        try {
            foreach ($request->get('counterparty') as $item) {
                /** @var Account $account */
                $account = Account::query()
                    ->where('user_id', '=', auth()->user()->id)
                    ->firstOrFail();

                /** @var Account $counterpartyAccount */
                $counterpartyAccount = Account::query()
                    ->where('id', '=', $item)
                    ->firstOrFail();

                $transactionRequest = new TransactionRequest($account,
                                                             Amount::majorToMinor($request->get('amount')),
                                                             $request->get('currency'),
                                                             $counterpartyAccount,
                                                             $request->get('description'));
                $debitTransaction = $this->transactionExecutor->execute($transactionRequest);
                $successMessage .= sprintf("Transaction to account %s with amount %s %s successfully done id:%s\n",
                                           $counterpartyAccount->id,
                                           $request->get('amount'),
                                           $request->get('currency'),
                                           $debitTransaction
                );
            }
        } catch (\Throwable $t) {

            Log::debug('Exception', ['exception' => $t]);
            return redirect()->route('transaction')->with('status-danger', 'Error, TODO normal error text');
        }

        return redirect()->route('home')->with('status', $successMessage);
    }

    /**
     * Debug method to make money from air
     *
     * @return Renderable
     */
    public function addBalance()
    {
        $account = Account::query()->where('user_id', '=', auth()->user()->id)->firstOrFail();
        $account->value += 100;
        $account->save();


        return redirect()->route('home');
    }

}
