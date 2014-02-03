<?php
/**Contributor: biplob53, hasanrang05
* Plugin Name: Kento Top Commenters
* Plugin URI: http://kentothemes.com
* Description: Show the top commenter through widget.
* Version: 1.0
* Author: KentoThemes
* Author URI: http://kentothemes.com
*License: GPLv2 or later
*License URI: http://www.gnu.org/licenses/gpl-2.0.html
 */
 
define('KENTO_TOP_COMMENTERS_PLUGIN_PATH', WP_PLUGIN_URL . '/' . plugin_basename( dirname(__FILE__) ) . '/' );

function kento_top_commenters_scripts($hook) {
        /* Register our script. */
		wp_enqueue_style( 'KENTO_TOP_COMMENTERS_STYLE', KENTO_TOP_COMMENTERS_PLUGIN_PATH.'css/style.css' );		 
        wp_enqueue_script( 'jquery');

}
add_action('wp_enqueue_scripts', 'kento_top_commenters_scripts'); 



//////////////////top_commenters widget//////////////////////////////////
wp_register_sidebar_widget(
    'top_commenters_widget',          // your unique widget id
    'Kento: Top Commenters',                 // widget name
    'top_commenters_widget_display',  // callback function to display widget
    array(                      // options
        'description' => 'Top Authors List'
    )
);

wp_register_widget_control(
	'top_commenters_widget',		// id
	'top_commenters_widget',		// name
	'top_commenters_widget_control'	// callback function
);

function top_commenters_widget_control($args=array(), $params=array()) {
    	//the form is submitted, save into database
    	if (isset($_POST['submitted'])) {
    		update_option('top_commenters_widget_title', $_POST['widgettitle']);
			update_option('top_commenters_widget_number', $_POST['number']);
			update_option('ktc_style', $_POST['ktc_style']);			 		
    	}
    	//load options
    	$widgettitle = get_option('top_commenters_widget_title');
		$number = get_option('top_commenters_widget_number');
		$ktc_style = get_option('ktc_style');
    	?>
		<br/>
    	Widget Title:<br />
    	<input type="text" class="widefat" name="widgettitle" value="<?php echo stripslashes($widgettitle); ?>" /><br/><br/>
		How Many Commenter Name to show:<br />
    	<input type="text" class="widefat" name="number" value="<?php echo stripslashes($number); ?>" /><br /><br />
        Select Your Style: <br/>
    	<label><input type="radio" name="ktc_style" value="style1" <?php if( $ktc_style == 'style1') echo 'checked'; ?> />Style 1</label>
    	<br /><br />
         <label><input type="radio" name="ktc_style" value="style2" <?php if( $ktc_style == 'style2') echo 'checked'; ?> />Style 2</label>
    	<br /><br />
         <label><input type="radio" name="ktc_style" value="style3" <?php if( $ktc_style == 'style3') echo 'checked'; ?> />Style 3</label>
    	<br /><br />
    	<input type="hidden" name="submitted" value="1" />
    	<?php
    }




function top_commenters_widget_display($args=array(), $params=array()) {
    	//load options
    	$widgettitle = get_option('top_commenters_widget_title');
		$number = get_option('top_commenters_widget_number');
		$ktc_style = get_option('ktc_style');
    	//widget output
    	echo stripslashes($args['before_widget']);
		echo '<div class="top_commenters">';
		if ($widgettitle != '') {
			echo stripslashes($args['before_title']);
			echo stripslashes($widgettitle);
			echo stripslashes($args['after_title']);
		}
    	else {
			echo "";
			}
			?>
            <ol class="top_commenters-list <?php echo $ktc_style; ?>">
<?php 
			function top_comment_authors($amount) {
global $wpdb;
$results = $wpdb->get_results('
    SELECT
    COUNT(comment_author_email) AS comments_count, comment_author_email, comment_author, comment_author_url
    FROM '.$wpdb->comments.'
    WHERE comment_author_email != "" AND comment_type = "" AND comment_approved = 1
    GROUP BY comment_author_email
    ORDER BY comments_count DESC, comment_author ASC
    LIMIT '.$amount
);

foreach($results as $result) {
    $output .= "<li>";
	$output .= "<div class='top-commenters-image'>".get_avatar($result->comment_author_email, 50)."</div>";
	$output .= "<div class='top-commenters-name' >".$result->comment_author." </div>";
	
 	$user = get_user_by( email, $result->comment_author_email );
	$userId= $user ->ID;
	$count = $wpdb->get_var('
             SELECT COUNT(comment_ID) 
             FROM ' . $wpdb->comments. ' 
             WHERE user_id = "' . $userId . '"');


	$output .= " <div class='commenters-count' > (".$count." Comments )</div>";
	$output .= "</li>";	
	
}

echo $output;
}
			
 top_comment_authors($number);	
			
			 ?>

            </ol><!--top_commenters-list -->
<?php
    	echo '</div> <!--top_commenters_widget -->';//close div.socialwidget
      echo stripslashes($args['after_widget']);
    }

//////////////////kento_top_authors widget end//////////////////////////////////