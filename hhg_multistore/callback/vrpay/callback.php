<?php
/**
 * Modul für Kreditkartenzahlungen
 * 
 * @version     $Id: vrpay_cc.php 16 2010-06-03 22:12:54Z tr4nt0r $
 * 
 * @package     hhg multistore
 * @subpackage	vr-pay
 * @copyright   (c) 2010 Manfred Dennerlein. All rights reserved.
 * @license     GNU/GPL, see LICENSE.txt
 * @author		Manfred Dennerlein <manni@zapto.de>
 */
chdir('../../');
require ('core/application_top.php');
require_once (DIR_FS_CORE_INC . 'inc.hhg_db_prepare_input.php');
$configuration = hhg_db_query('select configuration_key as cfgKey, configuration_value as cfgValue from configuration_' . $_SESSION['actual_store'] . '_modules WHERE configuration_key LIKE "MODULE_PAYMENT_VRPAY_%"', true);
while (!$configuration->EOF) {
	@define($configuration->fields['cfgKey'], $configuration->fields['cfgValue']);
	$configuration->MoveNext();
}

include (DIR_FS_CATALOG.'callback/vrpay/class.vrpay_callback.php');

$callback = new vrpay_callback();

echo 'STATUS=SUCCESS';

?>