<?php

namespace App\Handlers;

use App\Exceptions\NotAllowRegisterUserException;
use DefStudio\Telegraph\DTO\InlineQuery;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class AdministratorHandler extends BaseHandler
{
    protected function handleChatMessage(Stringable $text): void
    {
        if($text == 'operations'){
            $this->chat->markdown("operations Ready !")->send();

        }
        else{
            $this->chat->markdown("no understand your message")->send();
        }
    }

}
