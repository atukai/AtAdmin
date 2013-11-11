# AtAdmin

The missing ZF2 Admin module constructor.

## Requirements

* [Zend Framework 2](https://github.com/zendframework/zf2)
* [ZfcAdmin](https://github.com/ZF-Commons/ZfcAdmin)

## Features

* Theme based on [Twitter Bootstrap](http://twitter.github.com/bootstrap/)
* Custom layout for [ZfcAdmin](https://github.com/ZF-Commons/ZfcAdmin) with two-level menu

## Installation

 1. Add `"atukai/at-admin": "dev-master"` to your `composer.json` file and run `php composer.phar update`.
 2. Add `AtAdmin` to your `config/application.config.php` file under the `modules` key after `ZfcAdmin`.
 3. Copy or create a symlink of public/css, public/js and public/images to your website root directory

## Configuration

### Layout
AtAdmin ships with built-in layout which override default ZfcAdmin layout.
To override one with your custom layout follow to the next steps

1. In your module under the `view` directory create the folder `layout`
2. Create the override script `admin.phtml`