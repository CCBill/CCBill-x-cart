=== Plugin Name ===
Contributors: CCBill
Tags: CCBill, payment, gateway, X-Cart

Accept CCBill payments on your X-Cart website.

== Description ==

The CCBill payment gateway plugin for X-Cart allows you to easily configure and accept CCBill payments on your X-Cart web store.

== Installation ==

Installation involves the following steps:
* Installing the CCBill payment module for X-Cart
* Configuring your CCBill account for use with X-Cart
* Configuring the module with your CCBill account information

** Installing via File Upload**

The CCBill X-Cart module is installed by uploading the files in the zip file downloaded from the CCBill website into your X-Cart installation.  There are two folders which must be copied:
	/classes/XLite/Module/CCBill/CCBill_Payment
	/skins/admin/en/modules/CCBill/CCBill_Payment

After uploading the required files, sign into your X-Cart admin.  From the left menu, select "System Settings" -> "Cache Management."  Then choose "Re-deploy the Store."    

Once complete, select "Modules" in the left menu.  Use the top search box to locate the CCBill module.  If the files were copied correctly, the CCBill module will appear in your search results.  Check the box to enable the module and click the "Save Changes" button at the bottom of the page.  The store will automatically re-deploy.


**Configuring your CCBill Account**

Before using the plugin, it’s necessary to configure a few things in your CCBill account.  
Please ensure the CCBill settings are correct, or the payment module will not work.

**Enabling Dynamic Pricing**

Please work with your CCBill support representative to activate "Dynamic Pricing" for your account.  
You can verify that dynamic pricing is active by selecting "Feature Summary" under the 
"Account Info" tab of your CCBill admin menu.  Dynamic pricing status appears at the 
bottom of the "Billing Tools" section.

**Creating a Salt / Encryption Key**

A "salt" is a string of random data used to make your encryption more secure.  
You must contact CCBill Support to generate your salt.  Once set, it will be 
visible under the "Advanced" section of the "Sub Account Admin" menu.  It will 
appear in the "Encryption Key" field of the "Upgrade Security Setup Information" 
section.

**Disabling User Management**

Since this account will be used for dynamic pricing transactions rather than 
managing user subscription, user management must be disabled.
In your CCBill admin interface, navigate to "Sub Account Admin" and select 
"User Management" from the left menu.  
Select "Turn off User Management" in the top section.  
Under "Username Settings," select "Do Not Collect Usernames and Passwords."


**Creating a New Billing Form**

The billing form is the CCBill form that will be displayed to customers after they choose to check out using CCBill.  The billing form accepts customer payment information, processes the payment, and returns the customer to your X-Cart store where a confirmation message is displayed.

*Important*
CCBill provides two types of billing forms.  FlexForms is our newest (and recommended) system, but standard forms are still supported.  Please choose a form type and proceed according to the section for Option 1 or Option 2, according to your selection.

**Option 1: Creating a New Billing Form - FlexForms**

Note: Skip this section if using standard forms

To create a FlexForm form for use with X-Cart, first ensure "all" is selected in the top Client Account dropdown.   FlexForms are not specific to sub accounts, and cannot be managed when a sub account is selected.

Navigate to the FlexSystems tab in the top menu bar and select "FlexForms Payment Links."  All existing forms will be displayed in a table.

Click the "Add New" button in the upper-left to create a new form.

*Payment Flow Name*
At the top, enter a name for the new payment flow (this will be different than the form name, as a single form can be used in multiple flows).  

*Form Name*
Under Form Name, enter a name for the form.

*Dynamic Pricing*
Under Pricing, check the box to enable dynamic pricing.

*Layout*
Select your desired layout, and save the form.

*Edit the Flow*
Click the arrow button to the left of your new flow to view the details.

*Approval URL*
In the left menu, select "A URL."

Select "Add A New URL" and enter the base URL for your store, followed by: 

/cart.php?target=order&Action=CheckoutSuccess

For example, if your X-Cart store is located at http://www.test.com, the Approval URL would be:

http://www.test.com/cart.php?target=order&Action=CheckoutSuccess

*URL Name*
Enter a name for this URL.  This should be a descriptive name such as "Checkout Success."

*Redirect Time*
Select a redirect time of 1 second using the slider at the bottom and save the form.

*Add Pass-Through Variables*
In the CCBill top navigation menu, select "URLs Library."

The Saved URLs Editor appears.

Click the Plus (+) button under Sandbox Name/Value Pairs for the Payment Success URL you created earlier.

The Name/Value Parameters editor displays.

Enter the values exactly as shown above.

Your Parameter (key): merchant_order_id

Use this method: Merchant Pass-through Parameter

Parameter name: merchant_order_id

Click the "Add" button when finished.  Ensure the information you entered shows in the "Live" column of the URL library as well.

*Promote to Live*
Click the "Promote to Live" button to enable your new form to accept payments.

*Note the Flex ID*
Make note of the Flex ID: this value will be entered into the form name when completing the configuration in X-Cart.

**WebHooks (FlexForms Only)

Note: Skip this section if using standard forms

As a final step for configuring a FlexForm, select the sub account to be used with X-Cart from the top Client Account dropdown.  

Navigate to the Account Info tab in the top menu bar and select "Sub Account Admin."

Select "Webhooks" from the left menu, then select "Add" to add a new webhook.

*Webhook Succes URL*
Under Approval Post URL, enter the base URL for your store, followed by: 

/cart.php?target=payment_return&txn_id_name=cart_order_id&action=Approval_Post

For example, if your store is located at http://www.test.com, the Approval URL would be:

http://www.test.com/cart.php?target=payment_return&txn_id_name=cart_order_id&action=Approval_Post

Select "NewSaleSuccess," then click the Update button to save the Webhook information.

*Webhook Failure URL*
Under Denial Post URL, enter the base URL for your store, followed by: 

/cart.php?target=payment_return&txn_id_name=cart_order_id&action=Denial_Post

For example, if your store is located at http://www.test.com, the Denial URL would be:

http://www.test.com/cart.php?target=payment_return&txn_id_name=cart_order_id&action=Denial_Post

Select "NewSaleFailure," then click the Update button to save the Webhook information.

*Skip to "Configuration - X-Cart"*

Your CCBill FlexForms configuration is now complete.  
Please skip directly to the section titled "Configuration - X-Cart."


**Option 2: Creating a New Billing Form - Standard Forms**

Note: Skip this section if using FlexForms

To create a billing form for use with X-Cart, navigate to the “Form Admin” section of your CCBill admin interface.  All existing forms will be displayed in a table.

Click “Create New Form” in the left menu to create your new form.

Select the appropriate option under “Billing Type.”  (In most cases, this will be “Credit Card.”)

Select “Standard” under “Form Type” unless you intend to customize your form.

Select the desired layout, and click “Submit” at the bottom of the page.

Your new form has been created, and is visible in the table under “View All Forms.”  In this example, our new form is named “201cc.”  Be sure to note the name of your new form, as it will be required in the X-Cart configuration section.


**Configuring the New Billing Form**

Click the title of the newly-created form to edit it.  In the left menu, click “Basic.”

Under “Basic,” select an Approval Redirect Time of 3 seconds, and a Denial Redirect Time of “None.”


**Configuring Your CCBill Account**

In your CCBill admin interface, navigate to "Sub Account Admin" and select "Basic" from the left menu.  

**Site Name**

Enter the URL of your X-Cart store under "Site Name"

**Approval URL**

Under Approval URL, enter the base URL for your X-Cart store, followed by: 

/cart.php?target=order&order_number=%%merchant_order_id%%&Action=CheckoutSuccess

For example, if your X-Cart store is located at http://www.test.com, the Approval URL would be:

http://www.test.com/cart.php?target=order&order_number=%%merchant_order_id%%&Action=CheckoutSuccess

**Denial URL**

Under Denial URL, enter the base URL for your X-Cart store, followed by: 

/cart.php?target=order&order_number=%%merchant_order_id%%&Action=CheckoutFailure

For example, if your X-Cart store is located at http://www.test.com, the Denial URL would be:

http://www.test.com/cart.php?target=order&order_number=%%merchant_order_id%%&Action=CheckoutFailure

**Redirect Time**

Select an approval redirect time of 3 seconds, and a denial redirect time of "Instant."


**Background Post**

While still in the "Sub Account Admin" section, select "Advanced" from the left menu.  Notice the top section titled "Background Post Information."  We will be modifying the Approval Post URL and Denial Post URL fields.

**Approval Post URL**
Under Approval Post URL, enter the base URL for your X-Cart store, followed by: 

/cart.php?target=payment_return&txn_id_name=cart_order_id&action=Approval_Post

For example, if your X-Cart store is located at http://www.test.com, the Approval URL would be:

http://www.test.com/cart.php?target=payment_return&txn_id_name=cart_order_id&action=Approval_Post

**Denial Post URL**
Under Denial Post URL, enter the base URL for your X-Cart store, followed by: 

/cart.php?target=payment_return&txn_id_name=cart_order_id&action=Denial_Post

For example, if your X-Cart store is located at http://www.test.com, the Denial URL would be:

http://www.test.com/cart.php?target=payment_return&txn_id_name=cart_order_id&action=Denial_Post

**Confirmation**
Your CCBill account is now configured. In your CCBill admin interface, navigate to "Sub Account Admin" and ensure the information displayed is correct.

**Configuring the CCBill X-Cart Module**

Select "Store Setup" in the left menu and choose "Payment Methods."  Click the button at the top to add a payment method.  Locate "CCBill Payments" in the list and click "Choose."    

The module configuration screen displays.  Enter your CCBill account information and click "Update." 


**Confirmation**

Your CCBill account is now configured. In your CCBill admin interface, navigate to “Sub Account Admin” and ensure the information displayed is correct.


**Configuration - X-Cart**

**General Options**

Select “Store Setup” in the left menu and choose “Payment Methods.”  Click the button at the top to add a payment method.  Locate “CCBill Payments” in the list and click “Choose.”

The configuration page displays.
**CCBill Options**

**Client Account Number**
Enter your CCBill client account number.

**Client SubAccount Number**
Enter your CCBill client sub-account number.

**Form Name**
Enter the name of the form created during CCBill account configuration, or FlexForm ID if using FlexForms.

**Is FlexForm**
Select “Yes” if using FlexForms.

**Currency**
Select the billing currency.  Ensure this selection matches the selection made in the “Store Setup -> Localization -> Currencies” section of the X-Cart administration menu.

**Salt**
Enter your salt / encryption key obtained during CCBill configuration.
Click “Update” at the bottom of the CCBill configuration section.  You will be redirected to the payment methods list, and “CCBill Payments” should be marked as active.  If not, click the active/inactive switch to mark it as active.

**Confirmation**
You are now ready to process payments via CCBill!  Please conduct a few test transactions (using test data provided by CCBill) to ensure proper operation before enabling live billing mode in your CCBill account.


