================================================

This is a Payment Module for Magento Community Edition, that gives you the ability to process payments through E-ComProcessing's Payment Gateway - Genesis.

Requirements
------------

* Magento Community Edition* > 1.7
* [GenesisPHP v1.4](https://github.com/GenesisGateway/genesis_php) - (Integrated in Module)
* PCI-certified server in order to use ```E-ComProcessing Direct```

*Note: this module has been tested only with Magento __Community Edition__, it may not work
as intended with Magento __Enterprise Edition__

Install GenesisGateway Library
------------

You should follow these steps to install the __GenesisGateway__ Library if you wish to use more than one Payment Solution, which use GenesisGateway


* Install __Composer__ - [Composer Download Instructions](https://getcomposer.org/doc/00-intro.md)

* Install __GenesisGateway__

    ```sh
    $ composer require GenesisGateway/genesis_php:1.4@stable
    ```

* Remove __GenesisGateway__

    ```sh
    $ composer remove GenesisGateway/genesis_php
    ```

If you have the correct version of __GenesisGateway__ Library installed on your __Magento CE__, the Payment Module will not use the 
integrated Library in the Module, but it will connect to the __GenesisGateway__ installed by __Composer__.

GenesisPHP Requirements
------------

* PHP version 5.3.2 or newer
* PHP Extensions:
    * [BCMath](https://php.net/bcmath)
    * [CURL](https://php.net/curl) (required, only if you use the curl network interface)
    * [Filter](https://php.net/filter)
    * [Hash](https://php.net/hash)
    * [XMLReader](https://php.net/xmlreader)
    * [XMLWriter](https://php.net/xmlwriter)

Installation (via Modman)
------------

* Install [ModMan]
* Navigate to the root of your Magento installation
* run ```modman init```
* and clone this repo ```modman clone https://github.com/E-ComProcessing/magento-ce-ecp-plugin```
* Login inside the Admin Panel and go to ```System``` -> ```Configuration``` -> ```Payment Methods```
* Check ```Enable```, set the correct credentials, select your prefered payment method and click ```Save config```

Installation (manual)
------------
To avoid such errors in the future when installing/upgrading extensions I recommend the following steps in our

1) Turn off Magento’s Compilation Mode
2) Turn on Magento’s Cache
3) Install the Extension
4) Refresh Magento’s Cache
5) Log Out of the Backend and Log Back In
6) Rerun Magento’s Compilation Process
7) Genesis client for Magento CE

* Copy the files to the root folder of your Magento installation
* Login inside the Admin Panel and go to ```System``` -> ```Configuration``` -> ```Payment Methods```
* If one of the Payment Methods ```E-ComProcessing Direct``` or ```E-ComProcessing Checkout``` is not yet available, 
  go to  ```System``` -> ```Cache Management``` and clear Magento Cache by clicking on ```Flush Magento Cache```
* Check ```Enable```, set the correct credentials, select your prefered payment method and click ```Save config```

You're now ready to process payments through our gateway.

[ModMan]: https://github.com/colinmollenhour/modman
