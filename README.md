# AtAdmin [1.0.0]

The missing ZF2(3) аdmin panel constructor.

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/atukai/AtAdmin/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/atukai/AtAdmin/?branch=master)

## Requirements

* [Zend Framework 2](https://github.com/zendframework/zf2)
* [AtBase](https://github.com/atukai/AtBase)
* [AtDataGrid](https://github.com/atukai/AtDataGrid)
* [Bootstrap](http://getbootstrap.com/)

## Features

* Theme based on [Twitter Bootstrap 3](http://getbootstrap.com)
* Additional [Font Awesome](http://fortawesome.github.io/Font-Awesome) icons
* Integration with [AtDataGrid](https://github.com/atukai/AtDataGrid) for data grids manipulations

## Installation

 1. Add `"atukai/at-admin": "~1.0.0"` to your `composer.json` file and run `php composer.phar update`.
 2. Add `AtAdmin` to your `config/application.config.php` file under the `modules` key.
 3. Copy or create a symlink of public/css, public/js and public/images to your website root directory

## Configuration