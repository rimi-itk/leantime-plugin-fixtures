# Leantime Fixtures

Load fixtires into Leantime database.

## Installation

Clone this repository into your Leantime plugin folder:

``` shell
git clone https://github.com/rimi-itk/leantime-plugin-fixtures app/Plugins/Fixtures
```

Navigate to `/plugins/myapps` and activate the `leantime/Fixtures` plugin.

## Usage

Run this command to load fixtures:

``` shell
bin/leantime fixtures:load
```

## Development

### Coding standards

``` shell
docker run --rm --volume ${PWD}:/app --env COMPOSER=composer.dev.json itkdev/php8.1-fpm:latest composer install
docker run --rm --volume ${PWD}:/app --env COMPOSER=composer.dev.json itkdev/php8.1-fpm:latest composer coding-standards-check
```
