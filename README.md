# WordPress FirePlateX (WPFPX)

Implemented authentication by firebase and subscription by stripe in WordPress. It can recognize users who purchased subsciption, and provide services tailored to users. Moreover, you can provide information with WordPress that you always use.

To install, do a github clone in your theme directory, add a line to functions.php, save firebase and stripe API information in webpack/.env, and add Install composer in the directory where WordPress is installed and install the firebase and stripe libraries from composer.

## 1. Firebase Authentication Settings

https://console.firebase.google.com/

- A) Create Your Firebase Project
- B) Firebase console -> Project Settings -> Service Account -> create new secret key -> download your xxxxxxxxx-firebase-adminsdk-xxxxxxxxx.json ***IMPORTANT***
- C) Firebase console -> Authentication -> Sign-in method -> Add Your Domain
- D) Firebase console -> Authentication -> Sign-in method -> Login Provider enabled that you need(Mail/Password, Google is my fun)

## 2. Firebase Firestore Database Setting

- A) create your database ( prod or dev ? depend on your environment )
- B) Firestore Database -> rule

## 3. Stripe Setting

https://dashboard.stripe.com/

- A) Login your Stripe Account
- B) create new account -> put your account name etc..
- C) stripe home -> dashbord -> developper -> API Key
- D) Copy your public key (key will be start alphabet from "pk_test_xxx")***IMPORTANT***
- E) Copy your secret key (key will be start alphabet from "sk_test_xxx")***IMPORTANT***
- F) Create subscriptions and Pricing ***IMPORTANT***

## 4. install gRPC on your PHP as module

First of all, you need to install Google grpc. You can find more details below link.

https://cloud.google.com/php/grpc

```sh
sudo pecl install grpc
```

and then add grpc.so to php.ini 

easy to detect php.ini location typing below.
```
$ php -i | grep "Loaded Configuration File"
```

and then you can check grpc is enabled as typing below command

```sh
$ php -i | grep "grpc support"
```

"grpc support => enabled" = GJ.

## 5. install grpc/composer on your server and copy "xxxxxxxxx-firebase-adminsdk-xxxxxxxxx.json"

Initialize the composer in the same path where WordPress is installed.

```sh
$ mkdir composer # same path of WordPress
$ cd composer
$ composer require kreait/firebase-php grpc/grpc google/cloud-firestore stripe/stripe-php
```

and then copy xxxxxxxxx-firebase-adminsdk-xxxxxxxxx.json file to composer directory

```
$ cp xxxxxxxxx-firebase-adminsdk-xxxxxxxxx.json composer
$ cd composer
$ mv xxxxxxxxx-firebase-adminsdk-xxxxxxxxx.json adminsdk.json # <-IMPORTANT
```

***DO NOT FOR GET RENAME*** "xxxxxxxxx-firebase-adminsdk-xxxxxxxxx.json" to "adminsdk.json"


## 6. clone WPFPX on your theme directory

```sh
$ cd wordpress/wp-content/themes/twentytwentyone
$ git clone https://github.com/kmusiclife/WPFPX.git
```

## 7. Firebase Project setting infomation

https://console.firebase.google.com/

- A) Firebase console -> settings -> General -> My App -> WEB(</>) -> Add your nickname -> to console
- B) Firebase console -> settings -> General -> My App -> </> -> npm

you can find below 

```js
const firebaseConfig = {
  apiKey: "xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
  authDomain: "xxxxxxxxxxxx.firebaseapp.com",
  projectId: "xxxxxxxx-xxxx",
  storageBucket: "xxxx-xxxxx.appspot.com",
  messagingSenderId: "xxxxxxxxxxxx",
  appId: "x:xxxxxxxxxxxx:xxx:xxxxxxxxxxxxxxxxxxxxxxxxx",
  measurementId: "x-xxxxxxxxxxxxxx"
};
```

make .env file

```
$ cd wordpress/wp-content/themes/twentytwentyone/WPFPX/webpack
$ cp .env.dist .env
$ vim .env
```

edit your `.env` file as .env file format

```
apiKey="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx",
authDomain="xxxxxxxxxxxx.firebaseapp.com",
projectId="xxxxxxxx-xxxx",
storageBucket="xxxx-xxxxx.appspot.com",
messagingSenderId="xxxxxxxxxxxx",
appId="x:xxxxxxxxxxxx:xxx:xxxxxxxxxxxxxxxxxxxxxxxxx",
measurementId="x-xxxxxxxxxxxxxx"
stripePublicKey="pk_xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
```

stripePublicKey = Stripe Public API Key

eg.) pk_xxxxxxxxxxxxxxxxxx
You got it at section "3. Stripe Setting"

## 8. npm initialize

```
$ cd wordpress/wp-content/themes/twentytwentyone/WPFPX/webpack
$ npm i
$ npm run build
```

## 9. edit functions.php of your theme

```sh
$ cd wordpress/wp-content/themes/twentytwentyone
$ vim functions.php
```

bellow content add to functions.php

```php
include('WPFPX/init.php');
```

## 10. check WPFPX

Access below URL
[http://yourhostname/login/][http://yourhostname/login/]
[http://yourhostname/payment/][http://yourhostname/payment/]

You can check your login page of Firebase Authentication and Stripe Subscription.
You could not fins subscription plan, you should back to read to "3->F" section
