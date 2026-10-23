<p><img src="view/adminhtml/web/images/brand/wheelie.svg" alt="" width="64" height="64"></p>

# bigcartenergy/module-core

A free Magento 2 module by Big Cart Energy.

`BigCartEnergy_Core` is the small shared module that every free Big Cart Energy module for Magento Open Source builds on. It does nothing on its own. Install it because another Big Cart Energy module requires it; it is not useful alone.

## What it owns

- The "Big Cart Energy" item in the admin menu. Each Big Cart Energy module hangs its own entries under it, so the brand appears once no matter how many of our modules you install.
- The "Big Cart Energy" tab in Stores > Configuration, with an "About Big Cart Energy" section that lists the installed Big Cart Energy modules with their versions and links to the website, the free tools and the contact page.
- The brand assets (the Wheelie mark) and the shared admin styles the other modules use for their small brand placements.
- The ACL root `BigCartEnergy_Core::bce`, under which the other modules' permissions sit in User Roles.

Nothing in this module runs on the storefront, touches checkout, adds cron jobs, calls home or sends data anywhere. It has no settings.

## Requirements

Composer accepts Magento Open Source 2.3.0 to 2.4.9 on PHP 7.1 to 8.5 (PHP 7.1 syntax throughout; 2.0 to 2.2 are not supported). The lines tested so far are listed below; any other line is untested, so test on staging first.

| Magento Open Source | PHP | Result |
| --- | --- | --- |
| 2.4.9 | 8.3 | Pass: the same checks |
| 2.4.8 | 8.3 | Pass: menu item, configuration tab and About section, brand assets and styles, with Catalog Completeness installed and after it was uninstalled |
| 2.4.7 | 8.2 | Pass: the same checks |
| 2.3.7-p4 | 7.4 | Pass: the same checks |

## Install

```
composer require bigcartenergy/module-core
bin/magento module:enable BigCartEnergy_Core
bin/magento setup:upgrade
bin/magento setup:static-content:deploy  # production mode only
bin/magento cache:flush
```

## Remove

```
bin/magento module:disable BigCartEnergy_Core
composer remove bigcartenergy/module-core
bin/magento setup:upgrade
```

Disable or remove the Big Cart Energy modules that depend on it first; composer refuses to remove it while one of them requires it.

## Installation and configuration service

Big Cart Energy installs, configures and tunes its free modules for merchants who would rather not do it themselves. Details at https://bigcartenergy.com/contact.

## Licence

Open Software License 3.0 (OSL-3.0), the same licence as Magento Open Source. See [LICENSE.txt](https://github.com/bigcartenergy/module-core/blob/main/LICENSE.txt).

Repository: https://github.com/bigcartenergy/module-core · Issues: https://github.com/bigcartenergy/module-core/issues

Use at your own risk. Test on staging before production.

Copyright (c) Big Cart Energy. https://bigcartenergy.com
