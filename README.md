# PHP-API Project 
This project creates a RESTful API for managing users: 
- Create User
- Update Name (Forename)
- Toggle DarkMode
- Delete User
- List Users
- Search 

##Instructions

1. Download the project.

2. Create the database.

3. Change .env file on the following fields. 
DB_DATABASE=CHANGE HERE
DB_USERNAME=CHANGE HERE
DB_PASSWORD=CHANGE HERE

4. Run the command line to create the table (table details are in the file under app/database/migrations/2021_02_01_170139_create_users_table.php):
```
$ php artisan migrate
```
5. Import users.csv (provided) file to the database table.

#### Endpoints Authentication

1. Each endpoint must receive a header Bearer token in the following format: 'username:token'

2. The default token is 'protected' as you can see on the users.csv file. During the user creation the token can be set as a parameter and is stored in the database. The idea here would be to allow each user created to make use of the endpoints. 

3. The minimum token size is 8 characters.


#### Testing the API

1. Start the server using the command line:

```bash
$ php -S localhost:9291 -t public 
```

#### Endpoints
 
##### Create User

**/api/createuser**

1. Method: POST

2. Mandatory fields: forename, surname, darkmode, token.

3. Sample curl testing:
```bash
$ curl --location --request POST 'http://localhost:9291/api/createuser' \
--header 'Authorization: Bearer alinechribeiro:protected' \
--form 'forename="Aline"' \
--form 'surname="Ribeiro"' \
--form 'username="alinech"' \
--form 'darkmode="0"' \
--form 'created_at="'\''2021-01-09'\''"' \
--form 'token="protected"'
```

**/api/listusers**

1. Method: GET

2. Mandatory fields: forename, surname, darkmode, token.

3. Sample curl testing:
```bash
$ curl --location --request POST 'http://localhost:9291/api/createuser' \
--header 'Authorization: Bearer alinechribeiro:protected' \
--form 'forename="Aline"' \
--form 'surname="Ribeiro"' \
--form 'username="alinech"' \
--form 'darkmode="0"' \
--form 'created_at="'\''2021-01-09'\''"' \
--form 'token="protected"'
```

**/api/searchuser**

1. Method: POST

2. The following fields are mandatory to be sent on the request.
	*  identifier: string. > Column to be searched e.g.: 'forename'.
	*  identifierField: string. > Value to search for e.g.: 'Tom'.
	*  fields: array. > Which fields will be returned on the response e.g.: ['surname', 'darkmode'] or use ['*'] for all.

3. Sample curl testing:
```bash
curl --location --request POST 'http://localhost:9291/api/searchuser/' \
--header 'Authorization: Bearer alinechribeiro:protected' \
--header 'Content-Type: application/json' \
--data-raw '{
    "identifier": "forename",
    "identifierField": "John",
    "fields": ["surname","darkmode","username","created_at"]
}'
```

**/api/deleteuser/{id}**

1. Method: GET

2. Mandatory fields: id of the user to be deleted.

3. Sample curl testing:
```bash
curl --location --request GET 'http://localhost:9291/api/deleteuser/4' \
--header 'Authorization: Bearer alinechribeiro:protected'
```

**/api/updateuser**

1. Method: POST

2. Mandatory fields: id and forename to be updated.

3. Sample curl testing:
```bash
curl --location --request POST 'http://localhost:9291/api/updateuser/' \
--header 'Authorization: Bearer alinechribeiro:protected' \
--form 'id="3"' \
--form 'forename="Mary"'
```

**/api/togglemode/{id}**

1. Method: GET

2. Mandatory fields: id.

3. Sample curl testing:
```bash
curl --location --request GET 'http://localhost:9291/api/togglemode/3' \
--header 'Authorization: Bearer alinechribeiro:protected'
```
	
#### PHP Unit Tests: 
The PHPUNIT will test all the endpoints calls.
On the project root directory, run: 
```bash
$ vendor/bin/phpunit
```

Expected result: 6 tests, 6 assertions.

#### Testing with Postman:

Import the collection file 'Protected.postman_collection.json' into your Postman software and execute the requests. 
