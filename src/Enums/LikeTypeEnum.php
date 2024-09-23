<?php

namespace CSlant\LaravelLike\Enums;

enum LikeTypeEnum: string
{
    case LIKE = 'like';
    case DISLIKE = 'dislike';
    case LOVE = 'love';

    /**
     * Get the values of the enum.
     *
     * @return array<int, LikeTypeEnum>
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
}
