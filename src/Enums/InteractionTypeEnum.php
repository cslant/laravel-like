<?php

namespace CSlant\LaravelLike\Enums;

enum InteractionTypeEnum: string
{
    case DEFAULT = 'default';
    case LIKE = 'like';
    case DISLIKE = 'dislike';
    case LOVE = 'love';

    /**
     * Get the values of the enum.
     *
     * @return array<int, InteractionTypeEnum>
     */
    public static function getValues(): array
    {
        return [
            self::LIKE,
            self::DISLIKE,
            self::LOVE,
        ];
    }

    /**
     * Get the values of the enum as strings.
     *
     * @return array<string>
     */
    public static function getValuesAsStrings(): array
    {
        return [
            self::LIKE->value,
            self::DISLIKE->value,
            self::LOVE->value,
        ];
    }

    /**
     * Check if the value is like.
     *
     * @return bool
     */
    public function isLike(): bool
    {
        return $this === self::LIKE;
    }

    /**
     * Check if the value is dislike.
     *
     * @return bool
     */
    public function isDislike(): bool
    {
        return $this === self::DISLIKE;
    }

    /**
     * Check if the value is love.
     *
     * @return bool
     */
    public function isLove(): bool
    {
        return $this === self::LOVE;
    }

    /**
     * Get the type by value.
     *
     * @param  string  $value
     *
     * @return InteractionTypeEnum
     */
    public function getTypeByValue(string $value): InteractionTypeEnum
    {
        return match ($value) {
            self::LIKE->value => self::LIKE,
            self::DISLIKE->value => self::DISLIKE,
            self::LOVE->value => self::LOVE,
            default => self::DEFAULT,
        };
    }
}
