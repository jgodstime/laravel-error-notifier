
# Error Notification For Laravel

This package sends you email and slack notification whenever 500 internal server errors happens in your application.
This package only works for laravel 8 and 9
  

![alt text](https://img001.prntscr.com/file/img001/Zirxkel4QF-IDBFV5v1veA.png)

 
## What the package sends as a notification

- Last route the user visited
- If they are authenticated or not
- User ID (If user is authenticated)
- User email (If user is authenticated)
- User error description message (After form is submitted)
- Trace error (The file and line number of the error; *before and after form is submitted*)
- Error status code

## How to install

    composer require jgodstime/laravel-error-notifier

In your */app/Exceptions/Handler.php* file, in the register method, add `\ErrorNotifier\Notify\Helper::getError($e);` this must be  inside  reportable callback

    public function register()
    {
    	$this->reportable(function (Throwable  $e)  {
    		 \ErrorNotifier\Notify\Helper::getError($e);
    	});
    }

## Disable  Instant Notification 
 
 By default this package sends notification immediately the error occured i.e without waiting for users description of the error, you can disable this by adding `NOTIFIER_INSTANT=false` in your .env file
 
### Publishing the vendor files

By default, Laravel displays the 500 error page in your */views/errors/500.blade.php* file when a 500 error occurs in your application

This package places the 500.blade.php file in the */views/errors/* folder after you publish the vendor file

If you already have a 500.blade.php file in your */views/errors* folder and you want to use this package; you should remove or move it to another folder.

### Publish the vendor files

    php artisan vendor:publish --provider="ErrorNotifier\Notify\ErrorNotifierServiceProvider"

*Now the vendor files are published in their respective paths*

## Let's Test

### Setup your email driver
> To setup your email driver, you can use [mailtrap](https://mailtrap.io/) for test purpose.

    MAIL_DRIVER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=587
    MAIL_USERNAME=528a733...
    MAIL_PASSWORD=73c29...
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=mygoogle@gmail.com
    MAIL_FROM_NAME="${APP_NAME}"

### Add your slack webhook (Optional)
> To setup your  [slack](https://api.slack.com/messaging/webhooks)  webhook url

    LOG_SLACK_WEBHOOK_URL=https://hooks.slack.com/services/T....

### We are good 😊 
To test this, simply add laravel `abort(500)` method in one of your routes, then hit the route In your browser.


    Route::get('/convert/file', function(){
    	 $array['key1'] = 'john';
        $data  = $array['key2'];
    });

Notice that we are trying to access an array with key2 that doesn’t exist, this will throw an error and the package will send the error as notification ro your email as well as the trace.

Also, you must turn `APP_DEBUG=false` in your .env file

**You should see this**

![alt text](https://img001.prntscr.com/file/img001/Zirxkel4QF-IDBFV5v1veA.png)

 If you don’t  have `NOTIFIER_INSTANT=false` in your .env file, the package sends instant notification to the setup email and slack channel

Also, after user submits the form, the package sends another notification with user error description message.

### Modify Page Design
You can modify the style for the page to suit your taste,
>  **Note:** Be careful not to change the **form route** and **hidden inputs**.

A notification email is sent to the email specified in the laravel environment variable (`MAIL_FROM_ADDRESS`) in your *.env* file

You can change this by adding `NOTIFIER_EMAIL="hello@example.com"` in your .env file.


### Send Email to Multiple Recipients

You can send notification to multiple recipients by adding multiple emails as a string in comma separated format without space

    NOTIFIER_EMAIL="hello1@example.com,hello2@example.com"


### Change Redirect Page URL

You can change the page user is taken to after submitting the form, by default it goes to the home page (/)

    NOTIFIER_REDIRECT_URL='/thank-you'


## Exciting upgrade coming. Stay tuned!

  

## How can I thank you?

Why not star the github repo? I'd love the attention! Why not share the link for this repository on Twitter, LinkedIn or HackerNews? Spread the word!

Don't forget to [follow me on twitter](https://twitter.com/johngodstime)!

Thanks! Godstime John.




