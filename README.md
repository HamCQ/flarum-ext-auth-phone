# Flarum-ext-auth-phone

A [Flarum](http://flarum.org) extension. auth by phone sms once code

**This plug-in currently only allows verification of mobile phone numbers in China, and only support for aliyun(alibabacloud) service**

**此插件目前只允许验证大中华地区手机号码，且仅支持阿里云短信服务**

### Installation

Use [Bazaar](https://discuss.flarum.org/d/5151-flagrow-bazaar-the-extension-marketplace) or install manually with composer:

```sh
composer require hamcq/flarum-ext-auth-phone
```
  
```sh
php flarum hamcq:aesKey:build
```

### Updating

```sh
php flarum migrate

composer update hamcq/flarum-ext-auth-phone
```

### Links

- [Packagist](https://packagist.org/packages/hamcq/flarum-ext-auth-phone)

```
 "require-dev": {
        "hamcq/flarum-ext-auth-phone":"@dev"
    }
```

php flarum migrate
