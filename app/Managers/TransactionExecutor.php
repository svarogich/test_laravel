<?php declare(strict_types=1);

namespace App\Managers;

use App\Account;
use App\Dto\TransactionRequest;
use App\Exceptions\TransactionException;
use App\Transaction;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TransactionExecutor
{
    const TYPE_DEBIT = 'debit'; // from account
    const TYPE_CREDIT = 'credit'; // to account

    /**
     * @param TransactionRequest $request
     * @return int
     * @throws TransactionException
     */
    public function execute(TransactionRequest $request)
    {
        $this->checkCurrencies($request->getAccount(), $request->getCounterpartyAccount(), $request->getCurrency());
        $this->checkIsFundsEnough($request->getAccount(), $request->getAmount());

        // Make debit Transaction
        $debitTransactionId =
            $this->makeTransaction($request->getAccount(),
                                   $request->getAmount(),
                                   $request->getCurrency(),
                                   self::TYPE_DEBIT,
                                   $request->getDescription(),
                                   $request->getCounterpartyAccount());
        try {
            // Check if account is not in overdraft

            $this->checkIsFundsEnough($request->getAccount(), 0);
        } catch (TransactionException $e) {
            // Need to make refund...
            $this->makeTransaction($request->getAccount(),
                                   $request->getAmount(),
                                   $request->getCurrency(),
                                   sprintf('Refund due to overdraft [%s]', $debitTransactionId),
                                   self::TYPE_CREDIT);
            throw new TransactionException('During transaction execution account went to the overdraft', $e);
        }

        // Make credit Transaction
        try {
            $this->makeTransaction($request->getCounterpartyAccount(),
                                   $request->getAmount(),
                                   $request->getCurrency(),
                                   self::TYPE_CREDIT,
                                   $request->getDescription(),
                                   $request->getAccount());
        } catch (TransactionException $e) {
            // Need to make refund...
            $this->makeTransaction($request->getAccount(),
                                   $request->getAmount(),
                                   $request->getCurrency(),
                                   self::TYPE_CREDIT,
                                   sprintf('Refund due to overdraft [%s]', $debitTransactionId));
            throw new TransactionException('Error during credit transaction',0, $e);
        }

        return $debitTransactionId;
    }

    /**
     * @param Account $account
     * @param Account $couterpartyAccount
     */
    private function checkCurrencies(Account $account, Account $couterpartyAccount, string $transactionCurrency)
    {
        if (
            $account->currency === $couterpartyAccount->currency
            && $couterpartyAccount->currency === $transactionCurrency
        ) {
            return;
        }
        Log::alert('Transaction cannot be made, currencies not equal',
                   [
                       'account_id' => $account->id,
                       'currrency' => $account->currency,
                       'counterparty_account_id' => $couterpartyAccount->id,
                       'counterparty_currrency' => $couterpartyAccount->currency,
                       'transaction_currency' => $transactionCurrency
                   ]);
        throw new TransactionException('Transaction cannot be made, currencies not equal');
    }

    /**
     * @param Account $account
     * @param int $amount
     * @param string $currency
     * @param string $type
     * @param string $description
     * @param Account $couterpartyAccount
     * @return int
     */
    private function makeTransaction(Account $account,
                                     int $amount,
                                     string $currency,
                                     string $type,
                                     string $description,
                                     ?Account $couterpartyAccount = null)
    {
        // @TODO factory
        $transaction = new Transaction();
        $transaction->account_id = $account->id;
        if (null !== $couterpartyAccount) {
            $transaction->counterparty_account_id = $couterpartyAccount->id;
        }
        $transaction->amount = $amount;
        $transaction->currency = $currency;
        $transaction->description = $description;
        $transaction->type = $type;

        switch ($type) {
            case self::TYPE_DEBIT:
                $account->balance -= $amount;
                break;
            case self::TYPE_CREDIT:
                $account->balance += $amount;
                break;
            default:
                Log::alert('Invalid type', ['type' => $type]);
                throw new TransactionException('Invalid type');
        }

        try {
            DB::beginTransaction();
            // laravel cant save only one field? @TODO check
            $account->save();
            $transaction->save();
            Db::commit();
        } catch (\Throwable $t) {
            Db::rollBack();

            //@TODO check if logger can handle object as context, or make it loggable/jsonnable?
            Log::alert('Error while making transaction',
                       [
                           'account_id' => $transaction->account_id,
                           'counterparty_account_id' => $transaction->counterparty_account_id,
                           'amount' => $transaction->amount,
                           'currency' => $transaction->currency,
                           'type' => $transaction->type,
                       ]
            );
            throw new TransactionException('Error while making transaction', 0, $t);
        }
        return $transaction->id;
    }

    /**
     * @param Account $account
     * @param int $amount
     */
    private function checkIsFundsEnough(Account $account, int $amount)
    {
        $account->refresh();
        if ($account->balance - $amount >= 0) {
            return;
        }

        Log::alert('Not enough founds',
                   [
                       'account_id' => $account->id,
                       'currrency' => $account->currency,
                       'available_amount' => $account->balance,
                       'amount' => $amount,
                   ]);

        throw new TransactionException('Not enough founds');
    }
}
