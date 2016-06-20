Simple authentication extension for Yii 2
=========================================

Yii 2 extension that provides simple authentication based on secret key.

Extension provide components for easy authenticate and validate request. Each request get its own
unique token with expiration time, so no passwords or keys are send with request - it should be
safer than [basic access authentication](https://en.wikipedia.org/wiki/Basic_access_authentication)
when you don't use https.

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require salenauts/yii2-simple-auth "*"
```

or add

```
"salenauts/yii2-simple-auth": "*"
```

to the require section of your `composer.json` file.


Usage
-----

### Configuration

You can configure default secret key used by this extension by setting param in your config in
`config/web.php` and/or in `config/console.php`:

```php
return [
	...
	'params' => [
        ...
		'simpleauth' => [
			'secret' => 'mysecretkey',
		],
	],
];
```

This is optional - you can always explicitly specify the key for authentication/validation.


### Authentication (client side)

#### Authentication when using official `yii2-httpclient` extension

You can simply authenticate Request object from official Yii 2 [http-client](https://github.com/yiisoft/yii2-httpclient):

```php
use yii\httpclient\Client;
use salenauts\simpleauth\YiiAuthenticator as Authenticator;

$client = new Client();
$request = $client->createRequest()
	->setUrl('http://api.example.com/user/list/')
	->setData(['ids' => '1,2,3,4']);
$request = Authenticator::authenticate($request);
$response = $request->send();
```

By default Authenticator send authentication token in header of request. Alternatively you can send
it as param in GET or POST request. Be careful when you use POST method, becouse this will set
request method as POST and all data set by `setData()` will be send as POST data and will not be
included in the URL.

Authentication by GET param with custom secret key:

```php
use yii\httpclient\Client;
use salenauts\simpleauth\YiiAuthenticator as Authenticator;

$client = new Client();
$request = $client->createRequest()
	->setUrl('http://api.example.com/user/list/')
	->setData(['ids' => '1,2,3,4']);
$request = Authenticator::authenticate($request, Authenticator::METHOD_GET, 'mycustomsecretkey');
$response = $request->send();
```

Authentication by POST param:

```php
use yii\httpclient\Client;
use salenauts\simpleauth\YiiAuthenticator as Authenticator;

$client = new Client();
$request = $client->createRequest()
	->setUrl('http://api.example.com/user/list/?ids=1,2,3,4');
$request = Authenticator::authenticate($request, Authenticator::METHOD_POST);
$response = $request->send();
```

#### Authentication any request

You can use Authenticator to authenticate any request, even if you you don't use `yii2-httpclient`
package. For example, authentication cURL request by GET param:

```php
use salenauts\simpleauth\Authenticator;

$ch = curl_init();
$url = 'http://api.example.com/user/list/?ids=1,2,3,4';
$url .= '&' . Authenticator::PARAM_NAME . '=' . Authenticator::generateAuthToken($url);
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);
```

Authentication cURL request by header:
```php
use salenauts\simpleauth\Authenticator;

$ch = curl_init();
$url = 'http://api.example.com/user/list/?ids=1,2,3,4';
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
	Authenticator::HEADER_NAME . ': ' . Authenticator::generateAuthToken($url),
]);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
$response = curl_exec($ch);
curl_close($ch);
```


### Validation (server side)

To check whether the request has a valid token simply add action filter to your controller:

```php
use salenauts\simpleauth\ActionFilter;

class MyController extends \yii\web\Controller {

	public function behaviors() {
		return [
            ...
			'simpleauth' => [
				'class' => ActionFilter::className(),
			],
		];
	}

	...
}
```

You can also configure some settings for filter:

```php
use salenauts\simpleauth\ActionFilter;
use salenauts\simpleauth\Authenticator;

class MyController extends \yii\web\Controller {

	public function behaviors() {
		return [
            ...
			'simpleauth' => [
				'class' => ActionFilter::className(),
				// allow authentication only by header
				'allowedMethods' => [
					Authenticator::METHOD_HEADER,
				],
				// set token timeout to 1 hour (by default it is 5 minutes)
				'tokenDuration' => 3600,
				// override default header used for authentication
				'headerName' => 'X-My-Custom-Header',
				// override params names used for send authentication token
				'postParamName' => 'my_custom_token_param_name',
				'getParamName' => 'my_custom_token_param_name',
				// custom secret used for validate authentication
				'secret' => 'mycostomsecretkey',
			],
		];
	}

	...
}
```

### Final comments

Make sure that you generate token for final URL and no redirects are performed for request.
Token is generated for the exact address, so tokens for:
* `http://example.com/user/list/`
* `https://example.com/user/list/`
* `http://www.example.com/user/list/`
* `http://example.com/user/list`

will be completely different.

Be careful when using POST request. `Authenticator` and `ActionFilter` takes into account only the
URL, all POST data is ignored during the authentication and validation. This means that one token
may be used many times for different requests with different POST data if refer to the same URL.
