<?php
/**
 * Modul für Kreditkartenzahlungen
 * 
 * @version     $Id: vrpay_cc.php 16 2010-06-03 22:12:54Z tr4nt0r $
 * 
 * @package     xt-commerce
 * @subpackage	vr-pay
 * @copyright   (c) 2010 Manfred Dennerlein. All rights reserved.
 * @license     GNU/GPL, see LICENSE.txt
 * @author		Manfred Dennerlein <manni@zapto.de>
 */
chdir('../../');
require ('includes/application_top.php');
include (DIR_FS_DOCUMENT_ROOT.'callback/vrpay/class.vrpay_callback.php');

$callback = new vrpay_callback();

echo 'STATUS=SUCCESS';

?>