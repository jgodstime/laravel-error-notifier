# Error Notification For Laravel

This package allows users to send you messages via email whenever they get 500 internal server errors.

![alt text](https://img001.prntscr.com/file/img001/Zirxkel4QF-IDBFV5v1veA.png)


## What the package sends as a notification
- Last route the user visited  
- If they are authenticated or not  
- User ID  (If user is authenticated)
- User email  (If user is authenticated)
- User error description message  
- Error status code


## How to install

    composer require jgodstime/laravel-error-notifier

### Publishing the vendor files
By default, Laravel displays the 500 error page in your */views/errors/500.blade.php* file when a 500 error occurs in your application  
  
This package places the 500.blade.php file in the */views/errors/* folder after you publish the vendor file  
  
If you already have a 500.blade.php file in your */views/errors* folder and you want to use this package; you should remove or move it to another folder.

### Publish the vendor files

    php artisan vendor:publish --provider="ErrorNotifier\Notify\ErrorNotifierServiceProvider"

*Now the vendor files are published in their respective paths*
### Let's test
To test this, simply add laravel `abort(500)` method in one of your routes, then hit the route In your browser.

    Route::get('/convert/file',  function(){
	    abort(500);
    });

**You should see this**
![alt text](https://img001.prntscr.com/file/img001/Zirxkel4QF-IDBFV5v1veA.png)



> Don't forget to configure your email driver, you can use [mailtrap](https://mailtrap.io/) for test purpose.

    MAIL_DRIVER=smtp
    MAIL_HOST=smtp.mailtrap.io
    MAIL_PORT=587
    MAIL_USERNAME=528a733...
    MAIL_PASSWORD=73c29...
    MAIL_ENCRYPTION=tls
    MAIL_FROM_ADDRESS=mygoogle@gmail.com
    MAIL_FROM_NAME="${APP_NAME}"

### Modify Page Design
You can modify the style for the page to suit your taste, 
> **Note:** Be careful not to change the **form route** and **hidden inputs**.

A notification email is sent to the email specified in the laravel environment variable (`MAIL_FROM_ADDRESS`) in your *.env* file  
  
You can change this by adding `NOTIFIER_EMAIL="hello@example.com"` in your .env file.  

### Send Email to Multiple Recipients
 You can send notification to multiple recipients by adding multiple emails as a string in comma separated format without space

    NOTIFIER_EMAIL="hello1@example.com,hello2@example.com"

### Change Redirect Page URL
You can change the page  user is taken to after submitting the form, by default it goes to the home page (/)  

    NOTIFIER_REDIRECT_URL='/thank-you'

### Display Message on The Redirected Page
To display message on the redirected page after form is submitted, add the below code in the page

    @if(session()->has('success'))  
    	<div class="alert alert-success">  
    		{{  session()->get('success')  }}  
    	</div> 
    @endif

  
## Exciting upgrade coming. Stay tuned!  
  

## How can I thank you?

Why not star the github repo? I'd love the attention! Why not share the link for this repository on Twitter, LinkedIn or HackerNews? Spread the word!

Don't forget to  [follow me on twitter](https://twitter.com/johngodstime)!

Thanks! Godstime John.
