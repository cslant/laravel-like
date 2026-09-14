<?php

namespace CSlant\LaravelLike\Traits;

use CSlant\LaravelLike\Enums\InteractionTypeEnum;

/**
 * Trait ForgetsInteractions
 *
 * @package CSlant\LaravelLike\Traits
 */
trait ForgetsInteractions
{
    /**
     * Forget all recorded interactions of the given type.
     *
     * @param  string  $interactionType
     *
     * @return static
     */
    public function forgetInteractionsOfType(string $interactionType): static
    {
        $this->likes()->where('type', $interactionType)->delete();

        return $this;
    }

    /**
     * Forget all recorded interactions, optionally scoped by type.
     *
     * @param  null|string  $interactionType
     *
     * @return static
     */
    public function forgetInteractions(?string $interactionType = null): static
    {
        if ($interactionType && in_array($interactionType, InteractionTypeEnum::getValuesAsStrings())) {
            return $this->forgetInteractionsOfType($interactionType);
        }

        $this->likes()->delete();

        return $this;
    }
}
