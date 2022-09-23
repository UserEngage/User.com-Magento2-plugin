# User.com Magento 2 plugin

Official Magento 2 module for User.com integration. This tag will implement User.com on your Website and synchronize your 

## Requirements
Magento 2.3.4 version or higher  
PHP 7.4 or higher

## Installation process
1) Create app/code/Usercom/Analytics/ folder
```code
mkdir -p app/code/Usercom/Analytics/
```
2) Move to the app/code/Usercom/Analytics folder
```code
cd app/code/Usercom/Analytics/
```
3) Download the plugin
```code
git clone https://github.com/UserEngage/User.com-Magento2-plugin.git .
```
4) Move to the main Magento folder
```code
cd ../../../../
```
5) Update Magento config
```code
 bin/magento s:up && bin/magento s:d:c && bin/magento s:sta:d -f && bin/magento c:c && bin/magento c:f
 ```
## Configuration

### Required configuration data
- **API Key**
	- You can find it in your `Application -> Settings -> Setup & Integrations`
- **Application Domain**
	- You can find it in your `Application -> Settings -> Setup & Integrations`
- **REST API Key**
	- You can find it in your `Application -> Settings -> App Settings -> Advanced -> Public REST API keys`
 
## Functionality
1. Installation of widget tracking code on every webpage of your Magento app.
2. Gather data from login/registration forms and send it to the User.com app.
3. Gather data about the users from checkout pages and send it directly to the app.
4. Gather events and product events with a full range of user and product attributes.
5. Historical data synchronization.
6. Newsletter synchronization.

### Data synchronization
Historical data can be synchronized within a specific date range. You can synchronize purchases, orders and customers for the last 3/6/12 months.  

1. Customers from Magento create users inside User.com app based on their personal data. Please, do it as a first step of the synchronization process.
2. Orders (as order events) are assigned to specific users inside User.com app.
3. Purchases (as product events) are assigned to specific users and products inside User.com app.

## Newsletter synchronization 
1. Create automation in the Automations section inside User.com app. Automation should be created with `Client’s attribute change` module connected to the `API call` module.
2. After any change of the `Unsubscribe from emails` user's attribute, the automation triggers an API request sent to the "http://your-domain/rest/all/V1/usercom-analytics/newsletter". Inside the API call module, you have to add a custom "Content-Type" header with "application/json" value.

## Developers' note
- custom id - user identifier created inside the Magento
- usercom id - user identifier created inside the User.com
- userKey - user identifier automatically created by User.com widget


