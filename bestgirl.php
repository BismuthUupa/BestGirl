<?php
$url = 'https://anilist.co/api/auth/access_token';
$data = array('grant_type' => 'client_credentials', 'client_id' => 'youranilistcliendid', 'client_secret' => 'youranilistclientsecret');

// use key 'http' even if you send the request to https://...
$options = array(
    'http' => array(
        'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
        'method'  => 'POST',
        'content' => http_build_query($data)
    )
);
$context  = stream_context_create($options);
$result = file_get_contents($url, false, $context);
if ($result === FALSE) { /* Handle error */ }

$result = json_decode($result, true);
$access_token = $result["access_token"]; 

$year = date("Y");
$month = date("m");
if($month <= 3) $season = "winter";
if($month >3 && $month <= 6) $season = "spring";
if($month >6 && $month <= 9) $season = "summer";
if($month >9 && $month <= 12) $season = "fall";
//echo ucfirst($season)." Season, ".$year."<br>";
$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'https://anilist.co/api/browse/anime?year='.$year.'&season='.$season.'&type=tv&access_token='.$access_token
	));

$series = json_decode(curl_exec($curl), true);
$all_title_romaji = array();
$all_image_url_lge_series = array();
$all_id = array();
curl_close($curl);
foreach($series as $entry) {
	$all_title_romaji[$entry['id']] = $entry['title_romaji'];
	$all_image_url_lge_series[$entry['id']] = $entry['image_url_lge'];
	array_push($all_id, $entry['id']);
}

// print_r($all_title_romaji);
// print_r($series);
// print_r($all_id);
if(!isset($_POST['series'])) {
header('Location: http://bestgirl.animu.date');
}

foreach($_POST['series'] as $id){
$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'https://anilist.co/api/anime/'.$id.'/characters?access_token='.$access_token
	));
$characters[$id] = json_decode(curl_exec($curl), true);
}
curl_close($curl);?>
<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script>
function movePicture(elem){
	if( $(elem).parent().attr("id") != "dropzone" ){
     $(elem).appendTo('#dropzone');
	}
	else{
	var seriesid = "#" + $(elem).attr('series');
    $(elem).appendTo(seriesid); 
    }
}
</script>

</head>
<body>
	<div class="left">
<?
foreach($_POST['series'] as $id){
	$series_name = preg_replace('/[^0-9A-Za-z]/i', '', $all_title_romaji[$id]);
	echo "<div id=".$series_name." class='series'><h3>".$all_title_romaji[$id]."</h3>";
	foreach($characters[$id]['characters'] as $character){
	$image = $character['image_url_lge'];
	// if($image === "https://cdn.anilist.co/img/dir/character/reg/default.jpg") {
			// $curl = curl_init();
			// if(isset($character['name_last']) && isset($character['name_first'])) {
				// $curlURL = 'http://gelbooru.com/index.php?page=dapi&s=post&q=index&tags='.$character['name_last'].'_'.$character['name_first'].'+solo';
			// }
			// elseif(!isset($character['name_first'])) {
				// $curlURL = 'http://gelbooru.com/index.php?page=dapi&s=post&q=index&tags='.trim($character['name_last']).'_('.$all_title_romaji[$id].')+solo';
			// }
			// else {
			// $curlURL = 'http://gelbooru.com/index.php?page=dapi&s=post&q=index&tags='.trim($character['name_first']).'_('.$all_title_romaji[$id].')+solo';
			// }
		// curl_setopt_array($curl, array(
			// CURLOPT_RETURNTRANSFER => 1,
			// CURLOPT_URL => $curlURL
		// ));
		// $boorucurl = curl_exec($curl);
		// preg_match('/preview_url=".+?"/', $boorucurl, $booruresults);
		// $booruresults = str_replace('"', '', $booruresults[0]);
		// $booruresults = str_replace('preview_url=', '', $booruresults);
		// curl_close($curl);
		// $image = $booruresults;
	
	echo "<div><img series='".$series_name."' class='character' onclick='movePicture(this)' title='".$character['name_last']." ".$character['name_first']."' src='".$image."'></div>";
	}	
echo "</div><hr style='clear:both;'>";
}



?>
		
	</div>
	<div class="right">
		<div id="dropzone">
		
		</div>
	</div>
</body>
</html>