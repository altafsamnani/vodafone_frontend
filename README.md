<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://community.vodafone.nl/legacyfs/online/4266iBA0F575FFE7B9392.png" width="400"></a></p>

## Vodafone Laravel Test

The project is created with
- Laravel
- Docker
- Keycloak Guard to check Resource and allow access
- Artisan Commands
- Phpunit Testcases


<img src="https://raw.githubusercontent.com/robsontenorio/laravel-keycloak-guard/master/flow.png"/>

## Installation

1. Do  ```make setup``` to install the required libraries. We will be using MakeFile magic to setup.

2. Execute ```make up```, which will create the setup for the assignment and installation for you, If you are setting it up first time, please be patient and it will take some time. For Subsequent sail commands it will be quicker.

5. We are using ```socialite provider``` functionality, and our portal will be running on ```http://localhost:81/keycloak/test```

6. We have created a Test page at ```http://localhost:81/keycloak/test ```to authenticate with Keycloak at ```localhost:85/auth``` 


## Explaination

We are using socialite provider as a Frontend to get the token for the resource scope (/permission) which can be then sent to the protected apis. 

Please set the Keycloak variables inside .env file, available variables are
```KEYCLOAK_CLIENT_ID=vodafone-api```
```KEYCLOAK_CLIENT_SECRET=2cba9e0d-1fc4-4c32-904b-3cbb500e4aec```
```KEYCLOAK_REDIRECT_URI=http://localhost:81/keycloak```
```KEYCLOAK_BASE_URL=http://host.docker.internal:85/auth```
```KEYCLOAK_REALM=vodafone-demo```

### Api Endpoints (Inside routes.php)
1) To Test
   http://localhost:81/keycloak/test

2) To Authenticate
   http://localhost:81/keycloak/auth

3) Refresh/Revoke
   http://localhost:81/keycloak/refresh
   http://localhost:81/keycloak/revoke


### Test the application
Test cases are written inside the tests folder. 
You can run tests through following command
- ```make test```




