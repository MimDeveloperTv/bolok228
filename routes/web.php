<?php

use DefStudio\Telegraph\Facades\Telegraph as TelegraphFacade;
use DefStudio\Telegraph\Handlers\WebhookHandler;
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
    /** @var TelegraphChat $chat */
    TelegraphFacade::chat('729291442')->message('ok')->send();
   return response()->json(['status' => 'send']);

});

Route::get('/request/test', function () {

    /** @var class-string $handler */
    $handler = config('telegraph.webhook_handler');

    /** @var \App\Handlers\AdministratorHandler $handler */
    $handler = app($handler)->bookmarkApproveInput('سلام.مامان. بابا کجایی .من');
});

Route::get('/setwebhook', function () {
    $reponse = \DefStudio\Telegraph\Facades\Telegraph::registerWebhook()
    ->bot('1893970965:zKxrA1iPNSLigVbgNF8oakjI2DQnX4NAFz8taOsx')->send();

    dd($reponse->json());
});
