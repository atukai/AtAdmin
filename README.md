# AtAdmin

Version 0.1.0

A [Zend Framework 2](http://framework.zend.com) module based on [ZfcAdmin](https://github.com/ZF-Commons/ZfcAdmin) and provides an extra admin panel functionality.

## Requirements

* [Zend Framework 2](https://github.com/zendframework/zf2)
* [ZfcAdmin](https://github.com/ZF-Commons/ZfcAdmin)


## Installation

 1. Add `"atukai/at-admin": "dev-master"` to your `composer.json` file and run `php composer.phar update`.
 2. Add `AtAdmin` to your `config/application.config.php` file under the `modules` key after `ZfcAdmin`.

## Configuration

See [AtCms](https://github.com/atukai/AtCms) as example.

### Layout
AtAdmin ships with built in layout which override default ZfcAdmin layout.
To override the built in admin layout with your custom layout follow to the next steps

1. In your module under the `view` directory create the folder `layout`
2. Create the override script `admin.phtml`