<?php

namespace App\Handlers;

use DefStudio\Telegraph\DTO\InlineQuery;
use DefStudio\Telegraph\DTO\User;
use DefStudio\Telegraph\Facades\Telegraph;
use DefStudio\Telegraph\Handlers\WebhookHandler;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use Illuminate\Support\Stringable;

class DefaultHandler extends WebhookHandler
{
    public function test(): void
    {
        $this->chat->html("TEST: {$this->chat->chat_id}")->send();
    }

    protected function handleChatMessage(Stringable $text): void
    {
        if($text == 'keyboard'){
            $this->chat->markdown("keyboard before")->send();

            Telegraph::message('hello world')
                ->replyKeyboard(ReplyKeyboard::make()->buttons([
                    ReplyButton::make('foo')->requestPoll(),
                    ReplyButton::make('bar')->requestQuiz(),
                    ReplyButton::make('baz')->webApp('https://webapp.dev'),
                ]))->send();

            $this->chat->markdown("keyboard after")->send();



//            $this->chat->message('hello world')
//                ->keyboard(Keyboard::make()->buttons([
//                    Button::make('🗑️ Delete')->action('delete')->param('id', '42'),
//                    Button::make('👀 Open')->url('https://test.it'),
//                    Button::make('Web App')->webApp('https://web-app.test.it'),
//                    Button::make('Login Url')->loginUrl('https://loginUrl.test.it'),
//                ]))->send();
//
//            Telegraph::message('hello world')
//                ->keyboard(Keyboard::make()->buttons([
//                    Button::make('🗑️ Delete')->action('delete')->param('id', '42'),
//                    Button::make('👀 Open')->url('https://test.it'),
//                    Button::make('Web App')->webApp('https://web-app.test.it'),
//                    Button::make('Login Url')->loginUrl('https://loginUrl.test.it'),
//                ]))->send();



        }
        else{

            // disable preview link
            // $chat->message("http://my-blog.dev")->withoutPreview()->send();


            $this->chat->markdown("*Hello!*\n\nI'm here!")->send();
        }
    }

    protected function handleChatMemberJoined(User $member): void
    {
        /*
        * private storage every user
        $member->storage()->forget('key');
        $member->storage()->get('key','defaultValue');
        $member->storage()->set('key','NewValue');
       */
        Telegraph::message(json_encode($member->toArray()))->send();

        }

    protected function handleInlineQuery(InlineQuery $inlineQuery): void
    {
        // .. do nothing
    }

}
