# Country Store Module for Magento 2

[![Latest Stable Version](https://img.shields.io/packagist/v/opengento/module-country-store.svg?style=flat-square)](https://packagist.org/packages/opengento/module-country-store)
[![License: MIT](https://img.shields.io/github/license/opengento/magento2-country-store.svg?style=flat-square)](./LICENSE) 
[![Packagist](https://img.shields.io/packagist/dt/opengento/module-country-store.svg?style=flat-square)](https://packagist.org/packages/opengento/module-country-store/stats)
[![Packagist](https://img.shields.io/packagist/dm/opengento/module-country-store.svg?style=flat-square)](https://packagist.org/packages/opengento/module-country-store/stats)

This module add the many countries to many stores relation and make it available to the storefront.

 - [Setup](#setup)
   - [Composer installation](#composer-installation)
   - [Setup the module](#setup-the-module)
 - [Features](#features)
 - [Settings](#settings)
 - [Documentation](#documentation)
 - [Support](#support)
 - [Authors](#authors)
 - [License](#license)

## Setup

Magento 2 Open Source or Commerce edition is required.

###  Composer installation

Run the following composer command:

```
composer require opengento/module-country-store
```

### Setup the module

Run the following magento command:

```
bin/magento setup:upgrade
```

**If you are in production mode, do not forget to recompile and redeploy the static resources.**

## Features

### Country to store mapping

Define many countries to many stores relation. This configuration will allows Magento to map stores with countries.

## Settings

The configuration for this module is available in `Stores > Configuration > General > Country Store`.  

## Documentation

### How to add a country resolver

Create a new final class and implements the following interface: `Opengento\CountryStore\Api\CountryResolverInterface`.
The method `public function getCountry(): CountryInterface` should return the default country depending of the context.
The country code should be compliant to ISO 3166-1 alpha-2 format.

Register the new country resolver in the method factory, `Vendor/Module/etc/di.xml`:

```xml
<type name="Opengento\CountryStore\Model\Resolver\ResolverFactory">
    <arguments>
        <argument name="countryResolvers" xsi:type="array">
            <item name="customCountryResolver" xsi:type="string">Vendor\Module\Model\Country\Resolver\CustomCountryResolver</item>
        </argument>
    </arguments>
</type>
```

If you want the resolver to be available in settings, add it to the resolver list, `Vendor/Module/etc/di.xml`:

```xml
<virtualType name="Opengento\CountryStore\Model\Config\Source\CountryResolver">
    <arguments>
        <argument name="options" xsi:type="array">
            <item name="customCountryResolver" xsi:type="array">
                <item name="label" xsi:type="string" translatable="true">Custom Country Resolver</item>
                <item name="value" xsi:type="const">Vendor\Module\Model\Country\Resolver\CustomCountryResolver::RESOLVER_CODE</item>
            </item>
        </argument>
    </arguments>
</virtualType>
```

The country resolver is ready to use.

## Support

Raise a new [request](https://github.com/opengento/magento2-country-store/issues) to the issue tracker.

## Authors

- **Opengento Community** - *Lead* - [![Twitter Follow](https://img.shields.io/twitter/follow/opengento.svg?style=social)](https://twitter.com/opengento)
- **Thomas Klein** - *Maintainer* - [![GitHub followers](https://img.shields.io/github/followers/thomas-kl1.svg?style=social)](https://github.com/thomas-kl1)
- **Contributors** - *Contributor* - [![GitHub contributors](https://img.shields.io/github/contributors/opengento/magento2-country-store.svg?style=flat-square)](https://github.com/opengento/magento2-country-store/graphs/contributors)

## License

This project is licensed under the MIT License - see the [LICENSE](./LICENSE) details.

***That's all folks!***
