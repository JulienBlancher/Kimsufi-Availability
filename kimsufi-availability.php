<?php

define("DEV", true);
define("MAIL", "ju.blancher@gmail.com");

function notify($server, $debug = '')
{
	if (!DEV)
		mail(MAIL, "$server available", "https://www.kimsufi.com/fr/index.xml\n\n$debug");
	else
		echo trim($server)." available!!\n";
	exec('curl -u k4z3QsBl8v9pbmj78Am2bQeseI9IOYRi: -X POST https://api.pushbullet.com/v2/pushes --header \'Content-Type: application/json\' --data-binary \'{"type": "note", "title": "'.$server.' AVAILABLE", "body": "Last '. floor($since / 60) .' minutes\n https://www.kimsufi.com/fr/index.xml"}\'');
}

$servers = json_decode(file_get_contents("https://ws.ovh.com/dedicated/r2/ws.dispatcher/getAvailability2"))->answer->availability;

foreach ($servers as $server)
{
	if (strstr($server->reference, "150sk") && $server->reference != "150sk21" && $server->reference != "150sk41" && $server->reference != "150sk42")
	{
		foreach ($server->zones as $dispo)
		{
			if ($dispo->availability != "unknown" && $dispo->availability != "unavailable")
				notify($server->reference);
		}
	}
}

if (DEV)
	notify("test");