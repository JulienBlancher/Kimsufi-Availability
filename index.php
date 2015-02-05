<?php

/**
 *
 * @get      text between tags
 *
 * @param     $tag
 * @param     $html
 * @param int $strict
 *
 * @return string
 *
 */
function getTextBetweenTags($tag, $html, $strict=0)
{
	/*** a new dom object ***/
	$dom = new domDocument;

	/*** load the html into the object ***/
	if($strict==1)
	{
		$dom->loadXML($html);
	}
	else
	{
		$dom->loadHTML($html);
	}

	/*** discard white space ***/
	$dom->preserveWhiteSpace = false;

	/*** the tag by its tag name ***/
	$content = $dom->getElementsByTagname($tag);

	/*** the array to return ***/
	$out = array();
	foreach ($content as $item)
	{
		/*** add node value to the out array ***/
		$out[] = $item->nodeValue;
	}
	/*** return the results ***/
	return $out;
}

$curl_handle=curl_init();
curl_setopt($curl_handle, CURLOPT_URL,'https://www.kimsufi.com/fr/index.xml');
curl_setopt($curl_handle, CURLOPT_CONNECTTIMEOUT, 2);
curl_setopt($curl_handle, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($curl_handle, CURLOPT_USERAGENT, 'Your application name');
$content = curl_exec($curl_handle);
curl_close($curl_handle);

$table = getTextBetweenTags("table", $content);

$table_clear = trim(preg_replace('/ +/', ' ', preg_replace('/[^A-Za-z0-9eèéêë€™\-,\. ]/', ' ', urldecode(html_entity_decode(strip_tags($table[0]))))));


$ks1 = preg_match("~KS-1 (.*)~", $table_clear, $matchks1);
$ks2 = preg_match("~KS-2 [^SSD](.*) KS-1~", $table_clear, $matchks2);
$ks2ssd = preg_match("~KS-2 SSD(.*) KS-2~", $table_clear, $matchks2ssd);
$ks3 = preg_match("~KS-3 (.*) KS-2 SSD~", $table_clear, $matchks3);
$ks4 = preg_match("~KS-4 [^SSD](.*) KS-3~", $table_clear, $matchks4);
$ks5 = preg_match("~KS-5 [^SSD](.*) KS-4~", $table_clear, $matchks5);
$ks6 = preg_match("~KS-6 [^SSD](.*) KS-5~", $table_clear, $matchks6);

$ks1 = $matchks1[1];
$ks2 = $matchks2[1];
$ks2ssd = $matchks2ssd[1];
$ks3 = $matchks3[1];
$ks4 = $matchks4[1];
$ks5 = $matchks5[1];
$ks6 = $matchks6[1];

//var_dump($table_clear);
echo "<br><br>";
var_dump($ks1);
echo "<br><br>";
var_dump($ks2);
echo "<br><br>";
var_dump($ks2ssd);
echo "<br><br>";
var_dump($ks3);
echo "<br><br>";
var_dump($ks4);
echo "<br><br>";
var_dump($ks5);
echo "<br><br>";
var_dump($ks6);

if (preg_match("/En cours de réapprovisionnement/", $ks1) == true)
{
	mail("ju.blancher@gmail.com", "DISPO KIMSUFI KS1", "https://www.kimsufi.com/fr/index.xml\n2258933147KIMSUFI");
}