# Cooarchi Test

A community oriented archive interface.

## Getting Started

Start your new Cooarchi project by cloning git repo to a local folder

```bash
$ git clone <url> <folder>
```

After choosing go to the
`<folder>` and start PHP's built-in web server to verify installation:

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
