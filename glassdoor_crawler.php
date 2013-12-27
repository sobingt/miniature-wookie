<?php
include_once('crawler.php');
$company = $_REQUEST['company'];
$counter = $_REQUEST['counter'];
$counter = str_replace(' ', '_', $counter);
$counter = str_replace('.', '_', $counter);
$target_url="http://www.glassdoor.com/GD/Reviews/company-reviews.htm?clickSource=searchBtn&typedKeyword=&sc.keyword=".urlencode($company);
$html = new crawler();
$html->load_file($target_url);
$score = $html->find('.gdRatingValueBar span.notranslate');
$rating = $html->find('span.gdRatingDesc');
//echo "$score[0] and $rating[0]";
if (isset($rating[0])) {
	$rate =$rating[0]->plaintext;
	$rate =str_replace(array("\t", "\n"), "", $rate);
}
else {
	$rate = "Rating Not Available";
}
if (isset($score[0])) {
	$scoreText = $score[0]->plaintext;
}
else {
	$scoreText = 0;
}
$json = array();
$json[]= array(
       'name' => $company,
       'score' => $scoreText,
       'rating' => $rate,
       'counterName' => $counter
    );
echo json_encode($json);

?>