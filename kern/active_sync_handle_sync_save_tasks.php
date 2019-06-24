<?
function active_sync_handle_sync_save_tasks($xml, $user, $collection_id, $server_id)
	{
	$data = array();

	$codepage_table = array
		(
		"Tasks" => active_sync_get_default_tasks()
		);

	foreach($codepage_table as $codepage => $null)
		foreach($codepage_table[$codepage] as $token => $value)
			{
			if(isset($xml->ApplicationData->$token) === false)
				continue;

			$data[$codepage][$token] = strval($xml->ApplicationData->$token);
			}

	if(isset($xml->ApplicationData->Body))
		foreach($xml->ApplicationData->Body as $body)
			{
			$b = array();

			foreach(active_sync_get_default_body() as $token => $value)
				{
				if(isset($body->$token) === false)
					continue;

				$b[$token] = strval($body->$token);
				}

			if(isset($b["Data"]) === false)
				continue;

			if(strlen($b["Data"]) == 0)
				continue;

			$data["Body"][] = $b;
			}

	if(isset($xml->ApplicationData->Categories))
		if(count($xml->ApplicationData->Categories->Category) > 0)
			foreach($xml->ApplicationData->Categories->Category as $category)
				$data["Categories"][] = strval($category);

	if(isset($xml->ApplicationData->Recurrence))
		foreach(active_sync_get_default_recurrence() as $token => $value)
			{
			if(isset($xml->ApplicationData->Recurrence->$token) === false)
				continue;

			$data["Recurrence"][$token] = strval($xml->ApplicationData->Recurrence->$token);
			}

	return(active_sync_put_settings_data($user, $collection_id, $server_id, $data) === false ? 16 : 1);
	}
?>
