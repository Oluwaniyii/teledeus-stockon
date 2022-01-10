# Teledeus Oauth Service

A private user authorization service with oauth2.0 integration. 

### Getting Started

To use the oauth server, you need have a developer account first

[ ![Create application form](./_readme/newapp.png) ](https://tldstokon.herokuapp.com/developer)


**1.** head on to [**tldstockon developer console**](https://tldstokon.herokuapp.com/developer)

**2.** login into your developer account
           after a successful login , you will be taken to your dashboard where you can create an application

**3.**. On the top right side click the "**new app**" button to create your application

#### Application Form

[ ![Create application form](./_readme/appcreate.png) ](https://tldstokon.herokuapp.com/developer/app/create)

The application form requires the

**App name :**  The name of your app
<br>
**App description :**  What your app does in a nutshell
<br>
**Client success redirect url** This is where users will be redirected to after a successful authentication request
<br>
**Client error redirect url** If an error is  handleable by your service, the user will be redirected here, if you will be handling both error and success on the same page then you can omit this field
<br>
**App type :** This is essential for the selecting the authorization type suitable for your application

Be aware that only server side apps oauth are supported for now

<br>

### App Credentials

After creating your server side app, you get a **client id** and a **client secret**.
You can view this by clicking created app in your developer dashborad. Use the show credential button to copy your client credentials..
<br>
Be sure to keep this code safe and secured as they  are essential to accessing your application information.

The **client id** is a random 32 string. while the **client secret** is a 64-string-long code.
<br>
Some request might require you to use both client id as secret for verification.
 You can choose to send client credentails using the basic auth header or send it as a get request parameter or post data.
 when using as a basic header make sure it is in this format client_id:client_secret, converted to **base 64 string** 

```Http

Authorization Basic bas_64_string(<client_id>:<client_secret>)

```

<br>

### User Authorization: Authorization code grant

The authorization code is a temporary code that the you will use in exchange for an access token learn more at [oauth.com](https://www.oauth.com/oauth2-servers/server-side-apps/authorization-code/);

You will need to send the user to the oauth servers authorization page through a link or redirect with necessarry parameters value


#### Authorization code request Parameters 

The auth server expects the:

**client_id** : *(required)* your apps client id
<br>
**resonse_type** : *(required)* expected response type which must be set to "*code*", otherwise you get an error
<br>
**redirect_url** : *(required)* this must be the same with the "success rdirect url" provided while creating your app and must be active. **This should be written in url encoded format**
<br>
**scope** : *(required)* the auth server only allows you to pull user details, and doesn't require a scope parameter.
<br>
**state** : *(optional)* the state parameter is used by the client itself for known purpose, on a successful authorization, the auth server will return the exact value as a parameter while redirecting the user. learn more at [oauth.com](https://www.oauth.com/oauth2-servers/server-side-apps/authorization-code/);

##### Example Flow

*This is broken for easy readability* 

 ```php

https://tldstockon/oauth/authorizee
?client_id=41aca2fd4df8cb6c9e9d781e55c31e80
&response_type=code
&state=purchase
&redirect_uri=https%3A%2F%2Fartimeloan.com%2Fauth

```

#### The authorization response

![User Authorization prompt](./_readme/authui.png)

Upon being redirect to the authorization server, the user is shown the authorization prompt
<br>
If the user approves the request, the service redirects the user back to the app with a code and the original state and expiration time in seconds.
the code should then be exchanged for an access token

**Success response**

```php

    https://artimeloan.com/auth?code=CDI61d6bedb58c19hdtCiQEzY3I3WTvdBRbLUAqtc&expires_in=120&state=purchase

```

**Error response**

If the authorization encountered client verification or redirect url error, the user will not be redirect to your app but  will be shown a friendly error from the authorization server like the one below

![Server Authorization error](./_readme/authclerr.png)*Server authorization Error*

Otherwise, the user will be redirect back to your app but with an error and error descrption  paramters, it is advisable to show a custom user-friendly error 

```php

    https://artimeloan.com/auth?error=access_denied&error_description=the+user+denied+access

```

**unsupported_response_type** : This error is sent back when your *response_type* parameter is not set to code. This error specifies that the authorization server can't provide your response in the format requested 
<br>
**unauthorized_client** : This is sent back when the client is not authorized to request an authorization code using the method
<br>
**access_denied** : This is sent back when the user denies your app acess

<br>

### Obtaining Acess Token 

Finally the app uses the authorization code to get an access token by making a HTTPS POST request to the token endpoint

The auth server uses long term token so it wont issue a refresh token. The token can be used over and overfor a maximum of 30 days before it expires

***Sample Request***
```http

POST /oauth/token HTTP/1.1
Host: https://tldstockon.herokuapp.com
Content-Type: application/json

{
    "grant_type" : "authorization_code",
    "code" : "CDB61d76315e42ddSLb7qGjIZh6dLFwVEUXdCkudq",
    "redirect_url" : "http://artimeloan.com/auth",
    "client_id" : "41aca2fd4df8cb6c9e9d781e55c31e80",
    "client_secret" : "wltFdlRhDZKNtsFINElvZVxo8MryJriOxVxr1XUxvbzAYeQWK8axIPSfS0ib7L7M"
}

```
**grant_type** : the access token grant type must be set to *authorization_code*

**code** : previously issued authorization code

**redirect_url** : this must be the same with the success redirect url provided when creating your app

**client_id** : your app's client id, you can find thin in your developer dashboard [developer dashboard](https://tldstokon.herokuapp.com/developer)

**client_secret** : your app's client secret

any wrong value will return an "invalid_grant", error response

***Sample success response***
```json

{
    "access_token": "TKN161d765ac14ece3A6yvxMzXgKf8t2A33dBHaTxD",
    "token_type": "Bearer",
    "issued_at": 1641506220,
    "expires_in": 2592000
}

```

***Sample error response***
```json

{
    "error": "invalid_grant",
    "error_description": "code is invalid"
}

```
If anything goes wrong in the request the server returns an error and a good description of the error

**Error flags**

The flags descriptions are exhibited just to know what to expect

 **INVALID_REQUEST_ERROR :** "invalid or missing request parameters"
<br>
 **INVALID_CLIENT_ERROR :** "invalid or missing client credentials"
<br>
 **INVALID_AUTH_CODE_ERROR :** "code is invalid";
<br>
 **WRONG_CLIENT_ERROR :** "client is not the authorised owner of code"
<br>
 **INVALID_URL_GRANT_ERROR :** "provided url does not match app url";
<br>
 **EXPIRED_CODE_ERROR :** "code is expired"
<br>
 **UNAUTHORIZED_CLIENT_ERROR :** "client is not to use specified grant type";
<br>
 **SERVER_ERROR :** something went wrong on our end"

<br>

### Pulling User data

After obtaining an access token, you can then use the access token to pull out authorised user detail. An attempt to pull out any user other than the specific user who granted access will fail.
It is important you store the token in a Bearer authorization header like below

***Sample rquest response***
```http

GET /api/user/uid61cc1d156bd6f?client_id=41aca2fd4df8cb6c9e9d781e55c31e80 HTTP/1.1
Host: https://tldstockon.herokuapp.com
Authorization: Bearer TKNN61d6bf016d2eaIrM1VaIuN3quneKQh3Z4jl8WQ

```
***Sample success response***

```json

{
    "status": "Success",
    "data": {
        "unique_id": "uid61cc1d156bd6f",
        "username": "John",
        "email": "john@gmail.com",
        "phone": "111-222-3333",
        "address": {
            "building": "",
            "city": "iyana-ipaja",
            "state": "Lagos",
            "zip": "10111"
        },
        "joined": "2021-12-29 02:32:21"
}
}

```
 Any request error will return a **401 Unauthorised** response

<br>

 ###  User Revoking Access

<br>

 Users are allowed to revoke an access at any time given, when you try to make a request using revoked access token , you get an unauthorized response

**Request**

```http

GET /api/user/uid61cc1d156bd6f?client_id=41aca2fd4df8cb6c9e9d781e55c31e80 HTTP/1.1
Host: https://tldstockon.herokuapp.com
Authorization: Bearer TKNN61d6bf016d2eaIrM1VaIuN3quneKQh3Z4jl8WQ

```

**Response**

```http

401 Unauthorized

```

 You can learn to check token status below

<br>

 ### Token info Request 

 The token info endpoint accepts a post request with **token** parameter and client credentials
<br>
 You can choose to send client credentials using the basic auth header or send it as a post data
<br>
 The content type must be set to application/json

**Sapmle Request**

```http
POST /oauth/tokeninfo HTTP/1.1
Host: https://tldstockon.herokuapp.com
Content-Type: application/json
Authorization: Basic NDFhY2EyZmQ0ZGY4Y2I2YzllOWQ3ODFlNTVjMzFlODA6d2x0RmRsUmhEWktOdHNGSU5FbHZaVnhvOE1yeUpyaU94VnhyMVhVeHZiekFZZVFXSzhheElQU2ZTMGliN0w3TQ==
Content-Length: 64

{
    "token" : "TKNN61d6bf016d2eaIrM1VaIuN3quneKQh3Z4jl8WQ"
}

```


**Sapmle Response**

```json

{
"access_token": "TKNN61d6bf016d2eaIrM1VaIuN3quneKQh3Z4jl8WQ",
"client_id": "41aca2fd4df8cb6c9e9d781e55c31e80",
"username": "Ayodeley",
"valid": true,
"expiration_time": 1644055553,
"is_expired": false,
"revoked": false
}

```

The **username** points to the username of authorised user 
<br>
The **valid** index tells if the token is still valid.
<br>
either the **is_expired** or **revoked** will be true if valid is false.
<br>

 ### New Client Credentials 

If your client credentials has been exposed or you feel like your credentials are no longer secure, you can request for new credentials, but this will cost your app to request authorization again.

##### Requesting new credentials

**1.** Head on to your dashboard

**2.** Select an application 

**3.** Head down to settings and click the new credentials button. It happens in seconds

![App New Credentials Request](./_readme/nwcredraw.png)*New Credentials request*
`
