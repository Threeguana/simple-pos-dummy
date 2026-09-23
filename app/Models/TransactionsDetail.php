<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class TransactionsDetail extends Model
{
    public function transaction(): BelongsTo{
        return $this -> belongsTo(Transactions::class);
    }

    public function product(): BelongsTo{
        return $this -> belongsTo(Product::class);
    }
}
?>
