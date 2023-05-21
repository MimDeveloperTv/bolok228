<?php

namespace App\Utils;

use App\Handlers\BaseHandler;

class Helpers extends BaseHandler
{
    private  function cleanAndValidArray(array $input): array
    {
        $safe = [];
        foreach ($input as $item) {
            if (!empty($item)) {
                $safe[] = trim($item);
            }
        }
        return $safe;
    }

    public function prepareArrayUniquedInput($input,$seprator = '.') : array
    {
        $input = explode($seprator, $input);
        $safeInput = self::cleanAndValidArray($input);
        return array_unique($safeInput);
    }

    public function prepareArrayCleanedInput($input,$seprator = '.') : array
    {
        $input = explode($seprator, $input);
        return  self::cleanAndValidArray($input);
    }

    /**
     * @throws \DefStudio\Telegraph\Exceptions\StorageException
     */
    public function getChatStorageCommand()
    {
        return $this->chat->storage()->get('command',null);
    }

    /**
     * @throws \DefStudio\Telegraph\Exceptions\StorageException
     */
    public function getChatStorageId()
    {
        return (int) $this->chat->storage()->get('id', '1');
    }

    public function setChatStorageCommand($newCommand)
    {
         $this->chat->storage()->set('command', $newCommand);
    }


}
