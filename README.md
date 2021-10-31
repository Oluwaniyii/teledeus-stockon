Still need to make heavy changes refactor seprate credentials

coming feature
api third party registeration
authentication

to run app
cd into base folder

Import Database
import databse from database/users.sql

run
-composer install
-php -S localhost:800 -t public

send requests to localhost

There Is A Bug With Trailing Slashes

GET users 
/users

single user
/user/id

POST users
/users


DELETE users
/users/id

form format 
{
            "username": "Tom Felton",
            "password": "12345678`",
            "email": "tommy@gmail.com",
            "phone": "+234-111-222-3334",
            "address_building": "39,B5",
            "address_city": "gowon, isolo ",
            "address_state": "Lagos",
            "address_zipcode": "10011"
}

Authentication

Using postman :
    send email and password to authentication route
    {
        "username": "Tom Felton",
        "password": "12345678`",
    }

    A token will be sent back
        -grab token and remove quotation marks
        -navigate to authorization on postman
        -click the dropdown and select bearer;
        -paste token 

    send request to access protected routes, Voila!




