<?php

namespace App\Enums;

class Commands
{
    public const CLEAN_BOOKMARK = 'cleanbookmark';
    public const SET_NUMBER_BOOKMARK = 'setnumberbookmark';

    public const COMMANDS = [
        self::CLEAN_BOOKMARK,
        self::SET_NUMBER_BOOKMARK,
    ];
}
