<?php

use DefStudio\Telegraph\Facades\Telegraph as TelegraphFacade;
use DefStudio\Telegraph\Models\TelegraphBot;
use DefStudio\Telegraph\Models\TelegraphChat;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    /*
     * registerWebhook And Show Result
     $reponse = \DefStudio\Telegraph\Facades\Telegraph::registerWebhook()->send();
     dd($reponse->json());
     registerWebhook And Show Result
    */

    /** @var TelegraphChat $chat */
    TelegraphFacade::chat('88550255')->message('ok')->send();
 //   $chat = $bot->chats()->create([ 'chat_id' => '88550255','name' => 'mimdeveloper']);
   return response()->json(['status' => 'send']);

});
