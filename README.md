# AtAdmin

Version 0.1.3

The missing ZF2 Admin module constructor.

## Requirements

* [Zend Framework 2](https://github.com/zendframework/zf2)
* [ZfcAdmin](https://github.com/ZF-Commons/ZfcAdmin)
* [AtDataGrid](https://github.com/atukai/AtDataGrid)
* [BjyModulus](https://github.com/bjyoungblood/BjyModulus) (optional)

## Features

* Theme based on [Twitter Bootstrap](http://twitter.github.com/bootstrap/)
* Custom layout for [ZfcAdmin](https://github.com/ZF-Commons/ZfcAdmin) with two-level menu
* Integration with [AtDataGrid](https://github.com/atukai/AtDataGrid)
* Integration with [BjyModulus](https://github.com/bjyoungblood/BjyModulus)

## Installation

 1. Add `"atukai/at-admin": "0.*"` to your `composer.json` file and run `php composer.phar update`.
 2. Add `AtAdmin` to your `config/application.config.php` file under the `modules` key after `ZfcAdmin`.
 3. Copy or create a symlink of public/css, public/js and public/images to your website root directory

## Configuration

See [AtCms](https://github.com/atukai/AtCms) as example.

### Layout
AtAdmin ships with built in layout which override default ZfcAdmin layout.
To override the built in admin layout with your custom layout follow to the next steps

1. In your module under the `view` directory create the folder `layout`
2. Create the override script `admin.phtml`