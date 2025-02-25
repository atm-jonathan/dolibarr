<?php
// Load Dolibarr environment
$res=0;
// Try master.inc.php into web root detected using web root caluclated from SCRIPT_FILENAME
$tmp=empty($_SERVER['SCRIPT_FILENAME']) ? '' : $_SERVER['SCRIPT_FILENAME'];$tmp2=realpath(__FILE__); $i=strlen($tmp)-1; $j=strlen($tmp2)-1;
while ($i > 0 && $j > 0 && isset($tmp[$i]) && isset($tmp2[$j]) && $tmp[$i]==$tmp2[$j]) {
	$i--;
	$j--;
}
if (! $res && $i > 0 && file_exists(substr($tmp, 0, ($i+1))."/master.inc.php")) {
	$res=@include substr($tmp, 0, ($i+1))."/master.inc.php";
}
if (! $res && $i > 0 && file_exists(dirname(substr($tmp, 0, ($i+1)))."/master.inc.php")) {
	$res=@include dirname(substr($tmp, 0, ($i+1)))."/master.inc.php";
}
// Try master.inc.php using relative path
if (! $res && file_exists("../master.inc.php")) {
	$res=@include "../master.inc.php";
}
if (! $res && file_exists("../../master.inc.php")) {
	$res=@include "../../master.inc.php";
}
if (! $res && file_exists("../../../master.inc.php")) {
	$res=@include "../../../master.inc.php";
}
if (! $res) {
	die("Include of master fails");
}


require_once DOL_DOCUMENT_ROOT .'/core/lib/admin.lib.php';

if ($argc < 2) {
	echo "Usage: php module_manager.php <ModuleClass>\n";
	exit(1);
}
$moduleClass = $argv[1];

/**
* Check if a module is active
*/
function isModuleCurrentlyActive($moduleClass)
{
	global $db;
	$sql = "SELECT value FROM ".$db->prefix()."const WHERE name = 'MAIN_MODULE_" . $moduleClass . "'";
	$resql = $db->query($sql);

	if ($resql) {
		$obj = $db->fetch_object($resql);
		return ($obj && $obj->value == '1');
	}
	return false;
}

/**
 * Deactivate a module
 */
function disableCustomModule($moduleClass)
{
	if (function_exists('unActivateModule')) {
		unActivateModule($moduleClass);
		echo "Module $moduleClass deactivated\n";
	} else {
		echo "Error: unActivateModule function not found\n";
	}
}

/**
 * Activate a module
 */
function enableCustomModule($moduleClass)
{
	if (function_exists('activateModule')) {
		activateModule($moduleClass);
		echo "Module $moduleClass activated\n";
	} else {
		echo "Error: activateModule function not found\n";
	}
}

// Check if module is active before deactivating
if (isModuleCurrentlyActive($moduleClass)) {
	disableCustomModule($moduleClass);
	enableCustomModule($moduleClass);
} else {
	echo "Module $moduleClass is already disabled. Skipping.\n";
}
