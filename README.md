oxid-plugin
===============

OXID eSales module for integration with [Tiramizoo API](http://dev.tiramizoo.com/).
Module works with following OXID eSales versions: 4.3.2+, versions 4.4.x, 4.5.x will be available soon

# Checkout flow #

*   User adds items to basket (All items added by user have to have tiramizoo service enabled, directly or by category)

*   User go to checkout (Step 1)

*   User specify delivery address (Step 2)

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

*	Switch to 4.3.2 branch, download code

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

        Tiramizoo URL - Production version [https://api.tiramizoo.com/v1](https://api.tiramizoo.com/v1), testing version [https://sandbox.tiramizoo.com/api/v1](https://sandbox.tiramizoo.com/api/v1)

        Tiramizoo API token - Can be obtained via your user profile, Production version [https://www.tiramizoo.com/](https://www.tiramizoo.com/), testing version [https://sandbox.tiramizoo.com/](https://sandbox.tiramizoo.com/)

        Shop informations

        Time window delivery

    -   At the **Administer Products -> Categories -> Category selection -> Tiramizoo tab**

        Category and all parent categories need to have Tiramizoo service enabled to let containing products to be delivered by Tiramizoo service. Also dimensions form enables to specify default product dimentions which will apply to all products which have the category selected as a main category and will not have dimentions specified explicitly.

        products ready for delivery with tiramizoo should be available in stock, should have dimensions and weight specified and dimensions and weight can not cross specific level.

        Validation of products will take place when plugin asks tiramizoo server for quotes (POST request to /quotes between step 2 to 3 in checkout process). That means, even if flag "available for tiramizoo" is set, tiramizoo service could not appear as one of delivery option in 3th step of checkout process because of validation

    -   At the **Administer Products -> Products -> Article selection -> Tiramizoo tab**

        You can enable or disable Tiramizoo delivery for selected product

# Checking the Tiramizoo delivery status #

Go to *Order tab* to check the current delivery status