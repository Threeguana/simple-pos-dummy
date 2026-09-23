<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;


class Transactions extends Model
{
    public function details(): HasMany{
        return $this -> hasMany(TransactionsDetail::class);
    }

    public function user(): BelongsTo{
        return $this -> belongsTo(User::class);
    }
}
?>
