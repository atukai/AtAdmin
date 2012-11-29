# AtAdmin

Version 0.0.1

AtAdmin is a [ZfcAdmin](https://github.com/ZF-Commons/ZfcAdmin) based [Zend Framework 2](http://framework.zend.com) module 
which provides a admin panel functionality. It ships with components to generate grids and forms.

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