<?php

namespace App\Queries;

use App\Models\Debt;
use App\Support\ArabicNormalizer;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;

/**
 * One query class for both debt types (regular / with-supplier), the type
 * applied as a filter rather than two parallel classes. Backs the search
 * card on all four debt list pages.
 */
final class DebtSearchQuery
{
    private const SELECT_COLUMNS = [
        'id', 'user_id', 'tractor_driver_id', 'fullname', 'phone', 'date_debut_debt',
        'total_debt_amount', 'debt_paid', 'rest_debt_amount', 'date_end_debt', 'status',
        'created_at', 'updated_at',
    ];

    private const MIN_NAME_TOKEN_LENGTH = 2;
    private const MIN_PHONE_LENGTH = 3;

    public function paginate(?string $name, ?string $phone, bool $unpaid, bool $forSupplier, int $perPage = 25): LengthAwarePaginator
    {
        $query = Debt::query()
            ->select(self::SELECT_COLUMNS)
            ->with($forSupplier ? ['tractorDriver', 'getDebtProduct', 'debtHistories'] : ['getDebtProduct', 'debtHistories'])
            ->when($forSupplier, fn (Builder $q) => $q->excludingTractorDriver(1), fn (Builder $q) => $q->forTractorDriver(1))
            ->when($unpaid, fn (Builder $q) => $q->unpaid(), fn (Builder $q) => $q->paid());

        $this->applyNameFilter($query, $name);
        $this->applyPhoneFilter($query, $phone);

        return $query->orderBy('id', 'desc')->paginate($perPage)->withQueryString();
    }

    private function applyNameFilter(Builder $query, ?string $name): void
    {
        $normalized = $name !== null ? ArabicNormalizer::name($name) : '';
        $tokens = array_filter(explode(' ', $normalized), fn ($token) => mb_strlen($token) >= self::MIN_NAME_TOKEN_LENGTH);

        if ($tokens === []) {
            return;
        }

        foreach ($tokens as $token) {
            $query->where('fullname_normalized', 'like', '%' . $this->escapeLike($token) . '%');
        }

        $query->orderByRaw(
            'CASE WHEN fullname_normalized = ? THEN 0 WHEN fullname_normalized LIKE ? THEN 1 ELSE 2 END',
            [$normalized, $this->escapeLike($normalized) . '%']
        );
    }

    private function applyPhoneFilter(Builder $query, ?string $phone): void
    {
        $normalized = $phone !== null ? ArabicNormalizer::phone($phone) : '';

        if (mb_strlen($normalized) < self::MIN_PHONE_LENGTH) {
            return;
        }

        $query->where('phone_normalized', 'like', '%' . $this->escapeLike($normalized) . '%');
    }

    private function escapeLike(string $value): string
    {
        return addcslashes($value, '%_\\');
    }
}
