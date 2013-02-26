tiramizoo oxid plugin documentation
===============

OXID eSales module for integration with [Tiramizoo API](http://dev.tiramizoo.com/).
Module works with following OXID eSales versions: 4.3.2+. Versions: 4.4.x, 4.5.x will be available soon.

### Tiramizoo delivery option availability:
The tiramizoo delivery option is only available if the following rules are met:

1. The delivery address city specified by the consumer is the same as the shop location.
2. The delivery time windows are specified to days when the shop is open
3. The delivery time windows are specified to days when the tiramizoo service operates.

# The plugin allows to specify:

### 1. Shop data

* **Tiramizoo URL**: The URL of the tiramizoo service endpoint (production version [https://api.tiramizoo.com/v1](https://api.tiramizoo.com/v1), testing version [https://sandbox.tiramizoo.com/api/v1](https://sandbox.tiramizoo.com/api/v1))

* **Tiramizoo API token**: The user api token available in the user dashboard after registration on the tiramizoo.com site. The API token is required to authenticate api requests.

* **Shop URL**: URL used to build the webhook url for order creation. The tiramizoo service will send a request to the url each time an order status change occurs.

* **Pickup street address**: required address of the pickup location

* **Pickup city**: required city of the pickup location. It is also used to verify if the tiramizoo service is available for an order (The city in consumers delivery address needs to be the same as this one).

* **Pickup Postal Code**: required

* **Pickup Country Code**: required

* **Pickup Location Name**: required, example: “Herr Gregor Melhorn, Tiramizoo GmbH”

* **Pickup Phone Number**: required

* **Pickup email**: An email used to notify when tiramizoo service responds with 500 while making a request to orders API (while creating order)

### 2. General / Category / Product dimensions
Available on tiramizoo settings page, category page and product page. The shop admin is able to specify 3 dimensions and the weight of product. That feature allows the admin to not specify the dimension for every product, but use category or general dimensions fallback. The product page (tiramizoo tab) offers a preview of the effective dimensions.

### 3. Time windows (Defining pickup and delivery times)
The admin is able to specify pickup and delivery times (which customer selects on checkout process), as well as order to pickup offset.

* **Order to pickup offset** - The time the shop needs to prepare the package after the customer placed an order and before the courier can show up.

* **Delivery time window length** - how much time the courier has to deliver a package, calculated from pickup start hour. (example: pickup time set to 10:00, delivery time window length set to 90 min, means delivery time is from 10:00 to 11:30, and in this time range courier needs to deliver the package)

* **Pickup time window length** - how much time the courier has to pickup package, calculated from pickup start hour. (example: pickup time set to 10:00, pickup time window length set to 30 min, means pickup time is from 10:00 to 10:30, and in this time range courier needs to pickup the package)

* **1 - 5th pickup hour** - an hour when pickup starts

**Time windows example**:

* order to pickup offset: 30min
* delivery time window length: 180min
* pickup time window length: 5min
* 1st pickup time: 10:00
* 2nt pickup time: 15:00

*That will produce rules*:

* 1st time window: 10:00 - 10:05 to 10:00 - 13:00
* 2nt time window: 15:00 - 15:05 to 15:00 - 18:00
* A customer who orders before 09:30 (10:00 - 30min pickup offset) and after 14:30 (15:00 - 30min) has 1st time window as immediate delivery
* A customer who orders before 14:30 and after 09:30 has 2nd time window as immediate delivery

### 4. Package sizes
The shop admin is able to specify what packages are used by the shop. He can do that in 3 ways:

* **All products have individual dimensions**: That means each product will be packaged seperately (number of products equals number of packages).

* **All products should fit to one package**: That means every product will go to one package (no matter how many products customer bought there will be only 1 package for courier)

* **Specific dimensions of packages**: That means that the admin is able to specify all package sizes they use in shop to pack products. Then the  packing algorithm starts to pack all products in as smallest number of packages as possible and assumes the resulting package size as the package to deliver.

### 5. Available payment methods
The shop admin is able to specify with what payment methods tiramizoo delivery service works.

### 6. Evening delivery
The admin is able to turn on/off evening delivery. One of specified time windows needs to be marked as evening delivery to use that feature. When customer selects that on checkout process the evening time window is used.

### 7. Immediate delivery
The admin is able to turn on/off immediate delivery. The customer selects immediate delivery on the checkout process and the first available time window gets selected. Immediate delivery takes the pickup offset into account, so if time now is 09:31 and the first time window is 10:00 - 10:30, but offset is 30min, then the time window is not available as immediate delivery (second time window is chosen if specified, or first time window of next available day)

### 8. Fixed time windows
The admin is able to turn on/off fixed time windows. Fixed time windows means that the customer is able to select a time window in the future other than immediate and evening delivery. Up to 7 days in the future, excluding closed days

### 9. Enable only products in stock
admin is able to turn on/off stock monitoring. That means if any of products in customer’s basket is not available in the shop’s stock, the tiramizoo delivery option will not show up.

### 10. Shop availability (Opening times)
The admin is able to specify which day is a working day for shop. Additionally he can specify exclude / include dates.

* **Include date** - date when the shop is open even though week day is not specified as working day (example: shop usually works from Monday to Friday, but admin specifies that at Saturday 02.02.2013 the shop is open).

* **Exclude date** - opposite to include date, date when the shop is closed even though week day is specified as working day (example: shop usually works from Monday to Friday, but admin specifies that at Wednesday 27.02.2013 the shop is closed)

#Additional features:

### 1. Exception email notification
admin is able to specify **Pickup email** which plugin uses when sending exception notification. Exception notification will be sent only if tiramizoo service responds with 5XX status code while creating an order.

### 2. WebHook notifications
plugin is able to receive push notification sent by tiramizoo service each time the order status has changed. The order state is then changed on shop level as well. Go to *Order tab* to check the current order status. Webhook url needs to be accessible for POST request.

### 3. Disable tiramizoo service for individual product
Available on product page

### 4. Mark individual product to not be packed with others - standalone
Available on product page

### 5. Disable tiramizoo service for individual category
Available on category page

### 6. Mark each product within category as individual product to not be packed with others - standalone
Available on category page


# Customer checkout flow

*   User adds items to basket (All items added by user have to have tiramizoo service enabled, directly or by parent category)

*   User goes to checkout (Step 1)

*   User specifies delivery address (Step 2) *Warning: We highly recommend to set customer's phone number as a required field. It enables courier to contact customer when necessary

*   Between Step 2 and Step 3 plugin sends request to tiramizoo with items dimentions and address data to verify if package can be delivered with Tiramizoo. In case package is too big, or delivery address is outside service area tiramizoo will not appear as delivery option

*   User selects tiramizoo as a delivery option (Step 3)

*   User selects delivery time window - when he wants courier to deliver (Step 3)

*   User selects payment method (Step 3) (please remember that pay on delivery should not be available for tiramizoo service)

*   User selects payment option and go to step 4 (Step 3)

*   User verifies and confirms (corrects if needed) order by going to step 5

*   Between Step 4 and Step 5 plugin sends request to tiramizoo to create order

*   User sees thank you page and receives email with tracking url


# Demo #

*   Demo shop is available here: http://oxid-demo.tiramizoo.com

*   Admin panel access: http://oxid-demo.tiramizoo.com/admin

*   Admin username: oxid-demo@tiramizoo.com password: secret

*   Test client username: test@tiramizoo.com password: secret

*   Demo shop settings and data are reverted to default every 4h


# Installation #

*   Switch to 4.3.2 branch, download code

*   Copy all files from *copy_this* folder to OXID eSales installation path. This step does not overwrite any files.

*   Add these 2 lines to Textarea Shop Modules in **Master Settings -> Core Settings -> System Tab -> Modules**

    ```
    oxorder => oxtiramizoo_oxorder
    payment => oxtiramizoo_payment
    order => oxtiramizoo_order
    ```

*   Add following code to template files: (**Tip: if you use "basic" templates, and you did not change them, override them with files located in *changed_full* folder)

    **file: out/basic/tpl/email_order_cust_html.tpl**

    Add Tiramizoo tracking url after showing selected delivery method [see in the basic template](https://github.com/tiramizoo/oxid-plugin/blob/4.3.2/changed_full/out/basic/tpl/email_order_cust_html.tpl#L360)

    ```
    [{* oxtiramizoo BEGIN *}]
    [{if $order->oxorder__tiramizoo_tracking_url->value }]
        Tracking URL: [{$order->oxorder__tiramizoo_tracking_url->value}]<br>
    [{/if}]
    [{* oxtiramizoo END *}]
    ```

    **file: out/basic/tpl/email_order_cust_plain.tpl**

    Add Tiramizoo tracking url after showing selected delivery method [see in the basic template](https://github.com/tiramizoo/oxid-plugin/blob/4.3.2/changed_full/out/basic/tpl/email_order_cust_plain.tpl#L123)

    ```
    [{* oxtiramizoo BEGIN *}]
    [{if $order->oxorder__tiramizoo_tracking_url->value }]
      Tracking URL: [{$order->oxorder__tiramizoo_tracking_url->value}]
    [{/if}]
    [{* oxtiramizoo END *}]
    ```

    **file: out/basic/tpl/payment.tpl**

    Put thes lines after slect box contains abailable delivery sets [see in the basic template](https://github.com/tiramizoo/oxid-plugin/blob/4.3.2/changed_full/out/basic/tpl/payment.tpl#L38)

    ```
    [{* oxtiramizoo BEGIN *}]
    [{if $isTiramizooCurrentShippingMethod }]
        <div style="padding-top:20px; clear:both;">
        <h3>[{ oxmultilang ident="oxTiramizoo_selectTimeWindowTitle" }]</h3>

            <dl style="margin-top:16px;">
            [{foreach key=sDeliveryTime from=$aTiramizooAvailableDeliveryHours item=sDeliveryWindow}]
                <dt>
                    <input class="selectTiramizooTimeWindow" type="radio" name="sTiramizooTimeWindow" value="[{$sDeliveryTime}]" [{if $sTiramizooSelectedDeliveryTime == $sDeliveryTime}]checked="checked"[{/if}] onchange="JavaScript:document.forms.shipping.submit();" />
                    <label for="sTiramizooTimeWindow"><b>[{$sDeliveryWindow}]</b></label>
                </dt>
            [{/foreach}]
            </dl>
        </div>
    [{/if}]
    [{* oxtiramizoo END *}]

    ```

    **file: out/basic/tpl/order.tpl**

    Put these lines after showing selected delivery method
    [see in the basic template](https://github.com/tiramizoo/oxid-plugin/blob/4.3.2/changed_full/out/basic/tpl/order.tpl#L480)

    ```
    [{* oxtiramizoo BEGIN *}]
        [{ $sTiramizooTimeWindow }]
    [{* oxtiramizoo END *}]
    ```



*   Configure the module
    -   At the **Tiramizoo -> Settings**

        To finalize configuration form has to be filled in with proper data and tiramizoo service needs to be enabled. At least one pickup time and one payment method has to be selected. **Warning tiramizoo service will not work with "Pay on delivery" payment option. Such delivery will not be processed.

        * **Tiramizoo URL**: The URL of the tiramizoo service endpoint (production version [https://api.tiramizoo.com/v1](https://api.tiramizoo.com/v1), testing version [https://sandbox.tiramizoo.com/api/v1](https://sandbox.tiramizoo.com/api/v1))

        * **Tiramizoo API token**: The user api token available in the user dashboard after registration on the tiramizoo.com site. The API token is required to authenticate api requests.

        * **Shop URL**: URL used to build the webhook url for order creation. The tiramizoo service will send a request to the url each time an order status change occurs.

        * **Pickup street address**: required address of the pickup location

        * **Pickup city**: required city of the pickup location. It is also used to verify if the tiramizoo service is available for an order (The city in consumers delivery address needs to be the same as this one).

        * **Pickup Postal Code**: required

        * **Pickup Country Code**: required

        * **Pickup Location Name**: required, example: “Herr Gregor Melhorn, Tiramizoo GmbH”

        * **Pickup Phone Number**: required

        * **Pickup email**: An email used to notify when tiramizoo service responds with 500 while making a request to orders API (while creating order)

        * **Order To Pickup Time offset** - Time required by Shop to prepare package

        * **Delivery time window length** - Number of minutes client is available to receive a package at delivery address.

        * **Pickup time window length** - Number of minutes Shop is available for courier to pickup a package at pickup address

        * **1st - 5th pick up hour** - Hour when pickup starts, Shop can define up to 6 pickup hours. Courier is obligated to pickup package between pickup hour and pickup hour + pickup time window lenght (example: pickup hour: 10:00, pickup time window lenght: 30min, effective pickup window is: 10:00 - 10:30)

        * **Tiramizoo payment methods assigned** - Payment methods which are available for tiramizoo service. *Warning: Payment on delivery is currently not supported by Tiramizoo.

    -   At the **Administer Products -> Categories -> Category selection -> Tiramizoo tab**

        Product's main category and all its parents categories need to have Tiramizoo service enabled to let containing products to be delivered by Tiramizoo service. Also dimensions form enables to specify default product dimentions which will apply to all products which have the category selected as a main category and will not have dimentions specified explicitly.

        Please consider that inheritance of category properties that are not assigned as Main category wouldn't be apply to products they have.

        Products ready for delivery with tiramizoo should be available in stock, should have dimensions and weight specified and dimensions and weight can not cross specific level.

        Validation of products will take place when plugin asks tiramizoo server for quotes (POST request to /quotes between step 2 to 3 in checkout process). That means, even if flag "available for tiramizoo" is set, tiramizoo service could not appear as one of delivery option in 3th step of checkout process because of validation

    -   At the **Administer Products -> Products -> Article selection -> Tiramizoo tab**

        You can enable or disable Tiramizoo delivery for selected product

    **NOTE! After first installation and every update You have to go to Tiramizoo settings page and then clear tmp folder.**