oxid-plugin-dev
===============

This folders contains OXID eSales module for integration with [Tiramizoo API](http://dev.tiramizoo.com/).
Module working with foloowingOXID eSales versions: 4.3.2+, 4.4.x, 4.5.x 

# Installation #

*   Copy all files from *copy_this* folder to your OXID eSales installation path. This step not overwrite any files.

*   Add these 2 lines to Textarea Shop Modules in **Master Settings -> Core Settings -> System Tab -> Modules**

    ```
    payment => oxtiramizoo_payment
    order => oxtiramizoo_order
    ```

*   Change the templates in *changed_full* folder. If You use "basic" template and these files had never been changed, You can overwrite them.

    ### file: out/basic/tpl/email_order_cust_html.tpl ###

    ```
    @@ -354,7 +354,14 @@
         [{ $order->oxorder__oxdelcountry->value }]<br>
       [{/if}]
     
    -  [{if $payment->oxuserpayments__oxpaymentsid->value != "oxempty"}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGCARRIER" }] <strong>[{ $order->oDelSet->oxdeliveryset__oxtitle->value }]</strong><br>[{/if}]
    +  [{if $payment->oxuserpayments__oxpaymentsid->value != "oxempty"}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGCARRIER" }] <strong>[{ $order->oDelSet->oxdeliveryset__oxtitle->value }]</strong>
    +  <br>
    +
    +  [{if $order->oxorder__tiramizoo_tracking_url->value }]
    +    Tracking URL: [{$order->oxorder__tiramizoo_tracking_url->value}]<br>
    +  [{/if}]
    +
    +  [{/if}]
     
       [{if $payment->oxuserpayments__oxpaymentsid->value == "oxidpayadvance"}]
         [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BANK" }] [{$shop->oxshops__oxbankname->value}]<br>

    ```

    ### file: out/basic/tpl/email_order_cust_plain.tpl ###

    ```
    @@ -119,6 +119,9 @@
     
     [{if $payment->oxuserpayments__oxpaymentsid->value != "oxempty"}][{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_SHIPPINGCARRIER" }] [{ $order->oDelSet->oxdeliveryset__oxtitle->getRawValue() }]
     [{/if}]
    +[{if $order->oxorder__tiramizoo_tracking_url->value }]
    +  Tracking URL: [{$order->oxorder__tiramizoo_tracking_url->value}]
    +[{/if}]
     
     [{if $payment->oxuserpayments__oxpaymentsid->value == "oxidpayadvance"}]
     [{ oxmultilang ident="EMAIL_ORDER_CUST_HTML_BANK" }] [{$shop->oxshops__oxbankname->getRawValue()}]<br>

    ```

    ### file: out/basic/tpl/payment.tpl ###

    ```
    @@ -12,6 +12,12 @@
         <div class="errorbox">[{ oxmultilang ident="ORDER_READANDCONFIRMTERMS" }]</div>
     [{/if}]
     
    +
    +[{ if $oView->isTiramizooError() }]
    +    <div class="errorbox">[{$oView->getTiramizooError()}]</div>
    +[{/if}]
    +
    +
     [{ if !$oxcmp_basket->getProductsCount()  }]
       <div class="msg">[{ oxmultilang ident="ORDER_BASKETEMPTY" }]</div>
     [{else}]
    @@ -475,7 +481,10 @@
                       <input type="hidden" name="cl" value="payment">
                       <input type="hidden" name="fnc" value="">
                       [{assign var="oShipSet" value=$oView->getShipSet() }]
    -                  [{ $oShipSet->oxdeliveryset__oxtitle->value }]&nbsp;<span class="btn"><input id="test_orderChangeShipping" type="submit" value="[{ oxmultilang ident="ORDER_MODIFY3" }]" class="btn"></span>
    +                  [{ $oShipSet->oxdeliveryset__oxtitle->value }]&nbsp;
    +                  [{ $oView->getTiramizooTimeWindow()}]
    +
    +                  <span class="btn"><input id="test_orderChangeShipping" type="submit" value="[{ oxmultilang ident="ORDER_MODIFY3" }]" class="btn"></span>
                   </div>
                 </form>
             </dd>

    ```

    ### file: out/basic/tpl/order.tpl ###


    ```
    @@ -34,9 +34,30 @@
                     [{ /if}]
                   </div>
               </div>
    +          [{if $oView->isTiramizooCurrentShiippingMethod()}]
    +          <br />
    +          <br />
    +          <h3>[{ oxmultilang ident="oxTiramizoo_selectTimeWindowTitle" }]</h3>
    +
    +              <dl style="margin-top:16px;">
    +              [{foreach key=sDeliveryTime from=$oView->getAvailableDeliveryHours() item=sDeliveryWindow}]
    +                  <dt>
    +                      <input class="selectTiramizooTimeWindow" type="radio" name="sTiramizooTimeWindow" value="[{$sDeliveryTime}]" [{if $oView->getTiramizooTimeWindow() == $sDeliveryTime}]checked="checked"[{/if}] onchange="JavaScript:document.forms.shipping.submit();" />
    +                      <label for="sTiramizooTimeWindow"><b>[{$sDeliveryWindow}]</b></label>
    +                  </dt>
    +              [{/foreach}]
    +              </dl>
    +          [{/if}]
             </form>
         </div>
     
    +
    +
    +
    +
    +
    +
    +
       [{/if}]
     
       [{assign var="iPayError" value=$oView->getPaymentError() }]

    ```



*   Configure the module 
    -   At the **Tiramizoo -> Settings**

        Api Key

        Shop informations

        Time window delivery

        Time from order to packaging

    -   At the **Administer Products -> Categories -> Category selection -> Tiramizoo tab**

        You can dynamically assign stanard dimensions for all products inside specified category

    -   At the **Administer Products -> Products -> Article selection -> Tiramizoo tab**

        You can enable or disable use Tiramizoo delivery for specified product

# Checking the Tiramizoo delivery status #

Go to *Order tab* to check the current delivery status