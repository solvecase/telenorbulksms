# TelenorBulkSms

[![Latest Version on Packagist][ico-version]][link-packagist]

This package makes it easy to send sms notification using [Telenor Myanmar Business SMS][link-telenor-bulk-messaging] service with laravel. You can access Telenor Myanmar Developer Portal from [here][link-telenor-api-gateway].

## Installation

Via Composer

``` bash
$ composer require solvecase/telenorbulksms
```
## Setting Up
Open up .env file and add the following:

    TELENOR_API_BASE_URL=https://prod-apigw.mytelenor.com.mm/
    TELENOR_SMS_CALLBACK_URL=oauth2/telenorsms/callback
    TELENOR_SMS_CLIENT_ID=
    TELENOR_SMS_CLIENT_SECRET=
    TELENOR_SMS_SENDER=
    TELENOR_SMS_USERNAME=
    TELENOR_SMS_PASSWORD=

Open up *config/app.php* and find the providers key.

```php
'providers' => [
    ...
    SolveCase\TelenorBulkSms\TelenorBulkSmsServiceProvider::class,
]
```
[Laravel Task Scheduling][link-laravel-scheduling] is used to request bearer access token every hour and save it in cache. So, add the following Cron entry to your server interval every minute.

`* * * * * cd /path-to-your-project && php artisan schedule:run >> /dev/null 2>&1`
## Usage
##### Using [Laravel Notification][link-laravel-notification]
```php
<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;

    /**
     * Route notifications for the telenor sms channel.
     *
     * @param  \Illuminate\Notifications\Notification  $notification
     * @return string
     */
    public function routeNotificationForTelenorSms($notification)
    {
        return $this->mobile_no; // 959xxxxxxxx
    }
}
```
```php
<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use SolveCase\TelenorBulkSms\TelenorMessage;
use SolveCase\TelenorBulkSms\TelenorSmsChannel;

class InvoicePaid extends Notification
{

    /**
     * Get the notification channels.
     *
     * @param  mixed  $notifiable
     * @return array|string
     */
    public function via($notifiable)
    {
        return [TelenorSmsChannel::class];
    }

    /**
     * Get the sms representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return TelenorMessage
     */
    public function toTelenorSms($notifiable)
    {
        return TelenorMessage::create()
			->content($notifiable->inovice->id . ' has been paid!');
    }
}
```
```php
$user->notify(new InvoicePaid($invoice));
```
##### Using The Notification Facade
```php
Notification::send($users, new InvoicePaid($invoice));
```
##### Sending Sms using TelenorSmsClient
```php
<?php

namespace App\Http\Controllers;

use SolveCase\TelenorBulkSms\TelenorSmsClient;
use SolveCase\TelenorBulkSms\TelenorMessage;

class SmsController extends Controller{

	public function sendsms(){
		try{
			$message = TelenorMessage::create('Hello, Good Morning!')
				->to(959xxxxxxxx)->toArray();
			$client = new TelenorSmsClient();
			$response = $client->send($message)->getBody();
		}catch(\Exception $ex){
			return back()->withErrors('Something went wrong.');
		}
	}

}

```
## Change log

Please see the [changelog](changelog.md) for more information on what has changed recently.

## Contributing

Please see [contributing.md](contributing.md) for details and a todolist.

## Security

If you discover any security related issues, please email author email instead of using the issue tracker.

## Credits

- [SolveCase][link-author]
- [All Contributors][link-contributors]

## License

license. Please see the [license file](license.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/solvecase/telenorbulksms.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/solvecase/telenorbulksms.svg?style=flat-square
[ico-travis]: https://img.shields.io/travis/solvecase/telenorbulksms/master.svg?style=flat-square
[ico-styleci]: https://styleci.io/repos/12345678/shield

[link-packagist]: https://packagist.org/packages/solvecase/telenorbulksms
[link-downloads]: https://packagist.org/packages/solvecase/telenorbulksms
[link-travis]: https://travis-ci.org/solvecase/telenorbulksms
[link-styleci]: https://styleci.io/repos/12345678
[link-author]: https://github.com/solvecase
[link-contributors]: ../../contributors
[link-telenor-bulk-messaging]: https://www.telenor.com.mm/en/business/bulk-messaging-bulk-sms
[link-telenor-api-gateway]: https://apigw.mytelenor.com.mm/
[link-laravel-scheduling]: https://laravel.com/docs/master/scheduling
[link-laravel-notification]: https://laravel.com/docs/master/notifications