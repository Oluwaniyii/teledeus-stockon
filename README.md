<!-- 
PAGINATION 
 -->


<!-- Getting Started -->
# Teledeus Oauth Service

## Getting Started

To use the oauth server, you need have a developer account first

1 head on to  [**remote website**](https://tldstockon.herokuapp.com/developer) login into your developer account
after a successful login , you will be taken to your dashboard where you can create an applicion

2 On the top right side click new app from the form select an app type

**The information to be provided for app creation**
**The app name**  The name of your app
**App description**  What your app does in a nutshell
**App type** This is essential for the selecting the authorization type suitable for your application, only server side apps are supported for now.

**Client and error redirect urls** This is where users will be redirected to after authentication request, if you will be handling both error and success on the same page then you can omit the error redirect space

## client ID and Secret
After creationg your server side app, you get a **client id** and **client secret** code, be sure to keep this code safe and secured as they  are essential to accessing your application.

The client id is a random 32 string
while the client secret is a 64-string-long code

# User Authorization: authorization code grant


<!-- subheading -->
<!--  -->

 <!-- Obtaining Token Code -->

 <!-- Access Token Request -->

 <!-- Pulling User Data -->

 <!-- User revoking Access -->

 <!-- Client credentials change -->