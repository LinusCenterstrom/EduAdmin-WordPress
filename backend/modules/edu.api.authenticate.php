<?php
include_once(__DIR__ . "/../../includes/loApiClient.php");
$eduapi = new EduAdminClient();

if(isset($_REQUEST['authenticate']) && isset($_REQUEST['key']))
{
	$info = edu_DecryptApiKey($_REQUEST['key']);

	if(!isset($_SESSION['edu-usertoken']))
	{
		$_SESSION['edu-usertoken'] = $eduapi->GetAuthToken($info->UserId, $info->Hash);
	}
	else
	{
		$valid = $eduapi->ValidateAuthToken($_SESSION['edu-usertoken']);
		if(!$valid)
		{
			$_SESSION['edu-usertoken'] = $eduapi->GetAuthToken($info->UserId, $info->Hash);
		}
	}

	echo edu_encrypt("edu_js_token_crypto", $_SESSION['edu-usertoken']);
}
?>