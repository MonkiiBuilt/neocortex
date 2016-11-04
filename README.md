# neocortex

> _neocortex_ (Latin for "new bark" or "new rind")
> a part of the cerebral cortex concerned with sight and hearing in mammals, regarded as the most recently evolved part of the cortex.

The neocortex system is a collaborative display system built in Laravel and Vue.js

## For developers

neocortex is built in Laravel 5.3 for user registration, backend API and persistence.  The front end is built with Vue.js components.

### Dependencies

To develop neocortex you will need:

* [Virtualbox](https://www.virtualbox.org/wiki/Downloads)
* [Vagrant](https://www.vagrantup.com/downloads.html)
* [npm and node.js](https://nodejs.org/en/download/)
* [gulp](https://github.com/gulpjs/gulp/blob/master/docs/getting-started.md)
* [Composer](https://getcomposer.org/download/)

### Getting started

After checking out the project and installing software dependencies, it should be as simple as navigating to the project folder and typing:

```
cd neocortex
vagrant up
```

The project should then be accessible at http://localhost:9999

### Front end

Front end is built using [Vue.js](https://vuejs.org/guide/) and [SCSS](http://sass-lang.com/guide).

To compile front end resources (located in `resources/assets`) use:
```
gulp
```
or
```
gulp watch
```

### Back end

neocortex uses [Laravel](https://laravel.com/docs/5.3/) and Postgres on the backend.

## neocortex is proudly developed by [Monkii](http://www.monkii.com.au)