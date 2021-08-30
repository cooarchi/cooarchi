# cooArchi

a community oriented archive interface.

cooArchi is like a conversation,you can tell stories, relate to what others said and share your perspective. You can browse, find stories and get lost. It is an archive that we are building together. PART OF IT YOU ARE.

## Requirements

- Web Server of your choice: Nginx, Apache, Caddy
- PHP 7.4
- MySQL Database

## Setup/Installation guide

see documentation [here](https://cooarchi.github.io/documentation/)

## Frontend related Routes

- `/` - start website
- `/help` - shows help page
- `/login` and `/logout` - login/logout
- `/register/:hash:` - register a new kollektivistA Account with provided invitation link

## Admin Routes

- `/users` - manage registrated user
- `/invitations` - manage invitations
- `/content-management` - remove content (element, element relations)
- `/file-management` - remove files

## API Endpoints

- GET `/authstatus` - returns 404 or 202 status code with logged in user objec
- GET `/data` and `/data?delta=1` - returns list of elements and element relations
- POST `/save` - save elements and relations
- POST `/upload` - save file on server and create DB file entry
- GET `/settings` - returns existing cooArchi config

## CLI tools

- `vendor/bin/laminas`
- `vendor/bin/doctrine`

## Getting Started

Start your new Cooarchi project by cloning git repo to a local folder

```bash
$ git clone <url> <folder>
```

Clone `https://github.com/cooarchi/cooarchi-ui` into `public/ui` folder after that.

Install [composer](https://getcomposer.org) global or download phar inside project folder.

Run following commands then:

```bash
$ php composer.phar install
```

```bash
$ vendor/bin/laminas cooArchi:setup
```

```bash
$ vendor/bin/laminas cooArchi:create-administrata
```

```bash
$ vendor/bin/doctrine orm:generate-proxies
```

```bash
$ vendor/bin/doctrine orm:schema-tool:update --dump-sql
```

```bash
$ chmod 777 public/files
```

Copy SQL queries and execute them inside your DB setup.

You should be able to run the app now.

Use PHP internal server or something like Nginx or Caddy (preferred for local SSL support).

```bash
$ php -S 0.0.0.0:8080 -t public/ public/index.php
```

You can then browse to http://localhost:8080.

### To enable development mode

**Note:** Do NOT run development mode on your production server!

```bash
$ composer development-enable
```

**Note:** Enabling development mode will also clear your configuration cache, to 
allow safely updating dependencies and ensuring any new configuration is picked 
up by your application.

### To disable development mode

```bash
$ composer development-disable
```

### Development mode status

```bash
$ composer development-status
```

## Configuration caching

By default, the skeleton will create a configuration cache in
`data/config-cache.php`. When in development mode, the configuration cache is
disabled, and switching in and out of development mode will remove the
configuration cache.

You may need to clear the configuration cache in production when deploying if
you deploy to the same directory. You may do so using the following:

```bash
$ composer clear-config-cache
```

You may also change the location of the configuration cache itself by editing
the `config/config.php` file and changing the `config_cache_path` entry of the
local `$cacheConfig` variable.

just an editing testi test
