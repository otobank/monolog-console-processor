Monolog Console Processor
======================

Monolog Processor for Symfony console component.
Add extra data about console. (The current implementation is only command name)

Usage
-----

### Symfony

example) **`app/config/config.yml` or `app/config/services.yml`**

```yaml
services:
    monolog.processor.session_request:
        class: Otobank\Monolog\Processor\ConsoleProcessor
        tags:
            - { name: monolog.processor }
            - { name: kernel.event_subscriber }
```


Installation
------------

```
composer require otobank/monolog-console-processor
```


Author
------

Toshiyuki Fujita - tfujita@otobank.co.jp - https://github.com/kalibora


License
-------

Licensed under the MIT License - see the [LICENSE](LICENSE) file for details


----

OTOBANK Inc.
