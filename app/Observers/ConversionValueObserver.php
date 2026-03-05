<?php

namespace App\Observers;

use App\Jobs\RecalculateArticlePricing;

class ConversionValueObserver
{
    public function saved($model): void
    {
        // Konversi berubah = semua article harus hitung ulang
        RecalculateArticlePricing::dispatch(null)
            ->onQueue('pricing');
    }
}