# wpdev

A library for developing modern WordPress themes. It contains both PHP and JavaScript code, so it is necessary to utilize two different package managers. [Composer](https://getcomposer.org/) is used for PHP and [NPM](https://npmjs.com) for JavaScript.

This library is built on top of [Wpack.io](https://wpack.io/), which is a bundling tool that provides an easy-to-use set of tools to develop WordPress themes with the power of [webpack](https://webpack.js.org/).
The library adds some functionality to Wpack.io to allow a component based development approach, like the one seen in many popular front end frameworks like [React](https://reactjs.org/) or [Vue.js](https://vuejs.org/).
The approach let's you easily split up your front end code into components which will only be sent to the client if they are displayed on the page which the client is currently viewing.

In order to give you more power over the data structures your site is based upon, this library uses the pro version of the plugin [Advanced Custom Fields](https://www.advancedcustomfields.com/). With the help of this plugin's input components wpdev allows you to define data schemas for custom post types and single pages.

## Installation

PHP:

```bash
composer require paulkre/wpdev
```

JavaScript:

```bash
npm install @paulkre/wpdev
```

