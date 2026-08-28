<?php

namespace App\Models\Concerns;

use App\Support\ArabicNormalizer;

/**
 * Keeps fullname_normalized/phone_normalized in sync with fullname/phone
 * so they can never drift. Guarded by isDirty()/wasChanged() so a plain
 * write that doesn't touch fullname/phone (e.g. a payment updating totals)
 * doesn't pay the normalization cost.
 */
trait HasNormalizedSearch
{
    public static function bootHasNormalizedSearch(): void
    {
        static::saving(function (self $model) {
            if (!$model->exists || $model->isDirty('fullname')) {
                $model->fullname_normalized = ArabicNormalizer::name($model->fullname ?? '');
            }

            if (!$model->exists || $model->isDirty('phone')) {
                $model->phone_normalized = implode('/', ArabicNormalizer::phones($model->phone ?? ''));
            }
        });
    }
}
