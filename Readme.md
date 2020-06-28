### SaW (Ships at War) - battleship API

This is an API for playing battleship (rules can be found [here](https://www.hasbro.com/common/instruct/Battleship.PDF)) 
using symfony framework (MicroKernel) with [api-platform/core](https://github.com/api-platform/core) package for basic CRUD actions.

API definition files can be found inside [doc/](doc/) path for several tools like Postman and others.

#### Install

    # checkout project & cd into project
    > ./composer.phar install
    # start php's internal web server or drop project sources into a web root
    > php -S localhost:8000 -t public/

Open a browser at http://localhost:8000 to see symfony's welcome page.

At http://localhost:8000/api you find a Swagger UI or ReDoc for testing the API. At http://localhost:8000/api/docs.json a
json OpenApi definition is available for import into Postman / Paw or similar.

To minify setup project is using a SQLite database located in var/database. To change eg. to MySQL just change the dsn
string in .env file.

    # SQLite (current)
    DATABASE_URL=sqlite:///%kernel.project_dir%/var/database/SaW.db
    # MySQL (example)
    DATABASE_URL=mysql://USER:PASS@127.0.0.1:3306/DATABASE?serverVersion=5.5
    
Be sure to run `bin/console doctrine:schema:create` to create schema and `bin/console doctrine:fixtures:load` to load
ship fixtures. 

#### Features (so far)

* validation using EventSubscriber
* GridFactory service for easy loading saved grid layouts
* Ships handled in database
* 4 grid boards per game - 2 for user 2 for a bot - one for placing ships and one for storing shots or hits
* prepared variable grid size (default to 10x10)

#### Game flow overview

* start a new game (post playerName)
* place all your ships
* start shooting

#### Saw game endpoints overview

See more details in swagger UI

* POST /api/saw/start
* GET  /api/saw/{id}
* PUT  /api/saw/{id}/place_ship
* POST /api/saw/{id}/shot
* ~~GET  /api/saw/{id}/shot~~

#### Roadmap

* finish unit tests
* validate all requests for custom operations
* implement a bot to play against
* finish salve mode
* enhance user managing (unique users)
* build a frontend (new project!)
