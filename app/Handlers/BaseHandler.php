<?php

namespace App\Handlers;

use App\Exceptions\NotAllowRegisterUserException;
use DefStudio\Telegraph\DTO\InlineQuery;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class BaseHandler extends WebhookHandler
{
    protected function setupChat(): void
    {
        $telegramChat = $this->message?->chat() ?? $this->callbackQuery?->message()?->chat();
        assert($telegramChat !== null);
        /** @var TelegraphChat $chat */
        $chat = $this->bot->chats()->firstOrNew([
            'chat_id' => $telegramChat->id(),
        ]);
        $this->chat = $chat;

        if (!$this->chat->exists) {
            if (!$this->allowUnknownChat()) {
                throw new NotAllowRegisterUserException('NotAllowRegisterUser');
            }

            if (config('telegraph.security.store_unknown_chats_in_db', false)) {
                $this->chat->name = Str::of("")->append(" ", $telegramChat->title());
                $this->chat->save();
            }
        }
    }

    public function start(): void
    {
        $this->chat->html("You Are Welcome,Please type operations")->send();
    }

    public function test(): void
    {
        $this->chat->html("Chat ID: {$this->chat->chat_id}")->send();

        $replyKeyboard =  ReplyKeyboard::make()->buttons([
            ReplyButton::make('foo')->requestPoll(),
            ReplyButton::make('bar')->requestQuiz(),
            ReplyButton::make('baz')->webApp('https://webapp.dev'),
        ]);

        $this->chat->message('select operation')->replyKeyboard($replyKeyboard)->send();

        $keyboard = Keyboard::make()
            ->row([
                Button::make('Dismiss')->action('process')->param('id', '43'),
                Button::make('Dismiss')->action('process')->param('id', '42'),
            ])
            ->row([ Button::make('open')->url('https://test.it'), ]);

        $this->chat->message('hello world')->keyboard($keyboard)->send();
    }

    public function process(): void
    {
        $id = $this->data->get('id');
        $this->chat->html("ID: {$id}")->send();
        $this->deleteKeyboard();

    }

    protected function handleChatMessage(Stringable $text): void
    {
        if($text == 'operations'){
            $this->chat->markdown("operations Ready !")->send();
        }
        else{
            $this->chat->markdown("no understand your message")->send();
        }
    }

    protected function handleInlineQuery(InlineQuery $inlineQuery): void
    {
        // .. do nothing
    }

}
