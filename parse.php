<?php
// Max Base
// https://github.com/BaseMax/crawler-news-currency-gold-coins/new/main

require "NetPHP.php";

require "_core.php";

$page=1;
$pageTotal=500;
$pageLink="https://www.eghtesadonline.com/newsstudios/archive/?curp=1&categories=7&order=order_time&page=1&curps=1";
while($page<=$pageTotal) {
	print "\nPage $page\n";
	$input=_get($pageLink);
	// print_r($input);
	preg_match_all('/<h3 itemprop="headLine">(\s*|)<a class="fnb fn14 clr04" href="\/بخش-%D8%B7%D9%84%D8%A7-%D8%A7%D8%B1%D8%B2-7\/(?<id>[0-9]+)-(?<slug>[^\"]+)"([^\>]+|)>(\s*|)(?<title>[^\<]+)(\s*|)<\/a>/i', $input[0], $matches);
	// print_r($matches);
	foreach($matches["id"] as $i=>$id) {
		$slug=$matches["slug"][$i];
		$title=$matches["title"][$i];
		parse_post("https://www.eghtesadonline.com/%D8%A8%D8%AE%D8%B4-%D8%B7%D9%84%D8%A7-%D8%A7%D8%B1%D8%B2-7/$id-$slug/", $id, $slug, $title);
	}
	preg_match('/href="\?(?<next>[^\"]+)">(\s*|)بعدی(\s*|)<\/a>/i', $input[0], $next);
	if(isset($next["next"])) {
		$next=$next["next"];
		$next="https://www.eghtesadonline.com/newsstudios/archive/?".$next;
		$pageLink=$next;
	}
	$page++;
}

function parse_post($link, $id, $slug, $title) {
	print $link."\n";
	global $db;
	if($db->count("news", ["source_id"=>$id])>0) {
		return;
	}
	$input=_get($link);
	// print_r($input);

	preg_match('/<h2 class="fn14 clr10 pt8">(\s*|)(?<subtitle>[^\<]+)(\s*|)<\/h2>/i', $input[0], $subtitle);
	if(isset($subtitle["subtitle"])) {
		$subtitle=$subtitle["subtitle"];
	}
	else {
		$subtitle=null;
	}

	preg_match('/<img class="w75 mauto block border10" src="(?<image>[^\"]+)"/i', $input[0], $image);
	if(isset($image["image"])) {
		$image=$image["image"];
	}
	else {
		$image=null;
	}

	preg_match('/<p class="fn16 clr04 bg11 box-border05 pr16 pl16"([^\>]+|)>(\s*|)(?<subtext>.*?)(\s*|)<\/p>/i', $input[0], $subtext);
	if(isset($subtext["subtext"])) {
		$subtext=$subtext["subtext"];
	}
	else {
		$subtext=null;
	}

	# preg_match('/<\!\-\- news-content \-\->(?<text>.*?)<\!\-\- news-content \-\->/i', $input[0], $text);
	// print_r($text);
	preg_match('/"description":(\s*|)"(?<text>[^\"]+)"/i', $input[0], $text);
	if(isset($text["text"])) {
		$text=$text["text"];
		$text=html_entity_decode($text);
	}
	else {
		$text=null;
	}

	preg_match('/>(\s*|)(?<date>[^\<]+)(\s*|)<\/time>(\s*|)<\/div>(\s*|)<\!-- date-news -->/i', $input[0], $date);
	$date=$date["date"];

	$video=null;
	if(preg_match('/"@type":(\s*|)"VideoObject"/i', $input[0])) {

		preg_match('/"contentUrl":(\s*|)"(\s*|)(?<video>[^\"]+)"/i', $input[0], $video);
		// print_r($video);
		$video=$video["video"];

		preg_match('/"thumbnailUrl":(\s*|)"(\s*|)(?<image>[^\"]+)"/i', $input[0], $image);
		// print_r($image);
		$image=$image["image"];
	}

	// print_r($id);
	// print_r($title);
	// print_r($subtitle);
	// print_r($image);
	// print_r($subtext);
	// print_r($text);
	// print_r($date);
	// print $link."\n";
	// exit();
	// return;

	$values=[
		"title"=>$title,
		"subtitle"=>$subtitle,
		"slug"=>$slug,
		"source"=>1,
		"source_id"=>$id,
		"link"=>$link,
		"date"=>$date,
		"image"=>$image,
		"subtext"=>$subtext,
		"text"=>$text,
		"video"=>$video,
	];

	if($db->count("news", ["source_id"=>$id])>0) {
		$db->update("news", ["source_id"=>$id], $values);
	}
	else {
		$db->insert("news", $values);
	}
}

function _get($link) {
	return get($link);
	exec("curl '$link' > output.html");
	return file_get_contents("output.html");
}
