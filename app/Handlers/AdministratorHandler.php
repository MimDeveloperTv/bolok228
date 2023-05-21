<?php

namespace App\Handlers;

use App\Enums\Commands;
use App\Exceptions\NotAllowRegisterUserException;
use App\Models\bookmark;
use App\Models\mark;
use App\Utils\Helpers;
use DefStudio\Telegraph\DTO\InlineQuery;
use DefStudio\Telegraph\Keyboard\Button;
use DefStudio\Telegraph\Keyboard\Keyboard;
use DefStudio\Telegraph\Keyboard\ReplyButton;
use DefStudio\Telegraph\Keyboard\ReplyKeyboard;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Stringable;

class AdministratorHandler extends BaseHandler
{
    public BaseHandler $help;

    public function __construct()
    {
        parent::__construct();
        $this->help = new Helpers();
    }

    /**
     * @throws \DefStudio\Telegraph\Exceptions\StorageException
     */
    protected function handleChatMessage(Stringable $text): void
    {
        match ($this->help->getChatStorageCommand()) {
            Commands::SET_NUMBER_BOOKMARK => $this->setNumberBookmark($text),
            Commands::CLEAN_BOOKMARK => $this->processCleanBookmark($text),
            '******' => $this->bookmarkApproveInput($text),
            default => $this->chat->markdown("no understand your message")->send(),
        };
    }

    /**
     * @throws \DefStudio\Telegraph\Exceptions\StorageException
     */
    public function newbookmark(): void
    {
        $id = $this->help->getChatStorageId();
        $bk = bookmark::query()->orderBy('id', 'ASC')
            ->where('bookmarks', '')
            ->where('id', '>=', $id)
            ->first();
        if ($bk != null) {
            $this->chat->storage()->set('id', $bk->id);
            $this->chat->storage()->set('title', $bk->title);
            $this->chat->message(" شماره : {$bk->id}")->send();
            $this->chat->message(" عنوان : {$bk->title}")->send();
        } else {
            $this->chat->markdown("چیزی برای برچسب گذاری وجود ندارد")->send();
        }
    }

    public function nextbookmark(): void
    {
        $id = $this->help->getChatStorageId() + 1;
        $bk = bookmark::query()->orderBy('id', 'ASC')
            ->where('bookmarks', '')
            ->where('id', '>=', $id)
            ->first();
        $this->chat->storage()->set('id', $bk->id);
        $this->chat->storage()->set('title', $bk->title);
        $this->chat->message(" شماره : {$bk->id}")->send();
        $this->chat->message(" عنوان : {$bk->title}")->send();
    }

    public function bookmarkUpdateAction()
    {
        $selectedId = $this->help->getChatStorageId();
        $selectedTitle = $this->chat->storage()->get('title', '1');
        $selectedBookmarks = $this->chat->storage()->get('bookmarks', '');
        $Bookmarks = explode(',', $selectedBookmarks);
        foreach ($Bookmarks as $bookmark) {
            mark::query()->create([
                'bookmark_id' => (int)$selectedId,
                'user' => $this->chat->chat_id,
                'title' => $selectedTitle,
                'mark' => $bookmark,
            ]);
        }
        $dbBookmark = bookmark::query()->find($selectedId);
        $dbBookmark->bookmarks = 'Verify';
        $dbBookmark->save();
        $this->deleteKeyboard();
        $this->newbookmark();

    }

    public function bookmarkApproveInput(Stringable $text)
    {
        $safeBookmarks = $this->help->prepareArrayCleanedInput($text);

        $keyboard = Keyboard::make()->row(
            [Button::make('Update')->action('bookmarkUpdateAction')->param('Approve', 'true')]
        );
        $this->chat->storage()->set('bookmarks', implode(',', $safeBookmarks));
        $this->chat->message("برچسب های زیر را تایید کنید وگرنه دوباره وارد نمایید")->send();
        $this->chat->message(implode('-', $safeBookmarks))->keyboard($keyboard)->send();
    }

    public function resetbookmark(): void
    {
        $id = 1;
        $bk = bookmark::query()->orderBy('id', 'ASC')
            ->where('bookmarks', '')
            ->where('id', '>=', $id)
            ->first();
        $this->chat->storage()->set('id', $bk->id);
        $this->chat->storage()->set('title', $bk->title);
        $this->chat->message(" شماره : {$bk->id}")->send();
        $this->chat->message(" عنوان : {$bk->title}")->send();
    }

    public function cleanbookmark(): void
    {
        $this->help->setChatStorageCommand(Commands::CLEAN_BOOKMARK);
        $this->chat->message("هشتگ های نقطه دار خود را وارد کنید")->send();
    }

    public function setNumberBookmark($input): void
    {
        $this->chat->storage()->set('numberbookmark', $input);
        $this->chat->message("شمارنده ثبت شد")->send();
    }

    public function numberbookmark($input): void
    {
        $this->help->setChatStorageCommand(Commands::SET_NUMBER_BOOKMARK);
        $this->chat->message("شمارنده وارد کنید")->send();
    }

    public function processCleanBookmark($input)
    {
        $uniqueInputArray = $this->help->prepareArrayUniquedInput($input);

        $counter = (int)$this->chat->storage()->get('numberbookmark', '1');
        $counterArray = [];
        foreach ($uniqueInputArray as $item) {
            $counterArray[] = "{$counter}- {$item}";
            $counter++;
        }
        $prepareListNumeric = implode(PHP_EOL, $counterArray);
        $prepareListHashtags = implode('.', $uniqueInputArray);

        $this->chat->message($prepareListNumeric)->send();
        $this->chat->message($prepareListHashtags)->send();
        $this->chat->storage()->set('command', null);
    }

}
