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

$curl = curl_init();
	curl_setopt_array($curl, array(
		CURLOPT_RETURNTRANSFER => 1,
		CURLOPT_URL => 'https://anilist.co/api/browse/anime?year='.$year.'&season='.$season.'&type=tv&access_token='.$access_token
	));

$series = json_decode(curl_exec($curl), true);
$all_title_romaji = array();
$all_image_url_lge_series = array();
$all_avg_rating = array();
$all_id = array();
curl_close($curl);
foreach($series as $entry) {
	$all_title_romaji[$entry['id']] = $entry['title_romaji'];
	$all_image_url_lge_series[$entry['id']] = $entry['image_url_lge'];
	$all_avg_rating[$entry['id']] = $entry['average_score'];
	array_push($all_id, $entry['id']);
}

//print_r($all_title_romaji);
//print_r($series);
//print_r($all_id);
// foreach($all_id as $id){
// $curl = curl_init();
	// curl_setopt_array($curl, array(
		// CURLOPT_RETURNTRANSFER => 1,
		// CURLOPT_URL => 'https://anilist.co/api/anime/'.$id.'/characters?access_token='.$access_token
	// ));
// $characters[$id] = json_decode(curl_exec($curl), true);
// }
// curl_close($curl);

// foreach($all_id as $id){
	// echo "<div class='series'><h1>".$all_title_romaji[$id]."</h1>";
	// foreach($characters[$id]['characters'] as $character){
	// $image = $character['image_url_lge'];
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
	// }
		// echo "<div class='character'><img title='".$character['name_last']." ".$character['name_first']."' src='".$image."'></div>";
		// }
// echo "</div><hr style='clear:both;'>";
// }

?>

<html>
<head>
<link rel="stylesheet" type="text/css" href="style.css">
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
<script>
// function clickGold() {
	    // $("input:checkbox").attr('checked', $(this).is(':checked'));
// }
</script>
</head>
<body>
	<div class="left" style="align-items: center; display:flex; overflow-y: auto;">
		<div class="season">
		<img src="pickerlogo.png">
		<span style="font-size:24px;">We're currently in...</span><br>
		<? echo ucfirst($season)." Season, ".$year."";?>
		</div>
	</div>
	<div class="right">
		<div class="series-picker">
		<span>Choose which series you want to pick best girls from!</span><br>
		<span>Series with Rating >60.0 are marked golden</span><br>
		<span>Ratings provided by AniList API</span><br><br>
		<form action="bestgirl.php" method="post">
		<?
		foreach($all_id as $id){
			if($all_avg_rating[$id] < 60.0)
			echo '<input type="checkbox" name="series[]" value="'.$id.'">'.$all_title_romaji[$id].'<br>';
			else
			echo '<input type="checkbox" gold="gold" name="series[]" value="'.$id.'"><span style="color:#b99523; font-weight:bold;">'.$all_title_romaji[$id].'</span><br>';
		}
		?>
		<!--<input type="button" onclick="clickGold()" value="Select Series with Rating > 60">-->
		<input type="submit" value="Ready!">
		</form>
		</div>
	</div>
</body>
</html>