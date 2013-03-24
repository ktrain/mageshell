# Mageshell


###### Mageshell is an interactive prompt that runs arbitrary PHP commands within your Magento environment.

Want to test out your new Magento model and its amazing methods but don't want to write a controller test action?
Simply fire up Mageshell and do ```Mage::getModel('yourmodule/yourmodel')->amazingMethod()```.
Mageshell runs the code within the context of your Magento install and prints the result in a helpful format.

For Linux users and OS X users who have compiled PHP with ```--include-readline```,
Mageshell uses built-in PHP functions to store command history in ```.magesh_history```,
which can be recalled using the keyboard up and down arrows.
This functionality is not implemented for Windows, but is in the works.


### Installation

You can safely clone this repo directly in to the root of your Magento installation.
Both PHP files must be present.


### Starting Mageshell

To start Mageshell normally: ```php run-mageshell.php```.

To start with admin context: ```php run-mageshell.php -u <username> -p<password>```.
*Note the lack of space between ```-p``` and ```<password>```.*


### Command Evaluation

Unlike a normal interactive PHP shell that you get from doing ```php -a```,
Mageshell executes your command on hitting ```Enter``` and the value of the result,
as well as the class if the result is an object, is always printed.
If the result is a ```Varien_Object```, then its data is printed.
Data from collections is suppressed in order to avoid unwanted ```load()``` calls.

The ```;``` at the end of commands is optional.


###### Example

```php
magesh> $userId = 2

Result: 2

magesh> $user = Mage::getModel('admin/user')

Result: [ST_Core_Model_Admin_User]
Data:
Array
(
)


magesh> $user->load($userId)

Result: [ST_Core_Model_Admin_User]
Data:
Array
(
    [user_id] => 2
    [firstname] => Cookie
    [lastname] => Monster
    [email] => cookiemonster@sesamestreet.com
    [username] => admin
    [password] => c42cfed9ba28d04013b494e18939593b8715ddb3a20445154ac1171999590819:pK
    [created] => 2007-08-22 21:51:03
    [modified] => 2013-01-03 21:22:38
    [logdate] => 2007-08-23 01:51:03
    [lognum] => 2
    [reload_acl_flag] => 0
    [is_active] => 1
    [extra] => 
    [rp_token] => 
    [rp_token_created_at] => 
    [failures_num] => 0
    [first_failure] => 
    [lock_expires] => 
)
```
