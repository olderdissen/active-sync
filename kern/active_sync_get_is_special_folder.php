<?
function active_sync_get_is_special_folder($type)
	{
	return(in_array($type, array(2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 19)) === false ? 0 : 1);
	}
?>
