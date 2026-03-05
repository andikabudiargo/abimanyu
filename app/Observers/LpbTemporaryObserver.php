<?php

namespace App\Observers;

use App\Jobs\RecalculateArticlePricing;

class LpbTemporaryObserver
{
    public function saved($model): void
    {
        RecalculateArticlePricing::dispatch($model->article_code)
            ->onQueue('pricing');
    }

    public function deleted($model): void
    {
        RecalculateArticlePricing::dispatch($model->article_code)
            ->onQueue('pricing');
    }
}