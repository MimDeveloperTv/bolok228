<?php

namespace App\Handlers;

use App\Exceptions\NotAllowRegisterUserException;
use App\Models\bookmark;
use App\Models\mark;
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
    protected function handleChatMessage(Stringable $text): void
    {
        if ($this->chat->storage()->get('command') == 'setnumberbookmark') {
            $this->setNumberBookmark($text);

        } elseif ($this->chat->storage()->get('command') == 'cleanbookmark') {
            $this->processCleanBookmark($text);
        } elseif (str_contains($text, '.')) {
            $this->bookmarkApproveInput($text);
        } else {
            $this->chat->markdown("no understand your message")->send();
        }
    }

    /**
     * @throws \DefStudio\Telegraph\Exceptions\StorageException
     */
    public function newbookmark(): void
    {
        $id = (int)$this->chat->storage()->get('id', '1');
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
        $id = ((int)$this->chat->storage()->get('id', '1')) + 1;
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
        $selectedId = $this->chat->storage()->get('id', '1');
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

    public function reportError()
    {
        $this->chat->message("Error")->send();
    }

    public function bookmarkApproveInput(Stringable $text)
    {
        $bookmarks = explode('.', $text);
        $safeBookmarks = [];
        foreach ($bookmarks as $bookmark) {
            $bookmark = trim($bookmark);
            $safeBookmarks[] = $bookmark;
        }

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
        $this->chat->storage()->set('command', 'cleanbookmark');
        $this->chat->message("هشتگ های نقطه دار خود را وارد کنید")->send();
    }

    public function setNumberBookmark($input): void
    {
        $this->chat->storage()->set('numberbookmark', $input);
        $this->chat->message("شمارنده ثبت شد")->send();
    }

    public function numberbookmark($input): void
    {
        $this->chat->storage()->set('command', 'setnumberbookmark');
        $this->chat->message("شمارنده وارد کنید")->send();
    }

    public static function prepareValidInputData(array $inputArray)
    {
        $safeArray = [];
        foreach ($inputArray as $input) {
            if (!empty($input)) {
                $safeArray[] = trim($input);
            }
        }
        return $safeArray;
    }

    public function processCleanBookmark($input)
    {
        $inputToArray = explode('.', $input);
        $safeInputArray = self::prepareValidInputData($inputToArray);
        $uniqueInputArray = array_unique($safeInputArray);

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
