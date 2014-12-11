Genesis client for Magento CE
=============================

This is a Payment Module for E-ComProcessing that gives you the ability to process payments through E-ComProcessing's Payment Gateway - Genesis from within your Magento-based Store.

Requirements
------------

* Magento CE (Community Edition) >= 1.7
* GenesisPHP 1.0.1

Note: this module has been tested only with Magento Community Edition, it may not work
as intended with Magento Enterprise Edition

GenesisPHP Requirements
------------

* PHP >= 5.3 (built w/ libxml)
* PHP Extensions: cURL (optionally you can use Streams, but its not recommended on PHP < 5.6)
* Composer


Installation (auto)
---------------------

* Install [ModMan]
* Navigate to the root of your Magento installation
* run `modman init`
* and clone this repo `modman clone https://github.com/E-ComProcessing/magento-ce-ecp-plugin`
* Flush all Caches (from System -> Cache Management)
* Login inside the Admin Panel and go to System -> Configuration -> Payment Methods
* Check "Enable" and set the correct credentials, select your preferred payment method and click "Save config"

You're now ready to process payments through our gateway.


Installation (manual)
---------------------

* Copy the files to the root folder of your Magento installation
* Flush all Caches (from System -> Cache Management)
* Login inside the Admin Panel and go to System -> Configuration -> Payment Methods
* Check "Enable" and set the correct credentials, select your prefered payment method and click "Save config"

You're now ready to process payments through our gateway.

Supported Transaction Types
---------------------------

Currently, we support the following transaction types (for more in-depth information about these transaction types, you can reference the Genesis API Documentation):

* Authorize
* Authorize with 3D-Secure
* Capture
* Sale (Auth&Capture)
* Sale (Auth&Capture) with 3D-Secure
* Refund
* Void

[ModMan]: https://github.com/colinmollenhour/modman