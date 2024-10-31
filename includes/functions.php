<?php
/*
 * functions.php
 * 
 * @author Saurabh Sahni
 * @version 1.0
 * @description Includes functions which are called from the wordpress plugin  
 * @copyright MyBlogLog / Yahoo!,  Apr 15, 2008
 * @package mybloglog
 */
 
 //Turn off error or warning reporting
 $test=$_REQUEST['test'];

 $test=0; //Only for developers who wish to customize this plugin: Comment this on test/dev blog setups to enable debugging on plugin. You can then debug using an argument test=1 on disp_posts.php. 

  error_reporting(0);
 //Main function which calls other functions to decode json tags, find posts and finally render in UI 
 function showPosts($tags,$count,$age,$weight_interest,$weight_recent)
 {
     global $test;
 	 $tags=urldecode($tags);
 	 $tags=str_replace("&quot;",'"',$tags);
     $tags=stripslashes($tags);
         if($test==1)
         {
           echo "Before json decode";
           echo "<pre>";
           print_r($tags);
           echo "</pre>";
         }
  if(function_exists('json_decode'))      
    $visitor_tags= json_decode($tags);
         
   if($test==1)
         {
           echo "After json decode";
           echo "<pre>";
           print_r($visitor_tags);
           echo "</pre>";
         }
 	  
 	 $posts=getBestPosts($visitor_tags,$count,$age,$weight_interest,$weight_recent);
     renderHTMLOutput($posts);
 }
 
 //Find best posts for the given tags
 function getBestPosts($tags,$count,$age,$weight_interest,$weight_recent)
 {
  	
  global $posts,$test;
  if(!$weight_interest)
    $weight_interest=1;

  if(!$weight_recent)
    $weight_recent=1;
    
  $weight_ratio=3*$weight_recent/$weight_interest;
  $res= getAllBlogPosts($age);
         if($test==1)
         {
           echo "All posts";
           echo "<pre>";
           print_r($res);
           echo "\nTags to match";
           print_r($tags);
           echo "</pre>";
           
         }

  $posts=array();
  $score=array();
  $title=array();
  foreach($res as $i=>$row)
  {
  	
  	$row=(Array)($row);
  	
	$topic=$row['topics'];
        if($test==1)
           echo "Topic: $topic before normalized <br>";

	$topic=normalize_tag($topic);
        if($test==1)
           echo "Topic: $topic after normalized <br>";
  	$id=$row['ID'];
  	$title[$id]=$row['title'];      		

  	$age=(time()-strtotime($row['post_date']))/86400;
  	if(!$score[$id])
        {
    	  $score[$id]=0;
  	      //Weigh posts in last two meeks more
      	  if((14*($weight_ratio))-$age>0)
  	         $score[$id]+=((14*($weight_ratio))-$age);
  	      
        	 $score[$id]+=20*$weight_ratio/(1+$age);

          if($test==1)
          {
           echo "Score from age";
           echo "<pre>$id $age $score[$id] \n</pre>";
          }
          
        }
  	if($tags)
  	{
          reset($tags);
     	  foreach($tags as $tag=>$val)
  	  {
               
  		if(!strcmp($topic,$tag))
  		 {  
  		 	
  		 	$score[$id]+=$val; //Add score corresponding to each tag
                        if($test==1)
                        {
                          echo "<pre>Score from tag match:";
                          echo "$id $age $score[$id] $topic \n</pre>";
                        }

  		 }
  		 
  		 if($tagMatch[$id]==0)
  		 if(!strcmp($topic,$tag))
  		 {  
  		 	$score[$id]+=$weight_ratio*$val/3; //Add score for tag match: for loose coupling
                        if($test==1)
                        {
                          echo "<pre>Score from single tag match:";
                          echo "$id $age $score[$id] $topic \n</pre>";
                        }
            $tagMatch[$id]=1;

  		 }
  		 
  	  }
  	} 
  	
  }
  
  arsort($score);

         if($test==1)
         {
           echo "Scores after sort:";
           echo "<pre>";
           print_r($score);
           echo "</pre>";

         }


//  print_r($score);
  $i=0;

  foreach($score as $row=>$sc)
  {
  	$posts[$i++]=Array("ID"=>$row,"title"=>$title[$row]);
  }
  if($count>15)
    $count=15;
  
  $posts=array_slice($posts,0,$count*3,true);
  shuffle($posts);
  $posts=array_slice($posts,0,$count,true);
  
         if($test==1)
         {
           echo "Final output:";
           echo "<pre>";
           print_r($posts);
           echo "</pre>";
         }

  

  return $posts;
 } 
 
 //Find all posts from wordpress database, currently works for ver >=2.3
 function getAllBlogPosts($age)
 {
 	        global $wpdb,$table_prefix,$test;
 	        $age_cond="";
 	        if($age>0)
 	        {
 	        	$age_cond="AND (unix_timestamp(post_date)>(unix_timestamp()-{$age}*86400))";
 	        }
 	        $query="
                        SELECT ID, post_title as title, c.slug as topics, post_date
                        FROM {$table_prefix}posts a, {$table_prefix}term_relationships b, {$table_prefix}terms c, {$table_prefix}term_taxonomy d
                        WHERE a.ID=b.object_id AND c.term_id=d.term_id AND d.term_taxonomy_id=b.term_taxonomy_id
                        AND post_status = 'publish'
                        AND post_type != 'page'  $age_cond
                        ORDER BY post_date desc limit 1000;
		    ";
		    
		    if($test==1)
		       echo $query;
 			$post_list = (array)$wpdb->get_results($query);
 			
 			
		    return $post_list; 
 }
 //Display posts in a list format
 function renderHTMLOutput($posts)
 {
    echo "document.write(\"<div><ul>\");";
    foreach($posts as $row)
    {
      echo "document.write(\"<li><a href='".get_permalink($row[ID])."'>".($row[title])."</a></li>\");\n";
    }
    echo "document.write(\"</ul></div>\");";


 ?> <?php
 }


   $normalized_tag_cache=array();
   function normalize_tag($tag) {
        $original_tag=$tag;
        if($normalized_tag_cache["$tag"])
                 return $normalized_tag_cache["$tag"];
        $tag = html_entity_decode($tag);
        $tag = preg_replace('/&#39;/', "\'", $tag);
        $tag = trim(preg_replace("/[\s!-\*\,-\/:-@[-`~+{}|]/", "", $tag));
        $tag=strtolower($tag);
        $normalized_tag_cache["$original_tag"]=$tag;
        return $tag;
    }

?>
