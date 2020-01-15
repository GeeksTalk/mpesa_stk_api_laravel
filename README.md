<p align="center"><img src="https://res.cloudinary.com/dtfbvvkyp/image/upload/v1566331377/laravel-logolockup-cmyk-red.svg" width="400"></p>

## About This Project

This is a simple Mpesa Lipa Online STK push API implementation


## Configuring

Git clone and Run Composer 
```
composer install
```

Create ``.env`` file
```bash
cp .env.example .env
```

Generate Key

```bash
php artisan key:generate
```
 
## Set Up .env variables
For the staging environment, you can use the already gibe sandbox Short Code and Passkey, you'll only need to generate the Consumer Key and Secret from the Daraja sandbox
For Production , you'll have to provide the Short Code and Pass Key

```bash
MPESA_SHORT_CODE=
MPESA_PASSKEY=
CONSUMER_KEY=
CONSUMER_SECRET=
```

## Running
```bash
php artisan serve 
```
For the API callback to be hit from Mpesa , you must use a live server , or use [Ngrok](https://ngrok.com/) in order to expose your local server (localhost)

Remember to replace the env ``APP_URL`` with the generated Ngrok URL
## Licence

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
