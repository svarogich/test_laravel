<?php declare(strict_types=1);

namespace App\Dto;

use App\Account;

class TransactionRequest
{
    /**
     * @var int
     */
    private $amount;

    /**
     * @var string
     */
    private $currency;

    /**
     * @var string
     */
    private $description;

    /**
     * @var Account
     */
    private $account;

    /**
     * @var Account
     */
    private $counterpartyAccount;

    public function __construct(Account $account, int $amount, string $currency, Account $counterpartyAccount, string $description = '')
    {
        $this->account = $account;
        $this->amount = $amount;
        $this->currency = $currency;
        $this->counterpartyAccount = $counterpartyAccount;
        $this->description = $description;
    }

    /**
     * @return int
     */
    public function getAmount(): int
    {
        return $this->amount;
    }

    /**
     * @return string
     */
    public function getCurrency(): string
    {
        return $this->currency;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @return Account
     */
    public function getAccount(): Account
    {
        return $this->account;
    }

    /**
     * @return Account
     */
    public function getCounterpartyAccount(): Account
    {
        return $this->counterpartyAccount;
    }
}
