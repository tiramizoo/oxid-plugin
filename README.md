tiramizoo oxid plugin documentation
===============

OXID eSales module for integration with [Tiramizoo API](http://dev.tiramizoo.com/).
Module works with following OXID eSales versions: 4.7.x/5.0.x. The 4.3.2 version is deprecated.




## Customer checkout flow

Checkout has 5 steps. Step 1 - Cart, Step 2 - Address, Step 3 - Pay, Step 4 - Order, Step 5 - Ready  

All items added by user have to have tiramizoo service enabled, directly or by parent category

*   User adds items to basket. 

*   User goes to checkout (Step 1)

*   User specifies delivery address (Step 2)

*   User selects tiramizoo as a delivery option (Step 3)

*   User selects delivery time window - when he wants courier to deliver (Step 3)

*   User selects payment method (Step 3)

*   User verifies and confirms (or corrects if needed) order by going to step 5

*   Between Step 4 and Step 5 plugin sends request to tiramizoo to create order

*   User sees thank you page and receives email with tracking url




## Demo ##

*   Demo shop is available here: [http://staging.oxid-demo.tiramizoo.com](http://staging.oxid-demo.tiramizoo.com)

*   Admin panel access: [http://staging.oxid-demo.tiramizoo.com/admin](http://staging.oxid-demo.tiramizoo.com/admin)

*   Admin username: oxid-demo@tiramizoo.com password: secret

*   Test client username: test@tiramizoo.com password: secret

*   Demo shop settings and data are reverted to default every 4h




## Installation ##

*   Switch to [master](https://github.com/tiramizoo/oxid-plugin/tree/master) branch and download code

*   Copy all files from [copy_this](https://github.com/tiramizoo/oxid-plugin/tree/master/copy_this) folder to OXID eSales installation path. This step does not overwrite any files.

*   If template does not support blocks, please compare [changed files](https://github.com/tiramizoo/oxid-plugin/tree/master/copy_this/modules/oxtiramizoo/views/blocks) according to [metadata file](https://github.com/tiramizoo/oxid-plugin/blob/master/copy_this/modules/oxtiramizoo/metadata.php#L102)

*   Go to **eShop Admin -> Extensions -> Modules**, select the **"OXID Tiramizoo.com"** extension and press the "Activate" button in the "Overview" tab.

*   After these steps, the "Tirazmizoo" menu item appears in the eShop Admin navigation   




## Configure the module ##


*  At the **Tiramizoo -> Settings**

    To finalize configuration form has to be filled in with proper data.
    * **Tiramizoo URL:** The URL of the tiramizoo service endpoint (production version [https://api.tiramizoo.com/v1](https://api.tiramizoo.com/v1), testing version [https://sandbox.tiramizoo.com/api/v1](https://sandbox.tiramizoo.com/api/v1))

    * **Shop URL:** URL used to build the webhook url for order creation. The tiramizoo service will send a request to the url each time an order status change occurs.

    * **Packing strategy:**
    The shop admin is able to specify what packages are used by the shop. He can do that in 3 ways:

        * **All products have individual dimensions**: That means each product will be packed separately (number of products equals number of packages).

        * **Specific dimensions of packages**: That means that the admin is able to specify all package sizes they use in shop to pack products. Then the  packing algorithm starts to pack all products in as smallest number of packages as possible and assumes the resulting package size as the package to deliver. Go to tiramizoo configuration to specify these dimensions ([Production version](https://www.tiramizoo.com/dashboard/package_presets), [testing version](https://sandbox.tiramizoo.com/dashboard/package_presets)).

        * **All products should fit to one package**: That means every product will go to one package (no matter how many products customer bought there will be only 1 package for courier)


    * **Default dimensions and weight:**
    Tiramizoo delivery works only if every article in basket has specified dimensions and weight. The module allows to specify global dimensions and weight. These values are applied for each product without specified dimensions and weight.

    * **Tiramizoo payment methods assigned** 
    Payment methods which are available for tiramizoo service. *Warning: Cash on delivery is currently not supported by Tiramizoo.

    * **Enable only articles with stock:**
    Admin is able to turn on/off stock monitoring. That means if any of products in customer's basket is not available in the shop's stock, the tiramizoo delivery option will not show up.

    * **Retail Locations**
    The user api tokens available in the user dashboard after registration on the tiramizoo.com site. The API token is required to authenticate api requests.
    Plugin offers to connect for more than one tiramizoo account. Shop can deliver orders from more than one retail location. The decision which api token is going to be used is made by postal codes comparisons. 

    * **Synchronize all configuration:**
    Configuration options from all retail locations (tiramizoo accounts) are synchronized daily. This process is performed in the first http request to the shop after midnight. It is possible to synchronize configuration directly by pressing the "Synchronize" button


*  At the **Administer Products -> Categories -> Category selection -> Tiramizoo tab**

    Product's main category and all its parents categories need to have Tiramizoo service enabled to let containing products to be delivered by Tiramizoo service. Also dimensions form enables to specify default product dimensions which will apply to all products which have the category selected as a main category and will not have dimensions specified explicitly.

    Please consider, that inheritance of category properties that are not assigned as Main category, wouldn't be apply to products they have.

    Products ready for delivery with tiramizoo should be available in stock (if "Enable only articles in stock" is selected), should have dimensions and weight specified and dimensions and weight can not cross specific level.

    Validation of products will take place when delivery and payment methods appear. That means, even if flag "available for tiramizoo" is set, tiramizoo service could not appear as one of delivery option in 3th step of checkout process because of validation

*  At the **Administer Products -> Products -> Article selection -> Tiramizoo tab**

    You can enable or disable Tiramizoo delivery for selected product. This page contains preview of the effective dimensions


## Configure the account ##

The main configuration of tiramizoo delivery is available on tiramizoo page.

-   **Packages presets:** (Profile -> Default packages sizes)
    Retail location admin is able to specify package sizes that they use to pack products. 

-   **Pickup contact:** 

    Go to **Profile -> Account** and click on "Add Contact" link

    ![Add pickup contact step 1](readme/images/tiramizoo_add_new_contact_step1.png)

    Fill the required form fields and press the "Create contact" button

    ![Add pickup contact step 2](readme/images/tiramizoo_add_new_contact_step2.png)

    Click on radio button to set selected contact as "Pickup contact"

    ![Add pickup contact step 3](readme/images/tiramizoo_add_new_contact_step3.png)

-   **Enable Immediate delivery type:** (Profile -> Account -> Immediate time window enabled)
    Select Yes to enable Immediate delivery type or no to disable.

-   **Enable Evening delivery type:** (Profile -> Account -> Time window preset)
    Select time window to enable Evening delivery type or left blank to disable.

-   **Retail location name:** (Profile -> Account -> Name)
    Name of retail location presented on Oxid tiramizoo settings.

-   **Business hours:** (Profile -> Account -> Business hours)
    Specify when courier can pick up delivery. Selected days and hours could be extended by special days *e.g. 24/12/2013 8:00-14:00* (in this example business hours are reduced).



## Minimal configurations ##
The tiramizoo delivery option is only available if the following rules are met:

*   **Tiramizoo account configuration:**
    
    * Pickup contact is selected and has valid postal code
    * Immediate delivery type or Evening delivery type is enabled
    * Package presets are specified if shop use "Specific dimensions of packages" as packing strategy

*   **Oxid plugin configuration:**

    * Tiramizoo API url is filled correctly with *sandbox* or *production* API url
    * Shop url is not empty
    * One or more available payment method are checked
    * The API Token is added

*   **Article in basket configuration**
    
    * Article has specified dimensions and weight directly or inherited by global / category settings.
    * Article is enabled with Tiramizoo Delivery
    * Article's main category and its parent categories are enabled with Tiramizoo Delivery
    * Article stock quantity is greater than 0 if "Enable only articles with stock: is selected

*   **Oxid eShop configuration**

    * **Tiramizoo Delivery Set** is active
    * **Tiramizoo Cost rule** is active and assigned to **Tiramizoo Delivery Set**
    * Warning: We highly recommend to set customer's phone number as a required field. It enables courier to contact customer when necessary*


## Additional features / Tips ##

-   **WebHook notifications**
    
    plugin is able to receive push notification sent by tiramizoo service each time the order status has changed. The order state is then changed on shop level as well. Go to Order tab to check the current order status. Webhook url needs to be accessible for POST request.

-   **Disable tiramizoo service for individual product**

    Available on product page

-   **Mark individual product to not be packed with others - standalone**

    Available on product page

-   **Disable tiramizoo service for individual category**

    Available on category page

-   **Mark each product within category as individual product to not be packed with others - standalone**
    
    Available on category page

-   **Change cost calculation method of delivery**

    Default price paid by customer for every delivery is set as **Tiramizoo Standard Cost rule** and it could be changed. If needed that price is depended on conditions like (article dimensions, basket total price, article amount in basket) it could be solved by adding new cost rules. But if needed more complexity cost calculation depends on other factors (from tiramizoo configuration and time windows delivery type) it should be done by changing the method `calculateDeliveryPrice` in class [oxTiramizoo_DeliveryPrice](https://github.com/tiramizoo/oxid-plugin/blob/master/copy_this/modules/oxtiramizoo/core/oxtiramizoo_deliveryprice.php)



## Troubleshooting and bugs ##

* Check "Minimal configurations" section of this document
* Use the newest version of plugin from [master branch](https://github.com/tiramizoo/oxid-plugin/tree/master) and Your eShop version is 4.7.x/5.0.x.
* In case of any issues or suggestions please let us know through [github issues](https://github.com/tiramizoo/oxid-plugin/issues)

