==Title==
oxTiramizoo

==Author==
Tiramizoo GmbH

==Prefix==
oxTiramizoo

==Version==
1.0.3

==Link==
http://tiramizoo.com/

==Mail==
support@tiramizoo.com

==Description==
Integrates Tiramizoo Delivery into OXID ESHOP.

==Extend==
*oxorder
--_loadFromBasket
*oxbasket
--_calcDeliveryCost
*order
--init
--render
--execute
*payment
--getAllSets
--init
--changeshipping
--render
*oxshopcontrol
--_process

==Installation==
Add the module entries in admin.

==Modules==
oxorder       => oxtiramizoo/core/oxtiramizoo_oxorder
oxbasket      => oxtiramizoo/core/oxtiramizoo_oxbasket
order         => oxtiramizoo/application/controllers/oxtiramizoo_order
payment       => oxtiramizoo/application/controllers/oxtiramizoo_payment
oxshopcontrol => oxtiramizoo/application/controllers/oxtiramizoo_oxshopcontrol


==Libraries==

==Resources==