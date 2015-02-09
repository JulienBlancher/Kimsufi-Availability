<?php

// put your config here
define("DEV_MODE", false);
define("MAIL", ""); // empty to disable mail notification
define("PUSHBULLET_API_KEY", "k4z3QsBl8v9pbmj78Am2bQeseI9IOYRi"); // empty to disable pushbullet
// end of config
define("NOTIF_URL", "https://www.kimsufi.com/fr/commande/kimsufi.xml?reference=");

/**
 *
 * Here we notify by mail if we are not in dev mode and by pushbullet notification if it's enabled and cURL is loaded.
 *
 * @param        $server
 * @param string $debug
 */
function notify($server, $debug = '')
{
	if (!DEV_MODE && MAIL != '')
		mail(MAIL, "$server available", NOTIF_URL.$server);
	else if (DEV_MODE)
		echo trim($server)." available\n";

	if (PUSHBULLET_API_KEY != '' && function_exists('curl_init')) {
		$pushContent = '{"type": "note", "title": "'.$server.' AVAILABLE", "body": "'.NOTIF_URL.$server.'"}';
		$curl = curl_init();
		curl_setopt( $curl, CURLOPT_URL, "https://api.pushbullet.com/v2/pushes" );
		curl_setopt( $curl, CURLOPT_USERPWD, PUSHBULLET_API_KEY );
		curl_setopt( $curl, CURLOPT_CUSTOMREQUEST, "POST" );
		curl_setopt( $curl, CURLOPT_HTTPHEADER, array(
			'Content-Type: application/json',
			'Content-Length: ' . strlen( $pushContent )
		) );
		curl_setopt( $curl, CURLOPT_POSTFIELDS, $pushContent );
		curl_setopt( $curl, CURLOPT_RETURNTRANSFER, true );
		curl_setopt( $curl, CURLOPT_HEADER, false );
		curl_exec( $curl );
		curl_close( $curl );
	}
}

$servers = json_decode(file_get_contents("https://ws.ovh.com/dedicated/r2/ws.dispatcher/getAvailability2"))->answer->availability;

foreach ($servers as $server)
{
	if (strstr($server->reference, "150sk") && $server->reference != "150sk21" && $server->reference != "150sk31" && $server->reference != "150sk41" && $server->reference != "150sk42")
	{
		foreach ($server->zones as $dispo)
		{
			if (DEV_MODE)
				echo $server->reference." : ".$dispo->availability."\n";
			if ($dispo->availability != "unknown" && $dispo->availability != "unavailable")
				notify($server->reference);
		}
	}
}

if (DEV_MODE)
	notify("test");
