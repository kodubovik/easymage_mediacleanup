# Magento Media Cleanup Module
Magento doesn't remove product images automatically. So in time your product image directory may grow up incredibly and become a mess. This module allows you to match images in your media directory with images linked to products in your database and remove unused ones. You can run cleanup either manually or via cron.

# Compatibility
* Magento CE 1.8-1.9

# Installation
* Disable compilation
* Merge app/ dir of the module package with app/ dir of your magento installation
* Refresh magento cache, relogin to admin panel

# Usage
Go to **System > Configuration > EasyMage > Media Cleanup**
![screenshot_1](https://cloud.githubusercontent.com/assets/12259690/13453949/a510ea5e-e05c-11e5-8c1f-e67cf00778a4.png)<br/>
* List image extensions you want to match
* Set path to your image directory. It have to be relative to you magento root path
* If you set 'skip cache' to 'no' all cached images will be removed. Otherwise cache directory won't be affected
* You're able to cleanup images via cron and set cron expression
* You can enable email notification. When enabled you will get success or failure email on every cleanup run
* Before running cleanup it's wise to try dry run to see what result you'll get
