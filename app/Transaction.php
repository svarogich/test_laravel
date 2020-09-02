<?php declare(strict_types=1);

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Transaction extends Model
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'account_id',
        'counterparty_account_id',
        'amount',
        'description',
        'currency',
        'type',
    ];

    /**
     * @return BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(Account::class);
    }
}
