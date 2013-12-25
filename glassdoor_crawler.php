<?php
include_once('crawler.php');
$company= $_REQUEST['company'];
$target_url="http://www.glassdoor.com/GD/Reviews/company-reviews.htm?clickSource=searchBtn&typedKeyword=&sc.keyword=$company";
$html = new crawler();
$html->load_file($target_url);
$score = $html->find('.gdRatingValueBar span.notranslate');
$rating = $html->find('span.gdRatingDesc');
//echo "$score[0] and $rating[0]";
$rate =$rating[0]->plaintext;
$rate =str_replace(array("\t", "\n"), "", $rate);
$json = array();
$json[]= array(
       'name' => $company,
       'score' => $score[0]->plaintext,
     'rating' => $rate
    );
echo json_encode($json);

?>