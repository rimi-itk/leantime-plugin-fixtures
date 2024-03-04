# Leantime Fixtures

Load fixtires into Leantime database.

## Installation

Clone this repository into your Leantime plugin folder:

``` shell
git clone https://github.com/rimi-itk/leantime-plugin-fixtures app/Plugins/Fixtures
```

Install and enable the plugin (requires changes from <https://github.com/Leantime/leantime/pull/2364>):

``` shell
bin/leantime plugin:install leantime/fixtures
bin/leantime plugin:enable leantime/fixtures
```

Alternatively, navigate to `/plugins/myapps` and activate the
`leantime/fixtures` plugin.

## Usage

Run this command to load fixtures:

``` shell
bin/leantime fixtures:load
```

## Development

### Coding standards

``` shell
docker run --tty --interactive --rm --volume ${PWD}:/app itkdev/php8.1-fpm:latest composer install
docker run --tty --interactive --rm --volume ${PWD}:/app itkdev/php8.1-fpm:latest composer coding-standards-check
```

### Unit tests

``` shell
docker run --tty --interactive --rm --volume ${PWD}:/app itkdev/php8.1-fpm:latest composer test
```
