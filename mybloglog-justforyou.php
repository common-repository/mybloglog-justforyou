<?php
/*
Plugin Name: MyBlogLog: Just for you
Plugin URI: http://www.mybloglog.com/
Description: Displays a list of posts related to visitor interests as found from MyBlogLog.
Author: MyBlogLog Team
Version: 1.0.1
Author URI: http://www.mybloglog.com
*/

 require "includes/functions.php";

 $mbl_url = "http://www.mybloglog.com/buzz/plugins/community.php";

// This gets called at the plugins_loaded action
 function widget_mybloglog_justforyou_init() {

        // Check for the required API functions
        if ( !function_exists('register_sidebar_widget') || !function_exists('register_widget_control') )
                return;

        // This saves options and prints the widget's config form.
        function widget_mybloglog_justforyou_control() {
        	    global $mbl_url;
        	
                $options = $newoptions = get_option('widget_mybloglog_justforyou');

                if ( $_POST['mybloglog_justforyou-submit'] ) {
                    $community_name=trim(stripslashes($_POST['mybloglog-community_name']));
                    $newoptions['mybloglog-community_name']= $community_name;
                    $count=trim(stripslashes($_POST['mybloglog-posts_like_count']));
                    $newoptions['mybloglog-posts_like_count']=$count;
                    $title=trim(stripslashes($_POST['mybloglog-posts_like_title']));
                    $newoptions['mybloglog-posts_like_title']=$title;
                    $age=trim(stripslashes($_POST['mybloglog-posts_like_age']));
                    $newoptions['mybloglog-posts_like_age']=$age;
                    
                    $weight_recent=trim(stripslashes($_POST['mybloglog-posts_weight_recent']));
                    $newoptions['mybloglog-posts_weight_recent']=$weight_recent;
                    $weight_interest=trim(stripslashes($_POST['mybloglog-posts_weight_interest']));
                    $newoptions['mybloglog-posts_weight_interest']=$weight_interest;
                }
                if ( $options != $newoptions ) {
                        $options = $newoptions;
                        update_option('widget_mybloglog_justforyou', $options);
                }
                $select[0]="";
                $select[92]="";
                $select[183]="";
                $select[365]="";
                $days=wp_specialchars($options['mybloglog-posts_like_age'], true);
                $select[$days]="selected = 'selected'";
        ?>
        <div>
        <p style="text-align:left">If your reader has a MyBlogLog account, the plug-in uses tags the user has added to their MyBlogLog profile to suggest posts from your archives matched to their interests. For non-MyBlogLog users, the plugin simply uses tags of your other recent visitors to recommend posts. The weighting for a user's interests ranges from 10, a direct match between your posts' tags/categories and their MyBlogLog tags to 1, a loose coupling. 
         </p>         
        <br><br>
 <?php
        ?>
        
        <p>
                <label for="mybloglog-posts_like_title">Title: <br> <input type="text"  name="mybloglog-posts_like_title" id="mybloglog-posts_like_title" value="<?php echo wp_specialchars($options['mybloglog-posts_like_title'],true); ?>"/></label>
        </p>

        <p>
                <label for="widget_mybloglog_justforyou-community_name">MyBlogLog Community (URL display) Name: <small>http://www.mybloglog.com/buzz/community/</small><input type="text"  name="mybloglog-community_name" id="widget_mybloglog_justforyou-community_name" value="<?php echo wp_specialchars($options['mybloglog-community_name'],true); ?>" style='float:left;'/></label>
                <br/>
                <span id='mybloglog-posts_like_community_link'>
         	   <?php
         		if ($options['mybloglog-community_name']) {
                    ?>
         	      	 <a href="http://www.mybloglog.com/buzz/community/<?php echo $options['mybloglog-community_name'];?>" target="_new" >View</a>
         	      	<?php
		          }
		          else
		          {
		          	?>

		          	  <script type="text/javascript">
function mbl_e(id){
    if(document.getElementById != null){
        return document.getElementById(id);
    }
    
    if(document.all != null){
        return document.all[id];
    }

    if(document.layers != null){
        return document.layers[id];
    }
    return null;
}

					function mbl_load_likeposts_plugin(cname, site_id) {
	       			    mbl_e('widget_mybloglog_justforyou-community_name').value = cname;
                        if(cname)
                                mbl_e("mybloglog-posts_like_community_link").innerHTML = '<a href=\"http://www.mybloglog.com/buzz/community/'+cname+'/\" target=\"_new\">View</a>';
	       			    
      				}


                
                   </script>


    	           <script type="text/javascript" src='<?php echo $mbl_url."?gm=3&url=";bloginfo('url');?>'>
                   </script>
                    <?php		          	
		          }
                ?>
         	</span> 
                

        </p>
        
        <p>
				<label for="mybloglog-posts_like_count">Number of posts to show: <input type="text"  name="mybloglog-posts_like_count" id="mybloglog-posts_like_count" style="width: 25px; text-align: center;" value="<?php echo wp_specialchars($options['mybloglog-posts_like_count'], true); ?>"/></label>
				<br/>
				<small>(at most 15)</small>
		</p>

        <p>
        
                <label for="mybloglog-posts_like_age">Include posts updated: 
                   <select name="mybloglog-posts_like_age" id="mybloglog-posts_like_age">
                     <option  <?php echo $select[365] ?> value="365">within a year</option>
                     <option <?php echo $select[183] ?> value="183" >within the past 6 months</option>
                     <option <?php echo $select[92] ?> value="92">within the past 3 months</option>
                     <option <?php echo $select[0] ?> value="0" >anytime</option>
                   </select>
                 </label>
        </p>
        
        <p>
                <label for="mybloglog-posts_weight_recent">Weight for Recent posts: 
                  <select name="mybloglog-posts_weight_recent" id="mybloglog-posts_weight_recent">
                     <option value="0">auto</option>
                     <?php for($i=1;$i<=10;$i++) {
                             if($i==$options['mybloglog-posts_weight_recent'])
                                $select="selected = 'selected'";	
                              else
                                $select="";
                              
                     ?>                     
                     	
                     <option value="<?php echo $i; ?>" <?php echo $select ?>><?php echo $i; ?></option>
                     <?php } ?>
                   </select>
                
                </label>
				<br/>
				<small>(default auto, decided by system, a higher number shows more recent posts) </small>
        </p>
        <p>
                <label for="mybloglog-posts_weight_interest">Weight for user interests: 
                   <select name="mybloglog-posts_weight_interest" id="mybloglog-posts_weight_interest">
                     <option value="0">auto</option>
                     <?php for($i=1;$i<=10;$i++) {
                             if($i==$options['mybloglog-posts_weight_interest'])
                                $select="selected = 'selected'";
                              else
                                $select="";                                	
                     ?>                     
                     	
                     <option value="<?php echo $i; ?>" <?php echo $select ?>><?php echo $i; ?></option>
                     <?php } ?>
                   </select>
                
                </label>
				<br/>
				<small>(default auto, decided by system, a higher number shows stricter match to user's MyBlogLog tags)</small>
        </p>

        <input type="hidden" name="mybloglog_justforyou-submit" id="mybloglog_justforyou-submit" value="1" />
        </p>
        </div>
        <?php
        }

        // This prints the widget
        function widget_mybloglog_justforyou($args) {
                extract($args);
                $defaults = array();
                $options = (array) get_option('widget_mybloglog_justforyou');

                foreach ( $defaults as $key => $value )
                        if ( !isset($options[$key]) )
                                $options[$key] = $defaults[$key];

                ?>
                <?php 
                    $mbl_justforyou_community_name=$options['mybloglog-community_name'];
                    
                    $mbl_justforyou_count=$options['mybloglog-posts_like_count'];

                    $mbl_justforyou_title=$options['mybloglog-posts_like_title'];
                    if(!$mbl_justforyou_title)
                       $mbl_justforyou_title="Just for you";
                    $mbl_justforyou_age=$options['mybloglog-posts_like_age'];
                    if(!$mbl_justforyou_age)
                       $mbl_justforyou_age=365;
                    
                    $mbl_justforyou_weight_recent=$options['mybloglog-posts_weight_recent'];
                    
                    if(!$mbl_justforyou_weight_recent)
                       $mbl_justforyou_weight_recent=0;
                    
                    $mbl_justforyou_weight_interest=$options['mybloglog-posts_weight_interest'];
                    if(!$mbl_justforyou_weight_interest)
                       $mbl_justforyou_weight_interest=0;                    
                ?>
                <?php echo $before_widget; ?>
                <?php echo $before_title .$mbl_justforyou_title  . $after_title; ?>
                <script  type="text/javascript">
                var tagsXML="";
                </script>
                <script type="text/javascript" src="http://www.mybloglog.com/buzz/interesting_posts/tags_for_you.php?community_name=<?php echo $mbl_justforyou_community_name?>">
                </script>
                
                <script type="text/javascript">

                var url="<?php bloginfo('wpurl') ?>/wp-content/plugins/mybloglog-justforyou/includes/disp_posts.php?tags="+tagsXML+"&count=<?php echo $mbl_justforyou_count?>"+"&age=<?php echo $mbl_justforyou_age?>"+"&weight_recent=<?php echo $mbl_justforyou_weight_recent?>"+"&weight_interest=<?php echo $mbl_justforyou_weight_interest?>";
                document.write ("<script type='text\/javascript' src='"+url+"'><\/script>");
                </script>

                  
                <?php echo "<div align='center'>Powered by <a href='http://www.mybloglog.com'>MyBlogLog</a></div>".$after_widget;
        }

        // Tell Dynamic Sidebar about our new widget and its control
//        $widget_ops = array('classname' => 'widget_mybloglog_justforyou', 'description' => __( 'The most relevant posts on your blog per visitor' ));
         register_sidebar_widget('MyBlogLog', 'widget_mybloglog_justforyou');
         register_widget_control('MyBlogLog', 'widget_mybloglog_justforyou_control', 240, 300);
       //wp_widget_justforyou_register();
 }

 // Delay plugin execution to ensure Dynamic Sidebar has a chance to load first
 add_action('widgets_init', 'widget_mybloglog_justforyou_init');



function wp_widget_justforyou_register() {
        if ( !$options = get_option('widget_mybloglog_justforyou') )
                $options = array();
        $widget_ops = array('classname' => 'widget_mybloglog_justforyou', 'description' => __( 'The most relevant posts on your blog per visitor' ));
        $control_ops = array('width' => 240, 'height' => 300, 'id_base' => 'mybloglog_justforyou');
        $name = __('MyBlogLog');

        $id = false;
               $id = "mybloglog_justforyou"; 
                wp_register_sidebar_widget($id, $name, 'widget_mybloglog_justforyou', $widget_ops, array( 'number' => -1 ));
                wp_register_widget_control($id, $name, 'widget_mybloglog_justforyou_control', $control_ops, array( 'number' => -1 ));

}


?>
