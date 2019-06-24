<?
function active_sync_handle_sync_send_contacts(& $response, $user, $collection_id, $server_id, $collection)
	{
	$data = active_sync_get_settings_data($user, $collection_id, $server_id);

	$response->x_switch("AirSync");

	$response->x_open("ApplicationData");

		$codepage_table = array
			(
			"Contacts" => active_sync_get_default_contacts(),
			"Contacts2" => active_sync_get_default_contacts2()
			);

		foreach($codepage_table as $codepage => $null)
			{
			if(isset($data[$codepage]) === false)
				continue;

			$response->x_switch($codepage);

			foreach($codepage_table[$codepage] as $token => $value)
				{
				if(isset($data[$codepage][$token]) === false)
					continue;

				if(strlen($data[$codepage][$token]) == 0)
					{
					$response->x_open($token, false);

					continue;
					}

				if(in_array($token, array("Aniversary", "Birthday")))
					$data[$codepage][$token] = date("Y-m-d\TH:i:s\Z", strtotime($data[$codepage][$token]));

				$response->x_open($token);
					$response->x_print($data[$codepage][$token]);
				$response->x_close($token);
				}
			}

		if(isset($data["Children"]))
			{
			$response->x_switch("Contacts");

			$response->x_open("Children");

				foreach($data["Children"] as $value)
					{
					$response->x_open("Child");
						$response->x_print($value);
					$response->x_close("Child");
					}

			$response->x_close("Children");
			}

		if(isset($data["Body"]))
			{
			$default_class = active_sync_get_class_by_collection_id($user, $collection_id);

			if(isset($collection->Options))
				{
				foreach($collection->Options as $options)
					{
					if(isset($options->Class))
						if(isset($data["AirSync"]["Class"]))
							if(strval($options->Class) != $data["AirSync"]["Class"])
								continue;

					if(isset($options->RightsManagementSupport))
						if(intval($options->RightsManagementSupport) == 1)
							if(isset($data["RightsManagement"]))
								{
								$response->x_switch("RightsManagement");

								$response->x_open("RightsManagementLicense");

									# foreach($data["RightsManagement"] as $rights_management_id => $rights_management_data) # multiple licenses allowed on single message?

									foreach(active_sync_get_default_rights_management() as $token => $value)
										{
										if(isset($data["RightsManagement"][$token]) === false)
											continue;

										if(strlen($data["RightsManagement"][$token]) == 0)
											{
											$response->x_open($token, false);

											continue;
											}

										$response->x_open($token);
											$response->x_print($data["RightsManagement"][$token]);
										$response->x_close($token);
										}

								$response->x_close("RightsManagementLicense");
								}

					foreach($options->BodyPreference as $preference)
						{
						foreach($data["Body"] as $random_body_id => $null) # !!!
							{
							if(isset($data["Body"][$random_body_id]["Type"]) === false)
								continue;

							if($data["Body"][$random_body_id]["Type"] != intval($preference->Type))
								continue;

							if(isset($data["Body"][$random_body_id]["Data"]) === false)
								continue;

							$response->x_switch("AirSyncBase");

							$response->x_open("Body");

								if(isset($preference["Preview"]))
									{
									foreach($data["Body"] as $random_preview_id => $null) # !!!
										{
										if(isset($data["Body"][$random_preview_id]["Type"]) === false)
											continue;

										if($data["Body"][$random_preview_id]["Type"] != 1)
											continue;

										$response->x_open("Preview");
											$response->x_print(substr($data["Body"][$random_preview_id]["Data"], 0, intval($preference->Preview)));
										$response->x_close("Preview");
										}
									}

								if(isset($preference->TruncationSize))
									if(isset($data["Body"][$random_body_id]["EstimatedDataSize"]))
										if(intval($preference->TruncationSize) < $data["Body"][$random_body_id]["EstimatedDataSize"])
											{
											$data["Body"][$random_body_id]["Data"] = substr($data["Body"][$random_body_id]["Data"], 0, intval($preference->TruncationSize));

											$response->x_open("Truncated");
												$response->x_print(1);
											$response->x_close("Truncated");
											}

								foreach($data["Body"][$random_body_id] as $token => $value)
									{
									if(strlen($data["Body"][$random_body_id][$token]) == 0)
										{
										$response->x_open($token, false);

										continue;
										}

									$response->x_open($token);
										$response->x_print($data["Body"][$random_body_id][$token]); # opaque data will fail :(
									$response->x_close($token);
									}

							$response->x_close("Body");
							}
						}
					}
				}
			}

		if(isset($data["Categories"]))
			{
			$response->x_switch("Contacts");

			$response->x_open("Categories");

				foreach($data["Categories"] as $value)
					{
					$response->x_open("Category");
						$response->x_print($value);
					$response->x_close("Category");
					}

			$response->x_close("Categories");
			}

	$response->x_close("ApplicationData");
	}
?>
