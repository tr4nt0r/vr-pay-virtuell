<?php
/* -----------------------------------------------------------------------------------------
   $Id: checkout_vrepay_dialog.php v1.2 2007/03/19 fbi $   
   
   Path: xtc/

   XT-Commerce - community made shopping
   http://www.xt-commerce.com

   Copyright (c) 2003 XT-Commerce
   -----------------------------------------------------------------------------------------
   based on: 
   (c) 2000-2001 The Exchange Project  (earlier name of osCommerce)
   (c) 2002-2003 osCommerce(checkout_success.php,v 1.48 2003/02/17); www.oscommerce.com 
   (c) 2003	 nextcommerce (checkout_success.php,v 1.14 2003/08/17); www.nextcommerce.org

   Released under the GNU General Public License
   -----------------------------------------------------------------------------------------
   Third Party contribution:

   Copyright (c) 2009 Netzkollektiv / Cardprocess

   Commercial License
   ---------------------------------------------------------------------------------------*/

include ('includes/application_top.php');

// if the customer is not logged on, redirect them to the shopping cart page
if (!isset ($_SESSION['customer_id'])) {
	xtc_redirect(xtc_href_link(FILENAME_SHOPPING_CART));
}

require (DIR_WS_MODULES . 'payment/vrepay_giropay.php');

$vrepay = new vrepay_giropay();
$vrepay->epay_log( "checkout_vrepay_giropay redirect to..." . print_r($_SESSION['vrepaydialog'], TRUE) );

$session = $_SESSION['vrepaydialog'];

xtc_redirect($session['redirect']);

require (DIR_WS_INCLUDES.'header.php');

?>