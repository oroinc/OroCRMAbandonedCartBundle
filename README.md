OroCRMAbandonedCartBundle
===================

This Bundle provides management of Abandoned Cart Campaigns, which are used
for setup abandoned cart notifications in MailChimp

Initially Abandoned Cart Campaign is Marketing List with predefined 
type = **'dynamic'** and entity = **'Shopping Cart'**

In Marketing List there is field *source*, if this field has value *abandoned_cart*,  
it means that this is Abandoned Cart Campaign

## Abandoned Cart creation

During Abandoned Cart creation we automatically create campaign, which will
be used for the conversion. 
