# Expense CRUD API

## Description
This app is simple CRUD API that supports listing, reading, creating, updating and deleting expense resource. 
Expense is simple entity that has Price (non zero) stored in subunit, currency stored as ISO code, description for the expense and type from pre-defined set of types.

## Installation
* Run `docker-compose up -d`
* Using your MySQL db client create new databases: md-group, md-group_test
* Run `docker exec -it md-app bash`
* Run `cp .env.example .env`
* Run `cp .env.example .env.test`

* Set the correct variable in .env (for .env.test in database name use db name md-group not md-group_test)

* Run `composer install`

* Run `php bin/console doctrine:migrations:migrate`

## Test API
Using Postman Or any other client test APIs:
#### * Listing
@GET /expenses
    - page: 0 or greater
    - countPerPage: 1 or greater
#### * Show item
@GET /expenses/{id}

#### * Create Item
@POST /expenses
    - description (10000 Char)
    - Price (decimal > 0)
    - Currency (ISO Code)
    - Type (see enum ExpenseType)
#### * Update Item
@PUT /expenses/{id}
    - description (10000 Char)
    - Price (decimal > 0)
    - Currency (ISO Code)
    - Type (see enum ExpenseType)
#### * Delete item
@DELETE /expenses/{id}
## Tests
To Run tests run:
`./run-test.sh`
