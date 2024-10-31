<?php
/*
 * disp_posts.php
 * 
 * @author Saurabh Sahni
 * @version 1.0
 * @description Displays the apt posts in the wordpress plugin  
 * @copyright MyBlogLog / Yahoo!,  Apr 15, 2008
 * @package mybloglog
 */

$rootpath=realpath("../../../../");
require $rootpath."/wp-config.php";
require_once ABSPATH.WPINC.'/functions.php';
require_wp_db();
require_once "functions.php";
$tags= $_REQUEST['tags'];
$count=$_REQUEST['count'];
$weight_interest=$_REQUEST['weight_interest'];
$weight_recent=$_REQUEST['weight_recent'];
$age=$_REQUEST['age'];
if($age=="0")
  $age="-1";
if(!$weight_interest)
  $weight_interest=1;
if(!$weight_recent)
  $weight_recent=1;

 
if($count<1)
 $count=5;
showPosts($tags,$count,$age,$weight_interest,$weight_recent);

?>
