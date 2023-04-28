<?php

/* Manual Update Keyboard Buttons
         $newKeyboard = $this->originalKeyboard->deleteButton('Dismiss');
         $this->replaceKeyboard($newKeyboard);
*/

/* private storage every user
               $member->storage()->forget('key');
               $member->storage()->get('key','defaultValue');
               $member->storage()->set('key','NewValue');
              */

// disable preview link
// $chat->message("http://my-blog.dev")->withoutPreview()->send();

/*
    * registerWebhook And Show Result
    $reponse = \DefStudio\Telegraph\Facades\Telegraph::registerWebhook()->send();
    dd($reponse->json());
    registerWebhook And Show Result
   */
