<?php
###############################################
#    ShareCart 1.05 for Opencart by AlexDW    #
###############################################
$_['heading_title']    = '<i class="fa fa-cubes fa-lg" style="color: #5BC0DE"></i> ShareCart - Creating and using Shared Carts';

// Text
$_['text_module']		= 'Modules';
$_['text_edit']			= 'Edit ShareCart Module';
$_['text_success']		= 'Success: You have modified module ShareCart!';
$_['success_list']		= 'Cart list has been updated';

$_['text_settings']		= 'Settings';
$_['text_yes']			= 'Yes';
$_['text_no']			= 'No';

$_['text_sharelist']	= 'Shared Carts';

$_['text_clear']		= 'Clear';
$_['text_status']		= 'Customer status';
$_['ckday']				= 'days';
$_['guest']				= 'Guest';
$_['registered']		= 'Registered';
$_['customer']			= 'Customer';
$_['session']			= 'Session';
$_['uid']				= 'Identifier';
$_['ip']				= 'IP';
$_['user_agent']		= 'User Agent';
$_['column_pro']		= 'Products';
$_['total_goods']		= 'Total products';
$_['date_added']		= 'Update date';
$_['text_date_beg']		= 'From';
$_['text_date_end']		= 'to';
$_['unknown_product']	= '<span style="color:red">This product is disabled or removed</span>';

$_['confirm_refresh']	= 'Update the dates of adding for selected carts?';
$_['confirm_delete']	= 'Delete selected carts?';

$_['help_pro']			= 'Show / Hide';
$_['help_share']	= '<i class="fa fa-check"></i> Create and use Shared Carts.<br>Contents of Shared Carts are accessible via links and stored in the DB regardless of the current user carts.<br><br>If enabled, you can share the contents of the current cart simply by sending a link to it.<br> When use such a link, the same set of products will be added to the user\'s cart as it was when the link was created.<br><b>Please note, that to display the "Share cart" button, you will need to add the ShareCart module to the Design > Layouts</b>';
$_['help_share_onlyreg']	= '<i class="fa fa-check"></i> Ability to share the contents of the shopping cart via a link.<br><br>If enabled, only registered and logged-in users can create links for Share Cart.<br>If disabled, the ability to share the contents of the cart will be available to everyone.<br>Adding products from existing links is available to everyone, regardless of the option selected.';
$_['help_share_time']	= '<i class="fa fa-check"></i> Storage time periods for Shared Carts, depending on the creator.<br>Shared Carts with expired storage time will be deleted and their links will become invalid.';
$_['help_share_limit']	= '<i class="fa fa-check"></i> Limit of product items when creating a link to a shopping cart.<br>If the number of product items in the cart exceeds the limit for the Creator\'s group , the ability to share the cart and create a link to it will not be available.';
$_['help_share_elimit']	= '<i class="fa fa-check"></i> Total limit on the number of product items added by link.<br>If the total number of product items in the current basket plus the number of positions by link exceeds the limit for the specified group, adding products from the Shared Cart will not be available.';
$_['help_share_redirect']	= '<i class="fa fa-check"></i> The system address of the page that the user will be redirected to after adding products via the link.<br>For the main page it is <b>common/home</b>, for the shopping cart page it is <b>checkout/cart</b> and so on. If you are not good at this, leave it empty or specify checkout/cart';

// Entry
$_['entry_status']		= 'Module status';

$_['entry_share']		= 'Share Cart';
$_['entry_share_onlyreg']	= 'Only for registered';
$_['entry_share_timer']		= 'Storage period of registered user link';
$_['entry_share_timeg']		= 'Storage period of guest link';
$_['entry_share_limitr']	= 'Limit to creating for registered user';
$_['entry_share_limitg']	= 'Limit to creating for guest';
$_['entry_share_elimitr']	= 'Limit to adding for registered user';
$_['entry_share_elimitg']	= 'Limit to adding for quest';
$_['entry_share_redirect']	= 'Route for redirect after adding';

// Error
$_['error_permission'] = 'Warning: You do not have permission to modify module ShareCart!';
$_['error_permission_list']	= 'Warning: You do not have permission to modify Cart List!';
?>