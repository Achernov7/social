<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Post\PostController;
use App\Http\Controllers\User\UserController;
use App\Http\Controllers\Group\GroupController;
use App\Http\Controllers\Friend\FriendController;
use App\Http\Controllers\ChatMessages\ChatMessageController;
use App\Http\Controllers\Image\ImageController;
use App\Http\Controllers\Music\MusicController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::group(['middleware' => ['auth:sanctum', 'lastActivity']], function () {
    
    Route::group(['prefix'=>'messages'], function(){

        Route::get('/', [ChatMessageController::class, 'index']);
        Route::get('/getMessagesWithUser/{receiver_id}', [ChatMessageController::class, 'show']);
        Route::post('/saveMessage/{receiver_id}', [ChatMessageController::class, 'store']);
        Route::get('/check', [ChatMessageController::class, 'indexCheck']);
        Route::post('/stopCheck', [ChatMessageController::class, 'stopCheck']);
        Route::post('/receiverSaw/{idOfMessage}', [ChatMessageController::class, 'receiverSaw']);
        Route::get('/getNumberOfUnreadConversationsPlusDeafultAudioPlusId', [ChatMessageController::class, 'getNumberOfUnreadConversationsPlusDeafultAudioPlusId']);
        Route::get('/getNumberOfUnreadConversations', [ChatMessageController::class, 'getNumberOfUnreadConversations']);
        Route::post('/userRead/{conversationId}', [ChatMessageController::class, 'userRead']);
    });

    Route::group(['prefix'=>'users'], function(){
        Route::post('/store', [UserController::class, 'store']);
        Route::get('/', [UserController::class, 'showAuthenticated']);
        Route::get('/{user}', [UserController::class, 'show']);
        Route::delete('/deleteAuthUser', [UserController::class, 'destroy']);
    
    });

    Route::group(['prefix'=>'groups'], function(){
        Route::post('/create', [GroupController::class, 'create']);
        Route::get('/', [GroupController::class, 'index']);
        Route::post('/update/{group}', [GroupController::class, 'update']);
        Route::delete('/delete/{group}', [GroupController::class, 'destroy']);
        Route::post('/unsubscribe/{group}', [GroupController::class, 'unsubscribe']);
        Route::post('/subscribe/{group}', [GroupController::class, 'subscribe']);

        Route::group(['prefix'=>'{group}/users'], function(){
            Route::get('/getSubscribers', [GroupController::class, 'getSubscribers']);
            Route::get('/getBanUsers', [GroupController::class, 'getBanUsers']);
            Route::post('getSubscribers/ban/{user}/', [GroupController::class, 'banUser']);
            Route::post('getBanUsers/unban/{user}/', [GroupController::class, 'unbanUser']);
        });

        Route::group(['prefix'=>'/posts'], function(){
            Route::get('/likes/{postId}/{numberOfLikes}', [PostController::class, 'getLikesOfThePost']);
            Route::post('/', [PostController::class, 'indexWithCommentsWithoutConnectToTheCertainGroup']);
            Route::post('/like/{postId}', [PostController::class, 'postLike']);
            Route::post('/dislike/{postId}', [PostController::class, 'postDislike']);
            Route::post('/comments/create/{postId}', [PostController::class, 'createComment']);
            Route::post('/comments/{postId}/{limitForComments}', [PostController::class, 'getCommentsOfThePost']);
            Route::delete('/comments/delete/{comment}', [PostController::class, 'deleteComment']);
        });
        
        Route::group(['prefix'=>'{group}/posts'], function(){
            Route::post('/create', [PostController::class, 'create']);
            Route::get('/', [PostController::class, 'index']);
            Route::post('/withCommentsAndLikes', [PostController::class, 'indexWithCommentsToTheCertainGroup']);
            Route::post('/update/{post}', [PostController::class, 'update']);
            Route::delete('/delete/{post}', [PostController::class, 'destroy']);
        });
    });

    Route::group(['prefix'=>'friends'], function(){
        
        Route::post('/getFriendsForPaginationOfAnyUser/{user}', [FriendController::class, 'getFriendsForPaginationOfAnyUser']);
        Route::post('/addAsFriend/{user}', [FriendController::class, 'Addfriend']);
        Route::get('/getFriendsOfAuthWithPagination', [FriendController::class, 'getFriendsOfAuthWithPagination']);
        Route::post('/getSubscribersOfAuthWithPagination', [FriendController::class, 'getSubscribersOfAuthWithPagination']);
        Route::post('/getSubscribedToOfAuthWithPagination', [FriendController::class, 'getSubscribedToOfAuthWithPagination']);
        Route::get('/getSubscribersOfAuthWithPagination', [FriendController::class, 'getSubscribersOfAuthWithPagination']);
        Route::get('/getSubscribedToOfAuthWithPagination', [FriendController::class, 'getSubscribedToOfAuthWithPagination']);
        Route::get('/getUsersWithNoRelationshipWithAuthWithPagination', [FriendController::class, 'getUsersWithNoRelationshipWithAuthWithPagination']);
        Route::get('/acceptToBeFriends/{user}/{alreadyDisplayed}', [FriendController::class, 'acceptToBeFriends']);
        Route::delete('/deleteFromFriendList/{user}', [FriendController::class, 'deleteFromFriendList']);
        Route::delete('/CancelYourSubscription/{user}', [FriendController::class, 'CancelYourSubscription']);

    });

    Route::group(['prefix'=>'music'], function(){
        Route::post('/index', [MusicController::class, 'indexForLiked']);
        Route::get('/song', [MusicController::class, 'song']);
        Route::post('/store', [MusicController::class, 'store']);
        Route::get('/next/{songId}', [MusicController::class, 'next']);
        Route::get('/previous/{songId}', [MusicController::class, 'previous']);
        Route::post('/random/{songId}', [MusicController::class, 'random']);
        Route::post('/like/{songId}', [MusicController::class, 'like']);
        Route::post('/unlike/{songId}', [MusicController::class, 'unlike']);
    });

    Route::group(['prefix'=>'images'], function(){
        Route::post('/store', [ImageController::class, 'store']);
        Route::post('/index', [ImageController::class, 'index']);
        Route::post('/setMain/{id}', [ImageController::class, 'setMain']);
        Route::post('/unsetMain/{id}', [ImageController::class, 'unsetMain']);
        Route::post('/updateDescription/{image}', [ImageController::class, 'updateDescription']);
        Route::delete('/delete/{image}', [ImageController::class, 'destroy']);
    });
});

