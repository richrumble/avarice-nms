<?php
header("Content-Type: text/plain");
define('HKEY_CLASSES_ROOT',   0x80000000);
define('HKEY_CURRENT_USER',   0x80000001);
define('HKEY_LOCAL_MACHINE',  0x80000002);
define('HKEY_USERS',          0x80000003);
define('HKEY_CURRENT_CONFIG', 0x80000005);
define('HKEY_DYN_DATA',       0x80000006);

Win32RegistryIterator($o_Win32Registry = new COM('winmgmts://./root/default:StdRegProv'), HKEY_LOCAL_MACHINE, 'SYSTEM\\CurrentControlSet\\services\\eventlog');

function Win32RegistryIterator(COM $o_Win32Registry, $i_HiveKey, $s_RootKey) {
	static $i_Depth = -1;
	static $a_RegTypes = array
		(
		1 => 'REG_SZ (1)',
		2 => 'REG_EXPAND_SZ (2)',
		3 => 'REG_BINARY (3)',
		4 => 'REG_DWORD (4)',
		7 => 'REG_MULTI_SZ (7)',
		10 => 'REG_RESOURCE_REQUIREMENT_LIST (10)',
		);

	$a_Keys  = new VARIANT();
	$a_Names = new VARIANT();
	$a_Types = new VARIANT();

	$i_EnumKeyState    = $o_Win32Registry->EnumKey   ($i_HiveKey, $s_RootKey, $a_Keys);
	$i_EnumValuesState = $o_Win32Registry->EnumValues($i_HiveKey, $s_RootKey, $a_Names, $a_Types);

	if (VT_NULL !== variant_get_type($a_Keys)) {
		foreach($a_Keys as $i_Key => $s_Key) {
			echo '[', $s_Key, ']', PHP_EOL;
			Win32RegistryIterator($o_Win32Registry, $i_HiveKey, $s_RootKey . '\\' . $s_Key);
			}
		}

	if (VT_NULL !== variant_get_type($a_Names)) {
		$a_ExtractedTypes = array();
		foreach($a_Types as $i_Type) {
			$a_ExtractedTypes[] = $i_Type; 
			}
		foreach($a_Names as $i_Name => $s_Name) {
			$m_RegValue = new VARIANT();

			echo $i_Name, ' => ', ('' === $s_Name ? '(Default)' : $s_Name), ' of type ', $a_RegTypes[$a_ExtractedTypes[$i_Name]], ' with a value of ';

			switch($a_ExtractedTypes[$i_Name]) {
				case 1 : // REG_SZ
					$o_Win32Registry->GetStringValue($i_HiveKey, $s_RootKey, $s_Name, $m_RegValue);
					echo '"', $m_RegValue, '"';
					break;

				case 2 : // REG_EXPAND_SZ
					$o_Win32Registry->GetExpandedStringValue($i_HiveKey, $s_RootKey, $s_Name, $m_RegValue);
					echo '"', $m_RegValue, '"';
					break;

				case 3  : // REG_BINARY
				case 10 : // REG_RESOURCE_REQUIREMENT_LIST
					$o_Win32Registry->GetBinaryValue($i_HiveKey, $s_RootKey, $s_Name, $m_RegValue);
					if (VT_NULL !== variant_get_type($m_RegValue)) {
						foreach($m_RegValue as $i_RegValue) {
							echo str_pad(dechex($i_RegValue), 2, '0', STR_PAD_LEFT), ' ';
							}
						}
					break;

				case 4 : // REG_DWORD
  					$o_Win32Registry->GetDWORDValue($i_HiveKey, $s_RootKey, $s_Name, $m_RegValue);
					echo '0x', str_pad(dechex($m_RegValue), 8, '0', STR_PAD_LEFT), ' (', $m_RegValue, ')';
					break;

				case 7 : // REG_MUTLI_SZ
  					$o_Win32Registry->GetMultiStringValue($i_HiveKey, $s_RootKey, $s_Name, $m_RegValue);
  					if (VT_NULL !== variant_get_type($m_RegValue)) {
  						try {
	  						foreach($m_RegValue as $s_RegValue) {
	  							echo PHP_EOL, $s_RegValue;
	  							}
	  						}
	  					catch(com_exception $e) {
	  						// As yet, I cannot determine if the $m_RegValue is empty for a REG_MULTI_SZ,
	  						// so catch the exception and test that instead.
	  						if (-2147352565 !== $e->getCode()) {
	  							throw $e;
	  							}
	  						}
  						}
					break;
				}
			echo PHP_EOL;
			}
		}
	}
?>