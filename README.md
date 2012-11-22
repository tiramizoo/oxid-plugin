oxid-plugin-dev
===============

This folders contains OXID eSales Module for integration with [Tiramizoo API](http://dev.tiramizoo.com/)
Module working with foloowingOXID eSales versions: 4.3.2+, 4.4.x, 4.5.x 

# Installation #

*   Copy all files from *copy_this* folder to your OXID eSales installation path

*   Add Shop Modules in **Master Settings -> Core Settings -> System Tab -> Modules**

    ```
    payment => oxtiramizoo_payment
    order => oxtiramizoo_order
    ```

*   Change the templates in *changed_full* folder

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