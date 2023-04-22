<?php

namespace App\Handlers;

use App\Exceptions\NotAllowRegisterUserException;
use App\Models\bookmark;
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
        if(str_contains($text, '.')){
            $stateAction =  $this->bookmarkUpdateAction($text);
            if($stateAction == true){
                $this->newbookmark();
            }
            else{
                $this->reportError();
            }
        }
        else{
            $this->chat->markdown("no understand your message")->send();
        }
    }

    public function newbookmark(): void
    {
       $bk = bookmark::query()->orderBy('id','ASC')->where('bookmarks','')->first();
       if($bk != null)
       {
        $this->chat->storage()->set('id',$bk->id);
        $this->chat->message(" عنوان : {$bk->title}")->send();
       }
       else
       {
           $this->chat->markdown("چیزی برای برچسب گذاری وجود ندارد")->send();
       }
    }

    public function nextbookmark(): void
    {
        $skipId =  $this->chat->storage()->get('id','1');
        $bk = bookmark::query()->orderBy('id','ASC')
            ->where('bookmarks','')
            ->where('id','!=',$skipId)
            ->first();
        $this->chat->storage()->set('id',$bk->id);
        $this->chat->message(" عنوان : {$bk->title}")->send();
    }

    public function bookmarkUpdateAction(Stringable $text)
    {
        $selectedBookmarkId =  $this->chat->storage()->get('id','1');
         $dbBookmark =  bookmark::query()->find($selectedBookmarkId);
         $dbBookmark->bookmarks = $text;
         return $dbBookmark->save();
    }

    public function reportError()
    {
        $this->chat->message("Error")->send();
    }




}
