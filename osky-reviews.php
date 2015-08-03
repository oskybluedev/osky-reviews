<?php

/*

Plugin Name: Osky Reviews

Description: A plugin designed to manage reviews for a site

Version: 1.0

Author: OskyBlue

*/
require_once( 'wm-settings.php' );		
require_once( ABSPATH . WPINC . '/pluggable.php' );
ob_start();
$wp_rewrite = new WP_Rewrite();



function create_tables_if_not_exist()

{

global $wpdb;

$table_name = 'wp_osky_reviews_emails';

		$MSQL = "show tables like '$table_name'";



		if($wpdb->get_var($MSQL) != $table_name)

		{



		   $sql = "CREATE TABLE IF NOT EXISTS wp_osky_reviews_emails (

			  id mediumint(9) NOT NULL AUTO_INCREMENT,

			  email varchar(50),

			  firstname varchar(50),

			  lastname varchar(50),
 
              firstadded DATE,

			  status1 DATE DEFAULT '0000-00-00',			  

			  status2 DATE DEFAULT '0000-00-00',			  

			  status3 DATE DEFAULT '0000-00-00',			  

			  reply DATE DEFAULT '0000-00-00',
			  
			  lastsent DATE DEFAULT '0000-00-00',

			  PRIMARY KEY( id ),		  

			  UNIQUE (email)) ";



			require_once(ABSPATH . "wp-admin/includes/upgrade.php");



			dbDelta($sql);

        }

		

			//$MSQL = "show tables like wp_reviews_network";

		if($wpdb->get_var($MSQL) != 'wp_reviews_network')

		{



		   $sql = "CREATE TABLE IF NOT EXISTS wp_reviews_network (



			  id mediumint(9) NOT NULL AUTO_INCREMENT,



			 network varchar(50),



			 url varchar(250),

			 

			  rnm varchar(250),



			  PRIMARY KEY( id )



			) ";



			require_once(ABSPATH . "wp-admin/includes/upgrade.php");



			dbDelta($sql);

			

			

			

        }

		

		$table_name = 'wp_osky_reviews_schedule';

		$MSQL = "show tables like '$table_name'";



		if($wpdb->get_var($MSQL) != $table_name)

		{



		   $sql = "CREATE TABLE IF NOT EXISTS $table_name (



			  id mediumint(9) NOT NULL AUTO_INCREMENT,

              firstdays smallint(2),

			  firstbool smallint(2),

			  seconddays smallint(2),

			  secondbool smallint(2),

			  thirddays smallint(2), 

			  thirdbool smallint(2),

			  smsdays smallint(2), 

			  smsbool smallint(2),

			  emailone varchar(500),

			  emailtwo varchar(500),

			  emailthree varchar(500),

			  PRIMARY KEY( id )



			) ";

					require_once(ABSPATH . "wp-admin/includes/upgrade.php");



			dbDelta($sql);

        }	

}
create_tables_if_not_exist();


function or_syle_sync()

{
// Register the style 
wp_register_style( 'review-style', plugins_url( 'reviews.css', __FILE__ ));

 //enqueue the style:
wp_enqueue_style( 'review-style' );
}
add_action( 'wp_enqueue_scripts', 'or_syle_sync' );



// register post type for Reviews

function ty_post_type_init() {



$labels = array(

'name' => _x('reviews', 'Reviews'),

'singular_name' => _x('review', 'Review'),

'add_new' => _x('Add New', 'custom-post'),

'add_new_item' => __('Add New Review'),

'edit_item' => __('Edit Review'),

'new_item' => __('New Review'),

'all_items' => __('All Reviews'),

'view_item' => __('View Review'),

'search_items' => __('Search Review'),

'not_found' =>  __('No Reviews found'),

'not_found_in_trash' => __('No Reviews found in Trash'),

'parent_item_colon' => '',

'menu_name' => __('Reviews')

);

$args = array(

'labels' => $labels,

'public' => true,

'publicly_queryable' => true,

//'taxonomies' => array('category'),  

'show_ui' => true,

'show_in_menu' => true,

'menu_icon' => 'dashicons-star-half',

'query_var' => true,

'rewrite' => array( 'slug' => _x( 'Review', 'URL slug' ) ),

'capability_type' => 'post',

'has_archive' => true,

'hierarchical' => false,

'menu_position' => null,

'supports' => array( 'title', 'editor', 'author', 'thumbnail', 'excerpt', 'comments' )

);

register_post_type('reviews', $args);



}

function remove_menu_from_rs() {

  global $submenu;

  $post_type = 'reviews';

  $tax_slug = 'category';

  if (isset($submenu['edit.php?post_type='.$post_type])) {

    foreach ($submenu['edit.php?post_type='.$post_type] as $k => $sub) {

      if (false !== strpos($sub[2],$tax_slug)) {

        unset($submenu['edit.php?post_type='.$post_type][$k]);

      }

    }

  }

}

add_action('admin_menu','remove_menu_from_rs');



function create_my_taxonomies() {

    register_taxonomy(

        'reviews_stars',

        'reviews',

        array(

            'labels' => array(

                'name' => 'Reviews Stars',

                'add_new_item' => 'Add New Review Stars',

                'new_item_name' => "New Stars"

            ),

            'show_ui' => false,

            'show_tagcloud' => false,

            'hierarchical' => true

        )

    );



wp_set_object_terms( 0, 	 array(0.5,1.0,1.5,2.0,2.5,3.0,3.5,4.0,4.5,5.0), 'reviews_stars' );

//wp_set_object_terms( 0, 'one', 'reviews_stars' );

//wp_set_object_terms( 0, '1half', 'reviews_stars' );

//wp_set_object_terms( 0, 'two', 'reviews_stars' );

//wp_set_object_terms( 0, '2half', 'reviews_stars' );

//wp_set_object_terms( 0, '3half', 'reviews_stars' );

//wp_set_object_terms( 0, 'four', 'reviews_stars' );

//wp_set_object_terms( 0, '4half', 'reviews_stars' );

//wp_set_object_terms( 0, 'five', 'reviews_stars' );

//wp_set_object_terms( 0, 'three', 'reviews_stars' );



	 

}

add_action( 'init', 'create_my_taxonomies');





add_action( 'init', 'ty_post_type_init' );

 



//the_terms( $post->ID, 'reviews_stars');

//generate form code to input reviews

function form_code() {

	

?>

<link rel="stylesheet" href="<?php bloginfo('reviews.css'); ?>" type="text/css" />

<div class="wpcf7">

<form id="new_post" name="new_post" method="post" action="" class="wpcf7-form" enctype="multipart/form-data">

    <!-- post name -->

    <fieldset name="name">

        <label for="title">Review Tittle:</label>

        <input type="text" id="title" value="" tabindex="5" name="title" />

	 	<fieldset class="rating" style = "float:right;">

    <input type="radio" id="star5" name="rating" value="5.0" /><label class = "full" for="star5" title="Awesome - 5 stars"></label>

    <input type="radio" id="star4half" name="rating" value="4.5" /><label class="half" for="star4half" title="Very Good - 4.5 stars"></label>

    <input type="radio" id="star4" name="rating" value="4.0" /><label class = "full" for="star4" title="Good - 4 stars"></label>

    <input type="radio" id="star3half" name="rating" value="3.5" /><label class="half" for="star3half" title="Fine - 3.5 stars"></label>

    <input type="radio" id="star3" name="rating" value="3.0" /><label class = "full" for="star3" title="Okay - 3 stars"></label>

    <input type="radio" id="star2half" name="rating" value="2.5" /><label class="half" for="star2half" title="Poor - 2.5 stars"></label>

    <input type="radio" id="star2" name="rating" value="20" /><label class = "full" for="star2" title="Kinda Bad - 2 stars"></label>

    <input type="radio" id="star1half" name="rating" value="1.5" /><label class="half" for="star1half" title="Bad - 1.5 stars"></label>

    <input type="radio" id="star1" name="rating" value="1.0" /><label class = "full" for="star1" title="Very bad - 1 star"></label>

    <input type="radio" id="starhalf" name="rating" value="0.5" /><label class="half" for="starhalf" title="Awful - 0.5 stars"></label>

</fieldset> 

    </fieldset>



    <!-- post Content -->

    <fieldset class="content">

        <label for="description"></label>

        <textarea id="description" tabindex="15" name="description" cols="30" rows="3"></textarea>

    </fieldset>



    <fieldset class="submit">

        <input type="submit" value="Post Review" tabindex="40" id="submit" name="submit" style="float: right; " />

    </fieldset>



    <input type="hidden" name="action" value="new_post" />

<?php wp_nonce_field( 'new-post' ); ?>

</form>

</div> <!-- END WPCF7 -->

<?php

if( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) &&  $_POST['action'] == "new_post") {

 $bool = false;

	global $wpdb;
    // Do some minor form validation to make sure there is content

    if (isset ($_POST['title'])) {

        $title =  $_POST['title'];

    } else {

        echo 'Please enter the review name';

    }

    if (isset ($_POST['description'])) {

        $description = $_POST['description'];

    } else {

        echo 'Please enter some notes';

    }

	$fstn =  $_POST['fst-target'];
	$sndn =  $_POST['scd-target'];



//Use 'tax_input' instead of 'post_category' 



  //  $tags = $_POST['post_tags'];

	

    // ADD THE FORM INPUT TO $new_post ARRAY

    $new_post = array(

    'post_title'    =>   $title,

    'post_content'  =>   $description,

    //'post_category' =>   array($_POST['cat']),  

    //'tags_input'    =>   array($tags),

    'post_status'   =>   'publish',

    'post_type'     =>   'reviews' ,

    'tax_input' => array( 'reviews_stars' => $_POST['rating'] )

	);

    //SAVE THE POST
	$social_table_name = 'wp_reviews_network';

	$the_url = $wpdb->get_var("SELECT url FROM " . $social_table_name . " WHERE id = '1'", 0, 0);

	$the_network = $wpdb->get_var("SELECT network FROM " . $social_table_name . " WHERE id = '1'", 0, 0);
get_the_title(get_the_ID()); 
$pieces = explode ( ' ' , get_the_title(get_the_ID()));
$pieces[2];
	$bool = true;
$name = $wpdb->get_var("SELECT firstname FROM $table_name where ");	
$first_name = $result->firstname;
$last_name = $result->lastname;
$admin_email = get_option( 'admin_email' );
$subject = 'A Review Has Been Left';
$message = $pieces[2] . ' has left you a review of ' . $_POST['rating'] . ' stars.';
	wp_mail( $admin_email, $subject, $message);
	
	$wpdb->update($table_name, array('reply' => 1), array( 'email' => $email  )); 

    $pid = wp_insert_post($new_post);

 

wp_set_object_terms($pid,array( $_POST['rating']),'reviews_stars');

    //SET UP TAGS

  //  wp_set_post_tags($pid, $_POST['post_tags']);

 

    //REDIRECT TO THE NEW POST ON SAVE


$results = $wpdb->get_results("SELECT email FROM $table_name");
 foreach ($results as $result) 
 {


	 $email = $result->email;

	 $page = get_page_by_title('Thank You '. $first_name);

	

}
do_action('wp_insert_post', 'wp_insert_post');
wp_delete_post( get_the_ID() );
$table_name = 'wp_osky_reviews_emails';

 


  

?>

<div id="dom-target" style="display: none;">

    <?php 

     

        echo htmlspecialchars($the_url); 

    ?>

</div>

<div id="tom-target" style="display: none;">

    <?php 

     

        echo htmlspecialchars($the_network); 

    ?>

</div>

<div id="home-target" style="display: none;">

    <?php 

     

        echo htmlspecialchars(home_url()); 

    ?>

</div>

<div id="rat-target" style="display: none;">

    <?php 

               

        echo htmlspecialchars($_POST['rating']); 

    ?>

</div>



<script type="text/javascript">;

var div = document.getElementById("dom-target");

var diz = document.getElementById("tom-target");

var home = document.getElementById("home-target");

var rate = document.getElementById("rat-target");

if(rate.textContent==5)

{

var r = confirm("Thank you, would you like to review us on " + diz.textContent + "?");	

}

else{ 

var r = confirm("Thank you for your feedback.");

    }

if(r==true&&rate.textContent==5)

window.location.replace(div.textContent);

else

{window.location.replace(home.textContent);}

</script>

<?php





}

}



function add_reviews()

{

$args = array('post_type'=> 'Reviews');

		query_posts($args);

 





//pagination

if (have_posts()) :

$paged = ( get_query_var('paged') ) ? get_query_var('paged') : 1;

query_posts( array( 'post_type' => 'reviews', 'posts_per_page' => 10, 'ignore_sticky_posts' => 1, 'paged' => $paged ) );







	while (have_posts()) : the_post();

//echo the reviews	

$post = get_post();

$taxonomy = strip_tags( get_the_term_list($post->ID, 'reviews_stars') );

$category = get_the_category(); 

?>



    <h3><?php echo the_title(); echo " "; ?>

	

    <?php switch($taxonomy)

	{

case '0.5':

	 ?>

	<img src="http://www.pixempire.com/images/preview/mini-half-star-icon.jpg" height="20" width="20">

<?php break;



case '1.0':

	 ?>

	<img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

<?php break;





case '1.5':

	 ?>

	<img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	<img src="http://www.pixempire.com/images/preview/mini-half-star-icon.jpg" height="20" width="20">

	

<?php break;



case '2.0':

	 ?>

	<img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	<img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

<?php break;



case '2.5':

	 ?>

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	<img src="http://www.pixempire.com/images/preview/mini-half-star-icon.jpg" height="20" width="20">

<?php break;



case '3.0':

	 ?>

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	

<?php break;



case '3.5':

	 ?>

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	<img src="http://www.pixempire.com/images/preview/mini-half-star-icon.jpg" height="20" width="20">

<?php break; 



case '4.0':

	 ?>

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	

<?php break; 



case '4.5':

	 ?>

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	 <img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	<img src="http://www.pixempire.com/images/preview/mini-half-star-icon.jpg" height="20" width="20">

<?php break; 



case '5.0':

	 ?>

	<img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	<img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	<img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	<img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

	<img src="https://pbs.twimg.com/profile_images/2173516694/BlackStar_400x400.PNG" height="20" width="20">

<?php break;	

	

	}	

	?>



</h3>

	<h5>

    <span style="font-weight:normal;"><i><?php echo the_content(); ?></i></span>

	</h5>

   <br>

<?php

endwhile; 

?>

<div class="nav-next alignright"><?php previous_posts_link( 'Newer Reviews' ); ?></div>

<div class="nav-previous alignleft"><?php next_posts_link( 'Older Reviews' ); ?></div>



<?php

else : ?>

<p><?php _e('Sorry, no posts matched your criteria.'); ?></p>

<?php endif;



wp_reset_query();

}

add_action('pre_get_post','add_reviews');



// create custom plugin settings menu

//add_action('admin_menu', 'CSV_create_menu');



function CSV_create_menu() {



	//create new top-level menu

	//add_menu_page('Email Settings', 'Email Settings', 'administrator', __FILE__, 'Upload_CSV_page',$icon_url='dashicons-share');

	//add_options_page('My Options', 'My Plugin', 'manage_options', 'mt-top-level-handle', 'my_plugin_page');

    

	 add_menu_page('Email Settings', 'Email Settings', 'administrator', __FILE__, "Upload_CSV_page");

    add_submenu_page(__FILE__, "Status", "Status", 'administrator', "my-submenu-slug", "status_page");



	//call register settings function

	//add_action( 'admin_init', 'register_mysettings' );

}



function or_mailtoall($datebool1, $datebool2, $datebool3, $jbo){

global $wpdb;
$emailone = $wpdb->get_var("SELECT emailone FROM wp_osky_reviews_schedule" , 0, 0);														
$emailtwo = $wpdb->get_var("SELECT emailtwo FROM wp_osky_reviews_schedule" , 0, 0);
$emailthree = $wpdb->get_var("SELECT emailthree FROM wp_osky_reviews_schedule" , 0, 0);
$days1b = $wpdb->get_var("SELECT firstbool FROM wp_osky_reviews_schedule" , 0, 0);
$days2b = $wpdb->get_var("SELECT secondbool FROM wp_osky_reviews_schedule" , 0, 0);
$days3b = $wpdb->get_var("SELECT thirdbool FROM wp_osky_reviews_schedule" , 0, 0);
$rvn = $wpdb->get_var("SELECT rnm FROM wp_reviews_network" , 0, 0);
$table_name = 'wp_osky_reviews_emails';
$var = false;
$results = $wpdb->get_results("SELECT email FROM $table_name where reply !=1 AND status3!=1");
$email= $jbo;
$first_name  = $wpdb->get_var("SELECT firstname FROM wp_osky_reviews_emails where email = '$email'");
$last_name  = $wpdb->get_var("SELECT lastname FROM wp_osky_reviews_emails where email = '$email'");
//$datebool1 . ' ' . $datebool2 . ' ' . $datebool3 .  ' ' . $jbo . '<br>';
//$days1b . ' ' . $days2b . ' ' . $days3b . '<br>';
    $subject = 'Please Review Us';

    $social_table_name = 'wp_reviews_network';

    $linkurl = get_home_url().'/thank-you-'. str_replace ( '@' , '' , '-' . $first_name);

  

    $emailonec = str_replace ( '%name%' , $first_name , $emailone);

	$emailonec =  str_replace('%linkurl%' , $linkurl . ' ' , $emailonec);    

	$emailtwoc = str_replace ( '%name%' , $first_name , $emailtwo);

	$emailtwoc =str_replace('%linkurl%' , $linkurl . ' ', $emailtwoc);  

	$emailthreec =str_replace ( '%name%' , $first_name , $emailthree);

	$emailthreec =str_replace('%linkurl%' , $linkurl . ' ' , $emailthreec);   

 if(get_page_by_title('Thank You '. $first_name)!=null)

 {   

  if($wpdb->get_var( "SELECT status1 FROM $table_name where email =  '$email'" )=='0000-00-00'||null&&$days1b==1&&$datebool1==1)

  { 

	$wpdb->update($table_name, array('status1' => date("Y-m-d" ) , 'lastsent' => date("Y-m-d" )), array( 'email' => $email  ));

    wp_mail( $email, $subject, $emailonec); 

  }

	   

  if($wpdb->get_var( "SELECT status2 FROM $table_name where email = '$email'" )=='0000-00-00'||null&&$days2b==1&&$datebool2==1)

  {

	  $wpdb->update($table_name, array('status2' => date("Y-m-d" ) , 'lastsent' => date("Y-m-d" )), array( 'email' => $email  ));

	  wp_mail( $email, $subject, $emailtwoc);  	

  }


  

   if($wpdb->get_var( "SELECT status3 FROM $table_name where email = '$email'" )=='0000-00-00'&&$days3b==1&&$datebool3==1)

  { 

	 $wpdb->update($table_name, array('status3' => date("Y-m-d" ), 'lastsent' => date("Y-m-d" )), array( 'email' => $email  ));  

	 wp_mail( $email, $subject, $emailthreec); 

  }

 }

 else

 {  

 $unique_post = array(

  'post_title'    => 'Thank You ' . $first_name,

  'post_type'     => 'page',

  'post_name'     => 'Thank You ' . $first_name,

  'post_content'  => '[review_form] <div id="fst-target" name="fst-target" style="display: none;">'.$first_name.'</div><div id="scd-target" name="scd-target"  style="display: none;">'.$last_name.'</div>' ,

  'post_status'   => 'publish',

  'comment_status' => 'closed',

  'ping_status' => 'closed',

  'post_author' => 1,

  'menu_order' => 0

);

wp_insert_post( $unique_post );
}
   





}

 



function status_page() 

{

	

	//schedule_emails ();

global $wpdb;

$row = $wpdb->get_results ( "

    SELECT * 

    FROM  wp_osky_reviews_emails" );

	

	?>

	<table>

  <tr>	


    <th>Email</th>

    <th>Name</th> 

    <th>First</th>

	<th>Seccond</th>

	<th>third</th>

	<th>Reply</th>

	<th>Last Sent</th>

  </tr>

 

  <tr>



<?

	foreach($row as $rows)

	{

    ?><td><?echo $rows->email;?></td><?

	?><td><?echo $rows->firstname . $rows->lastname ;?></td><?

    ?><td><?echo $rows->status1;?></td> <?

    ?><td><?echo $rows->status2;?></td><?

	?><td><?echo $rows->status3;?></td><?

    ?><td><?echo $rows->reply;?></td> <?

    ?><td><?echo $rows->lastsent;?></td></tr><?

    

	}

	?>

	</table>

<?

}

schedule_emails () ;

function schedule_emails () 

{
global $wpdb;
global $datebool1, $datebool2, $datebool3;	
$datebool1 = 0;
$datebool2 = 0;
$datebool3 = 0;
$shell = '0000-00-00';
$emailobj = $wpdb->get_col("SELECT email FROM wp_osky_reviews_emails");
$days1 = $wpdb->get_var("SELECT firstdays FROM wp_osky_reviews_schedule" , 0, 0);
$days1b = $wpdb->get_var("SELECT firstbool FROM wp_osky_reviews_schedule" , 0, 0);
$days2 = $wpdb->get_var("SELECT seconddays FROM wp_osky_reviews_schedule" , 0, 0);
$days2b = $wpdb->get_var("SELECT secondbool FROM wp_osky_reviews_schedule" , 0, 0);
$days3 = $wpdb->get_var("SELECT thirddays FROM wp_osky_reviews_schedule" , 0, 0);
$days3b = $wpdb->get_var("SELECT thirdbool FROM wp_osky_reviews_schedule" , 0, 0);
$emailone = $wpdb->get_var("SELECT emailone FROM wp_osky_reviews_schedule" , 0, 0);
$emailtwo = $wpdb->get_var("SELECT emailtwo FROM wp_osky_reviews_schedule" , 0, 0);
$emailthree = $wpdb->get_var("SELECT emailthree FROM wp_osky_reviews_schedule" , 0, 0);
$k = 0;
$ty;
$tm;
$td;
$day1date;
$day2date;
$day3date;
$newdate1 =0;
$newdate2 =0;
$newdate3 = 0;
$temp;

foreach($emailobj as $jbo)
{
$emaillastsent = $wpdb->get_var("SELECT lastsent FROM wp_osky_reviews_emails where email = '$jbo'" , 0, 0);
$emailfirstadded = $wpdb->get_var("SELECT firstadded FROM wp_osky_reviews_emails where email = '$jbo'" , 0, 0);

if($emaillastsent==null||$shell)
{
$dateiq = $emailfirstadded;
}
else
{
$dateiq = $emaillastsent;	
}
$date1 = date_create($dateiq);
date_add($date1,date_interval_create_from_date_string("$days1 days"));
date_format($date1,"Y-m-d") . '<br>';

$date2 = date_create($dateiq);
date_add($date2,date_interval_create_from_date_string("$days2 days"));
date_format($date2,"Y-m-d") . '<br>';

$date3 = date_create($dateiq);
date_add($date3,date_interval_create_from_date_string("$days3 days"));
date_format($date3,"Y-m-d") . '<br>';

if($dateiq==date_format($date1,"Y-m-d"))
$datebool1 = 1;

if($dateiq==date_format($date2,"Y-m-d"))
$datebool2 = 1;

if($dateiq==date_format($date3,"Y-m-d"))
$datebool3 = 1;
	
	or_mailtoall($datebool1, $datebool2, $datebool3, $jbo);
}

}


function fm_shortcode() {form_code();}

function pst_shortcode() {add_reviews();}

add_shortcode( 'review_form', 'fm_shortcode' );

add_shortcode( 'review_post', 'pst_shortcode' );



get_option( 'my_option_name');

$my_page = create_settings_page(



  'my_page_id',

  __( 'My Page' ),

  array(

    'title' => __( 'My Menu' )

  ),

  array(

    'my_setting_id' => array(

      'title'       => __( 'My Setting' ),

      'description' => __( 'This is my section description.' ),

      'fields'      => array(

        'my_option_name' => array(

          'label'        => __( 'My Option' ),

          'description'  => __( 'This is my field description.' )

        )

      )

    )

  )

);

// Access the values

$my_value = get_setting( 'my_setting_id', 'my_option_name' );

$my_top_page = create_settings_page(

  'my_top_level_page',

  __( 'Settings' ),

  array(

    'parent'   => 'edit.php?post_type=reviews',

    'title'    => __( 'Reviews Settings' ),

    'icon_url' => 'dashicons-admin-generic',

    'position' => '63.3'

  ),

  array(

    'my_standard_section' => array(

      'title'  => __( 'Add Emails' ),

      'description' => __( 'Add emails here to send request for reviews.' ),

      'fields' => array(

       /* 'my_input'    => array(

          'label' => __( 'Add Email' ),

		  'sanitise' => true

        ),*/

        'my_checkbox' => array(

          'type'  => 'file',

          'label' => '',

		  'description' =>  'How create a csv file <a> http://www.computerhope.com/issues/ch001356.htm </a>' 

        )

        


      )
    )

  ),

  array(

    'tabs'        => true,

    'submit'      => __( 'Submit' ),

    'reset'       => __( 'reset' ),

    'description' => __( 'Customize your review settings' ),

    'updated'     => __( 'success message !')

  )

);

// And a sub-page


add_submenu_page( 'edit.php?post_type=reviews', 'Status', 'Status', '7', 'status', 'status_page' );
$my_top_page->apply_settings( array(

  'my_formatted_section' => array(

    'title'  => __( 'Social Networks' ),

    'fields' => array(

      'my_select' => array(

        'type'    => 'select',

        'label'   => __( 'Network' ),

        'options' => array(

          '1'   => __( 'Google+'),

          '2'   => __( 'Yelp'),

          '3' => __( 'Yahoo'),

		  '4' => __( 'Bing')

        )

      ),

      'my_url'    => array(

        'type'  => 'url',

        'label' => __( 'URL' )

      ),

      'my_number' => array(

        'type'  => 'number',

        'label' => __( 'Refer Minimum Stars' )

      )
    )

  ),

  'my_multi_section'    => array(

    'title'  => __( 'Schedule' ),

    'fields' => array(

     

      'my_multi'  => array(

        'type'    => 'multi',

        'label'   => __( 'Schedule' ),

        'options' => array(

          'one'   => __( 'First Email'),

          'two'   => __( 'Second Email'),

          'three' => __( 'Third Email')

        )

      ),

	  'my_number1' => array(

        'type'  => 'number',

        'label' => __( 'Days to wait for 1st email' )

		

      ),

	  'my_number2' => array(

        'type'  => 'number',

        'label' => __( 'Days to wait for 2nd email' )

      ),

	  'my_number3' => array(

        'type'  => 'number',

        'label' => __( 'Days to wait for 3rd email' )

      ),

	  'my_textarea1' => array(

          'type'  => 'textarea',

          'label' => __( 'First Email' ),

		  'description' => 'Supported tags %name% %linkurl% '

        ),

		'my_textarea2' => array(

          'type'  => 'textarea',

          'label' => __( 'Seccond Email' ),

		  'description' => 'Supported tags %name% %linkurl% '

        ),

		'my_textarea3' => array(

          'type'  => 'textarea',

          'label' => __( 'Third Email' ),

		  'description' => 'Supported tags %name% %linkurl% '

        ),

		'my_email'    => array(

        'type'  => 'email',

        'label' => __( 'Send test email to' )

      ),
	  	'stm_action' => array(

        'type'        => 'action',

        'label'       => __( 'Send Test Email' ),

        'description' => __( ''),

        'action'      => 'send_test_email'

      )

		

    )

  )

) );





function do_my_action() {

	

	//echo 'test';

  // If error

  wp_send_json_error( __( 'Error !' ) );

  // If success

  wp_send_json_success( __( 'Success !' ) );

  // If the page needs to reload

  wp_send_json_success( array(

    'reload'  => true,

    'message' => __( 'This message is only displayed if "reload" => false.' )

  ) );

}

function send_test_email ()

{ 

global $wpdb;

$linkurl = 'http://brock.oskydev.com/2145-2/';
$emailsender = $wpdb->get_var("SELECT email FROM wp_osky_reviews_emails where id = 1" , 0, 0);

$emailone = $wpdb->get_var("SELECT emailone FROM wp_osky_reviews_schedule" , 0, 0);

$emailonec = str_replace ( '%name%' , 'clinetname' , $emailone);

$emailonec =  str_replace('%linkurl%' , $linkurl . ' ' , $emailonec);   



$subject = 'test';

wp_mail( $emailsender, $subject, $emailonec); 

echo 'Sent Successfully';  

}

	

function do_my_sanitation( $input, $name ) {

  return sanitize_text_field( strtoupper( $input ) );

}

add_action( 'my_top_level_page_settings_updated', 'do_my_page_callback' );

function do_my_page_callback() {

  // All settings of my_top_level_page have been updated.

    //email page option

    $email_fruit = get_option('my_standard_section');

	$email_fruit=implode($email_fruit,"");

	if( strpos($email_fruit,'@') !== false && strpos($email_fruit,'.') !== false) {

        // valid address

		//print_r($email_fruit);

		str_replace ( ',' , '', $email_fruit);
		global $wpdb;
$the_email = $wpdb->get_var("SELECT email FROM " . 'wp_osky_reviews_emails' . " WHERE email = '$email_fruit'", 0, 0);
		

		    $arguments = array(

				'email' => $email_fruit ,
								

				'status1' => '0000-00-00',

			    'status2' => '0000-00-00',

			    'status3'  => '0000-00-00',

			    'reply'  => '0000-00-00',

			    'lastsent'  => '0000-00-00');
				
if($the_email!=$email_fruit)
	$wpdb->insert( 'wp_osky_reviews_emails', $arguments);

    }

    else {

        // invalid address

		

		//print_r('Error, please check input!');

    }

	

	//social page option

	$net_fruit = get_option('my_formatted_section');

	$net_fruit=implode($net_fruit," ");

	$swi = substr($net_fruit, 0, 1);

	//print_r($net_fruit);

	//$swi = substr($net_fruit, 0, 1);

    $tok = strtok($net_fruit, ' ');

	$count = 1;
$rnm = 0;
	while ($tok !== false) {

		if ($count==1)

			$network =$tok;

		if ($count==2)

			$url =$tok;

		if ($count==3)

			$rnm =$tok;

  

    $tok = strtok(' ');

	$count++;

}

switch($network)

{

	case 1 :

	$network = 'Google+';

	break;

	

	case 2 :

	$network = 'Yelp';

	break;

	

	case 3 :

	$network = 'Yahoo';

	break;

	

	case 4 :

	$network = 'Bing';

	break;

}

$et;

$e3 = 0;
$get = 0;
global $wpdb;

		    $args = array(

			 'id' => 1,

			 'network' => $network, 

		     'url' => $url,

		     'rnm' => $rnm);

			$wpdb->replace( 'wp_reviews_network', $args);

			

	$sch_fruit = get_option('my_multi_section');

	$sch_fruit=implode($sch_fruit,"|/\|");

	//echo $sch_fruit;

	    $tok = strtok($sch_fruit, '|/\|');
$count = 1;
if($tok[0]!='{')
	{
	$count++;	
	}
	

	while ($tok !== false) {
		//echo $tok . '<br>';

	
		if ($count==1)

			$get =$tok;

		if ($count==2)

			$f =$tok;

		if ($count==3)

			$s =$tok;

		if ($count==4)

			$t =$tok;

        if ($count==5)

			$e1 =$tok;

		if ($count==6)

			$e2 =$tok;

		if ($count==7)

			$e3 =$tok;

		if ($count==7)

			$et =$tok;

    $tok = strtok('|/\|');

	



	$count++;

} 

//echo $et;

//echo $get;

$f;

$s;

$t;

$pos1 = strpos($get, "one\":");

$pos2 = strpos($get, "two\":" );

$pos3 = strpos($get, "three\":" );

$whatIWant1 = substr($get, $pos1+5 ,1); 

$whatIWant2 = substr($get, $pos2+5 ,1);

$whatIWant3 = substr($get, $pos3+7 ,1); 

		   

			$wpdb->replace( 'wp_osky_reviews_schedule',  $arg = array(

			'id' => 1,

			'firstdays' => $f, 

		    'firstbool' => $whatIWant1,

			'seconddays' => $s, 

		    'secondbool' => $whatIWant2,

			'thirddays' => $t, 

		    'thirdbool' => $whatIWant3, 

		    'smsdays' => '0',

		    'smsbool' => '0',

			'emailone' => $e1,

			'emailtwo' => $e2,

			'emailthree' => $e3));



//$wpdb->update('wp_osky_reviews_emails', 'email' => $email, where"" id = 1  );

//schedule_emails ();

}

add_action( 'update_option_custom_fields_section', 'do_my_section_callback' );

function do_my_section_callback() {

  $my_custom_options = get_setting( 'my_custom_section' );



  // All options of custom_fields_section have been updated.

}

$my_page->add_notice( __( 'My info message.') );

$my_page->add_notice( __( 'Your Settings have Been Saved'), 'updated' );

$my_page->add_notice( __( 'Error, please check input!'), 'warning' );

$my_page->add_notice( __( 'Fatal Error'), 'error' );

//schedule_emails (); 




add_action( 'wp', 'oskyreviews_setup_schedule_email' );
/**
 * On an early action hook, check if the hook is scheduled - if not, schedule it.
 */
function oskyreviews_setup_schedule_email() {
	if ( ! wp_next_scheduled( 'oskyreviews_daily_email' ) ) {
		wp_schedule_event( time(), 'daily', 'oskyreviews_daily_email');
	}
}


add_action( 'oskyreviews_daily_email', 'oskyreviews_daily_email_generate' );
/**
 * On the scheduled action hook, run a function.
 */
function oskyreviews_daily_email_generate() {
	// do something everyday
	schedule_emails (); 
}

function or_upload_csv ()
{
	?>
  <div class="wrap">

<?php

if ( isset( $_POST['ocb_member_data'] ) && wp_verify_nonce( $_POST['ocb_member_data'], 'import_member_data' ) && isset($_POST['insert_members']) ) {
	global $wpdb;	
	$uploadsroot = wp_upload_dir();				
	if(!is_dir($uploadsroot["basedir"]."/ocb")) mkdir($uploadsroot["basedir"]."/ocb", 0775);
	$filename_data_items = time().'_'.$_FILES['data_membercsv_file']['name'];	
	$upload_csv_name	= $uploadsroot["basedir"]."/ocb/".$filename_data_items;
	$upload_csv_url_name	= $uploadsroot["baseurl"]."/ocb/".$filename_data_items;	
	
	if(move_uploaded_file($_FILES['data_membercsv_file']['tmp_name'], $upload_csv_name)){		
		$csvfile = fopen($upload_csv_name, 'r');
		$theData = fgets($csvfile);
		$i = 0;
		while (!feof($csvfile)) {
			$csv_data[] = fgets($csvfile, 1024);
			$csv_array = explode(",", $csv_data[$i]);
			
			$insert_csv = array();
			$first_name = ''; $last_name = ''; $email_address = '';
			
			$first_name = $csv_array[0];
			$last_name = $csv_array[1];
			$email_address = $csv_array[2];
			
			$date = current_time(Y-m-d);
	

	 $wpdb->insert('wp_osky_reviews_emails', 
	 array('email' => $email_address, 
	 'firstadded' => $date,
	 'firstname' => $first_name, 
	 'lastname' => $last_name 
	));  			
			$i++;
		}
		fclose($csvfile);		
		
		echo "<div id='message' class=\"updated\"><p>Successfully Added!</p></div>";
		
	}else{ //eof move upload file
		echo "<div class='error'><p>Failed!</p></div>";
	}
	
}
?>


<h2>Import</h2>

<h3>Import Emails</h3>
<form   method="post" enctype="multipart/form-data">
<p>Select csv file: <input type="file" name="data_membercsv_file" /></p>
<p> <a href = "http://www.computerhope.com/issues/ch001356.htm"> How create a csv file </a></p>
<p><input action = 'edit.php?page=2145-2/' type="submit" name="insert_members" value="Upload CSV" /></p>
<?php wp_nonce_field('import_member_data','ocb_member_data'); ?>
</form>

</div>
<?	
}