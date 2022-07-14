<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"></a></p>

# Simple api for simple bank
This project contains simple api to manage simple bank

## Initialize
First run `composer install`.</br>
Then config your database in `.env` file.</br>
after that generate key for your laravel application using: `php artisan key:generate`.</br>
Then use `php artisan migrate:fresh --seed` to create tables and pre-populate them.</br>
after that run `php artisan passport:install`.</br>
Then `php artisan serve`.</br>
And there you have it!.
## Some discriptions
There are 8 api routes in this project.
```
Route::middleware('auth:api')->group(function ()
{
    Route::get('/getAllCustomers',[BankController::class,'allCustomers']);
    Route::get('/getAllAccounts',[BankController::class,'allAcounts']);
    Route::post('/CreateAccount/{customer}',[BankController::class,'create']); 
    Route::post('/Transfer/from/{from}/to/{to}',[BankController::class,'transfer']);
    Route::get('/getAccountAmount/{account}',[BankController::class,'getAmount']);
    Route::get('/TransferHistory/{account}',[BankController::class,'history']);
});

//auth
Route::post('/register',[AuthController::class,'register']);
Route::post('/login',[AuthController::class,'login']);
```
### Let me explain the routes for you
First you need to register a user as employee to get access_token.for that you have to send a post request to `YOUR_APP_URL/api/register` with some data.the data must contain 3 values like this:
```
[
'name' => 'YOU_NAME',
'email' => 'YOUR_EMAIL',
'password' => 'YOUR_PASSWORD'
]
```
(you can use post-man form-data to test it)</br>
If everything goes well you will get a response in json format like this:
```
{
    "user": {
        "name": "YOUR_NAME",
        "email": "YOUR_EMALI",
        "updated_at": "TIME",
        "created_at": "TIME",
        "id": 9
    },
    "token": "YOUR_TOKEN",
    "token_type": "Bearer"
}
```
Now you have your token.copy your token because you gonna need it for all of the requests.</br>
Now if for all routes that you wanna access, you have to send a header with `Authorization` as a key and `Bearer YOUR_TOKEN` as value,
and you are good to go.
#### Create Account For A Customer
A customer can have many accounts so you can use `YOUR_APP_URL/api/CreateAccount/{customer}` to create a new account for a specefic customer.instead of `{customer}` you have to enter the coustomers id to assing that account to the entered customer.(we can easily change it to find the costomer by name or... but we are ok with id).</br>after making account using this route it will return the created account details in json format.
#### Transfer
You can transfer amounts between any two accounts, including those owned by different customers.you only need to use this route`YOUR_APP_URL/api/Transfer/from/{from}/to/{to}`</br>
Instead of `{from}` you have to put the account's id that you wanna transfer amount from and instead of `{to}` you have to put account's id that wanna recieve the amount.if any problem happens you will recieve a message in json format that says `it is not possible` and if everything goes well you will recieve a `success message`.
#### Get Account's Amount
You can get the amount of a specefic account by using `YOUR_APP_URL/api/getAccountAmount/{account}`.you have to us account's id in `{account}` and then you will get full data of that account including amount.
#### Transfer history
you can get the full history of transfering a specefic account by this route `YOUR_APP_URL/api/TransferHistory/{account}`. you only need to put account's id instead of `{account}`.then you will see full transfers history in json format like this:</br>
````````````````````````````````````
{
    "data": {
        "this_account_transfered": [
            {
                "transfererAccount_id": 1,
                "transferer_name": "Anibal Sporer",
                "recieverAccount_id": 2,
                "reciever_name": "Jermaine Terry",
                "amount": 10
            }
        ],
        "this_account_recieved": [
            {
                "recievedFromAccount_id": 2,
                "transferer_name": "Jermaine Terry",
                "recieverAccount_id": 1,
                "reciever_name": "Anibal Sporer",
                "amount": 12
            }
        ]
    }
}
````````````````````````````````````
You will see a "data" that includs "this_account_transfered" and "this_account_recieved".</br>
"this_account_transfered" includes the info about amount that has been transfered by this account.</br>
and "this_account_recieved" includes the info about amount that has been recieved by this account.
##Test
you can run the tests by using `php artisan test`.</br>
and if you faced with the error including this cocept that "there is no "unit" folder" you have to make the `Unit` folder inside `test` folder and then run:
`php artisan test`
