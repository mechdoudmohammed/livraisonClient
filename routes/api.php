<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

// Route::get('/user', function (Request $request) {

//     return $request->user();
// });
Route::get('/email/verify/{id}/{hash}', 'VerifyEmailController@__invoke')
    ->middleware(['signed', 'throttle:6,1'])
    ->name('verification.verify');


// Resend link to verify email
Route::post('/email/verify/resend', 'VerifyEmailController@resend')
    ->middleware(['throttle:6,1']);


Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return auth()->guard('admin')->user();
    // return $request->user();
});

Route::middleware('auth:sanctum')->get('/employe', function (Request $request) {
    return auth()->guard('employe')->user();
});

Route::middleware('auth:sanctum')->get('/client', function (Request $request) {
    return auth()->guard('client')->user();
});


Route::post('visiteur/callLogs', 'HomeController@callLogs');
Route::get('getBank', 'HomeController@getBank');
Route::post('visiteur/villes', 'HomeController@getVillesVisiteur');
Route::get('visiteur/historiqueCommande/{id}', 'HomeController@historiqueCommande');

/*Client route*/
Route::post('inscription', 'Client\ClientController@inscription');
Route::post('loginClient', 'Client\ClientController@login');
Route::get('Villes', 'Client\ClientController@getVilles');
Route::middleware(['auth:client'])
    ->group(function () {
        Route::apiResource('Client', 'Client\ClientController');
        Route::apiResource('Commande', 'Client\CommandeController');
        Route::apiResource('Article', 'Client\ArticleController');
        Route::apiResource('Facture', 'Client\FactureController');
        Route::apiResource('Reclamation', 'Client\ReclamationController');
        Route::apiResource('Store', 'Client\StoreController');
        Route::apiResource('EmployeClient', 'Client\EmployeClientController');
        Route::apiResource('Notification', 'Client\NotificationClientController');
        Route::post('getEmployeClient', 'Client\EmployeClientController@index');
        Route::get('BlockEmployeClient/{id}', 'Client\EmployeClientController@BlockEmployeClient');
        Route::post('updateStore', 'Client\StoreController@updateStore');
        Route::get('changeFavorite/{id}', 'Client\StoreController@changeFavorite');
        Route::post('getReclamation', 'Client\ReclamationController@index');
        Route::post('getStore', 'Client\StoreController@getStore');
        Route::get('demandeActiverStock', 'Client\ClientController@demandeActiverStock');
        Route::get('receptionRetour/{id}', 'Client\CommandeController@receptionRetour');
        Route::get('checkBlackList/{telephone}', 'Client\ClientController@checkBlackList');

        Route::post('FactureClient', 'Client\FactureController@index');
        Route::get('BlockStore/{id}', 'Client\StoreController@BlockStore');
        Route::post('modifierProfile', 'Client\ClientController@modifier');
        Route::get('getClientsData', 'Client\ClientController@getClientsData');

        Route::post('updatePassword', 'Client\ClientController@updatePassword');

        Route::post('pickupCommande', 'Client\ClientController@pickupSelectedCommande');
        Route::post('confirmeCommande', 'Client\ClientController@confirmeSelectedCommande');

        Route::post('logoutClient', 'Client\ClientController@logout');
        Route::get('changeStatutClient/{id}', 'Client\ClientController@changeStatutClient');


        Route::post('updateCommande', 'Client\CommandeController@updateCommande');
        Route::post('suivieCommande', 'Client\CommandeController@getCommandeSuivie');
        Route::post('retourCommande', 'Client\CommandeController@getALLCommandeRetourRR');

        Route::get('showPackage', 'Client\ClientController@showPackage');
        Route::post('getPackage', 'Client\CommandeController@getPackage');
        Route::post('bonRamassage', 'Client\FactureController@bonRamassage');
        Route::get('getFacture/{id}', 'Client\FactureController@getFacture');
        Route::post('downloadStickerArticle', 'Client\ArticleController@downloadSticker');
        Route::get('historiqueCommande/{id}', 'Client\CommandeController@historiqueCommande');
        Route::post('downloadHistoriqueArticle', 'Client\ArticleController@downloadHistoriqueArticle');

        Route::get('afficheArticleDisponible', 'Client\ArticleController@afficheArticleDisponible');
        Route::post('checkStock', 'Client\ArticleController@checkStock');
        Route::post('getDeliveredCommande', 'Client\StatistiqueController@getDeliveredCommande');
        Route::post('commandeStatistiquesRevenue', 'Client\StatistiqueController@commandeStatistiquesRevenue');
        Route::post('statistiquesVille', 'Client\StatistiqueController@statistiquesVille');

        Route::post('changeStatutCommande', 'Client\CommandeController@changeStatutCommande');

        Route::get('getNotification', 'NotificationController@getStatutCommande');

        Route::post('rechercheCommande', 'Client\RechercheController@rechercheCommande');
        Route::post('rechercheCommandeSuivie', 'Client\RechercheController@rechercheCommandeSuivie');
        Route::post('classifierCommande', 'Client\RechercheController@classifierCommande');

        //---------------Debut Notification---------------
        Route::apiResource('Notification', 'Client\NotificationController');
        Route::post('NotificationModifier', 'Client\NotificationController@modifier');
        Route::get('checkNotification', 'Client\NotificationController@checkNotification');
        Route::get('importantNotification', 'Client\NotificationController@importantNotification');
    
        //---------------Fin Notification---------------
        Route::get('getAnnonces', 'Client\ClientController@getAnnonces');
        Route::post('sendMessage', 'Client\ClientController@sendMessage');
        Route::get('getMessages/{id}', 'Client\ClientController@getMessages');

    });





//---------------Other---------------

//---------------Fin Other---------------
