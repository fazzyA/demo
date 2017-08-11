<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');

use Illuminate\Http\Request;

// First route that user visits on consumer app
Route::get('/required', function () {
    // Build the query parameter string to pass auth information to our request
    $query = http_build_query([
        'client_id' => 17,
        'redirect_uri' => 'http://localhost:82/laravel5.3/demo/public/callback',
        'client_secret' => 'hdcwuX3oHDVIKzNz99qvrU8GsY5zvYE4vTxbwvE8', // from admin panel above
        'response_type' => 'code',
        'scope' => null
    ]);

    // Redirect the user to the OAuth authorization page
    return redirect('http://localhost:82/laravel5.3/wsapi/public/oauth/authorize?' . $query);
});

// Route that user is forwarded back to after approving on server
Route::get('/callback', function (Request $request) {
    $http = new GuzzleHttp\Client;

    $response = $http->post('http://localhost:82/laravel5.3/wsapi/public/oauth/token', [
        'form_params' => [
            'grant_type' => 'authorization_code',
            'client_id' => 17, // from admin panel above
            'client_secret' => 'hdcwuX3oHDVIKzNz99qvrU8GsY5zvYE4vTxbwvE8', // from admin panel above
            'redirect_uri' => 'http://localhost:82/laravel5.3/demo/public/callback',
            'code' => $request->code // Get code from the callback
        ]
    ]);

    // echo the access token; normally we would save this in the DB
    return json_decode((string) $response->getBody(), true)['access_token'];
});

Route::get('/required2', function (Request $request) {
$client = new GuzzleHttp\Client;

try {
    $response = $client->post('http://localhost:82/laravel5.3/wsapi/public/oauth/token', [
        'form_params' => [
            'client_id' => 17,
            // The secret generated when you ran: php artisan passport:install
            'client_secret' => 'hdcwuX3oHDVIKzNz99qvrU8GsY5zvYE4vTxbwvE8',
            'grant_type' => 'password',
            'username' => 'sadaf_cu@hotmail.com',
            'password' => 'testuser',
            'scope' => '*',
        ]
    ]);

    // You'd typically save this payload in the session
    $auth = json_decode( (string) $response->getBody(),true );
var_dump($auth);
    $access_token=$auth['access_token'];
//echo $access_token;
    $response = $client->get('http://localhost:82/laravel5.3/wsapi/public/api/files', [
        'headers' => [
            'Authorization' => 'Bearer '.$auth['access_token'],
        ]
    ]);
//
    $todos = json_decode( (string) $response->getBody() );

    $todoList = "";
    foreach ($todos as $todo) {
        $todoList .= "<li>{$todo->name}".($todo->status == 3 ? '✅' : '').">>>>".var_dump($todo)."</li>";
    }

    echo "<ul>{$todoList}</ul>";

} catch (GuzzleHttp\Exception\BadResponseException $e) {
    echo $e;
    echo "Unable to retrieve access token.";
}
});

Route::get('/upload', function () {
    return view('uploadform');

});

Route::post('/upload', function (Request $request) {
    $client = new GuzzleHttp\Client;

    try {
        $response = $client->post('http://localhost:82/laravel5.3/wsapi/public/oauth/token', [
            'form_params' => [
                'client_id' => 17,
                // The secret generated when you ran: php artisan passport:install
                'client_secret' => 'hdcwuX3oHDVIKzNz99qvrU8GsY5zvYE4vTxbwvE8',
                'grant_type' => 'password',
                'username' => 'sadaf_cu@hotmail.com',
                'password' => 'testuser',
                'scope' => '*',
            ]
        ]);

        // You'd typically save this payload in the session
        $auth = json_decode( (string) $response->getBody(),true );
        var_dump($auth);
        $access_token=$auth['access_token'];
//echo $access_token;

        $image_path = $request->fileToUpload->getPathname();
        $image_mime = $request->fileToUpload->getmimeType();
        $image_org  = $request->fileToUpload->getClientOriginalName();

        $response = $client->request('POST','http://localhost:82/laravel5.3/wsapi/public/api/upload_file', [
            'headers' => [
                'Authorization' => 'Bearer '.$auth['access_token'],
                'X-CSRF-TOKEN' => $request->_token,
//                'Content-Type' => 'multipart/form-data'
            ],
            'multipart' => [
                [
                    'name'     => 'photo',
                    'filename' => $image_org,
                    'Mime-Type'=> $image_mime,
                    'contents' => fopen( $image_path, 'r' ),
                ]


            ]

        ]);
//
        $result = json_decode( (string) $response->getBody() );


        dd($result);



    } catch (GuzzleHttp\Exception\BadResponseException $e) {
//        echo $e;
//        dd($e->getResponse());

        var_dump($response->getBody());
        var_dump($e->getTrace());
        var_dump($e->getMessage());
        echo $e->getMessage();

    }

});


Route::get('/download/{file_id}', function ($file_id) {

    $client = new GuzzleHttp\Client;

    try {
        $response = $client->post('http://localhost:82/laravel5.3/wsapi/public/oauth/token', [
            'form_params' => [
                'client_id' => 17,
                // The secret generated when you ran: php artisan passport:install
                'client_secret' => 'hdcwuX3oHDVIKzNz99qvrU8GsY5zvYE4vTxbwvE8',
                'grant_type' => 'password',
                'username' => 'sadaf_cu@hotmail.com',
                'password' => 'testuser',
                'scope' => '*',
            ]
        ]);

        // You'd typically save this payload in the session
        $auth = json_decode( (string) $response->getBody(),true );
        var_dump($auth);



        $response = $client->get('http://localhost:82/laravel5.3/wsapi/public/api/download_file', [
            'headers' => [
                'Authorization' => 'Bearer '.$auth['access_token'],
            ]
        ]);

        $result = json_decode( (string) $response->getBody() );


        dd($result);



    } catch (GuzzleHttp\Exception\BadResponseException $e) {
//        echo $e;
//        dd($e->getResponse());

        var_dump($response->getBody());
        var_dump($e->getTrace());
        var_dump($e->getMessage());
        echo $e->getMessage();

    }

});

Route::get('/info/{file_id}', function ($file_id) {

    $client = new GuzzleHttp\Client;

    try {
        $response = $client->post('http://localhost:82/laravel5.3/wsapi/public/oauth/token', [
            'form_params' => [
                'client_id' => 17,
                // The secret generated when you ran: php artisan passport:install
                'client_secret' => 'hdcwuX3oHDVIKzNz99qvrU8GsY5zvYE4vTxbwvE8',
                'grant_type' => 'password',
                'username' => 'sadaf_cu@hotmail.com',
                'password' => 'testuser',
                'scope' => '*',
            ]
        ]);

        // You'd typically save this payload in the session
        $auth = json_decode( (string) $response->getBody(),true );
        var_dump($auth);



        $response = $client->get('http://localhost:82/laravel5.3/wsapi/public/api/file_info/'.$file_id, [
            'headers' => [
                'Authorization' => 'Bearer '.$auth['access_token'],
            ]
        ]);

        $result = json_decode( (string) $response->getBody() );


        dd($result);



    } catch (GuzzleHttp\Exception\BadResponseException $e) {
//        echo $e;
//        dd($e->getResponse());

        var_dump($response->getBody());
        var_dump($e->getTrace());
        var_dump($e->getMessage());
        echo $e->getMessage();

    }

});
