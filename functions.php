<?php
/**
 * @package BuddyBoss Child
 * The parent theme functions are located at /buddyboss/buddyboss-inc/theme-functions.php
 * Add your own functions in this file.
 */

/**
 * Sets up theme defaults
 *
 * @since BuddyBoss 3.0
 */
function buddyboss_child_setup()
{
  /**
   * Makes child theme available for translation.
   * Translations can be added into the /languages/ directory.
   * Read more at: http://www.buddyboss.com/tutorials/language-translations/
   */

  // Translate text from the PARENT theme.
  load_theme_textdomain( 'buddyboss', get_stylesheet_directory() . '/languages' );

  // Translate text from the CHILD theme only.
  // Change 'buddyboss' instances in all child theme files to 'buddyboss_child'.
  // load_theme_textdomain( 'buddyboss_child', get_stylesheet_directory() . '/languages' );

}
add_action( 'after_setup_theme', 'buddyboss_child_setup' );

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since BuddyBoss 3.0
 */
function buddyboss_child_scripts_styles()
{
  /**
   * Scripts and Styles loaded by the parent theme can be unloaded if needed
   * using wp_deregister_script or wp_deregister_style.
   *
   * See the WordPress Codex for more information about those functions:
   * http://codex.wordpress.org/Function_Reference/wp_deregister_script
   * http://codex.wordpress.org/Function_Reference/wp_deregister_style
   **/

  /*
   * Styles
   */
  wp_enqueue_style( 'buddyboss-child-custom', get_stylesheet_directory_uri().'/css/custom.css' );

  /*
   * Conditional styles for Ponentes and Coordinadores
   */
  $perfil  = xprofile_get_field_data( 'perfil', bp_displayed_user_id(), $multi_format = 'comma' );

  if ( $perfil == 'Ponente' ) {
    wp_enqueue_style( 'buddyboss-child-ponente', get_stylesheet_directory_uri().'/css/style-ponente.css' );

  } elseif ( $perfil == 'Coordinador' ) {
    wp_enqueue_style( 'buddyboss-child-coordinador', get_stylesheet_directory_uri().'/css/style-coordinador.css' );

  }

}
add_action( 'wp_enqueue_scripts', 'buddyboss_child_scripts_styles', 9999 );


/****************************** CUSTOM FUNCTIONS ******************************/

// Add your own custom functions here


/**
 * Add Perfil to activity
 * 
 * @return html
 */
function job_title_activity_action( $action, $activity, $r)
{
  // Get some values
  $bp_job_title = bp_get_profile_field_data( 'field=Perfil&user_id=' . bp_get_activity_user_id());
  $job_title = '</a> <span style="color:red;" class="perfil-user">(' . $bp_job_title . ')</span>';
  $action = str_replace( '</a>', $job_title, $action);

    return $action;
}
add_filter( 'bp_get_activity_action_pre_meta', 'job_title_activity_action', 1, 3 );


/**
 * Show medals for assistance number.
 * 
 * @return html
 */
function ShowAssistanceMedals()
{
  // Get some values
  $asistencias = xprofile_get_field_data( 'Asistencias a EBEs', bp_displayed_user_id() );
  $showmedals = '';

  // We iterate over the attendances to display a medal for each
  for ($i=0; $i < $asistencias; $i++) { 
    $showmedals .= '<i class="fa fa-certificate" aria-hidden="true"></i> ';
  }

  if ( $showmedals == '' ) {
    $showmedals = 'Aún no ha asistido a ninguna edición';
  }

  $output = ' <br><div class="assistance"><strong>Asistencias a ediciones de EBE: ' . $showmedals . '</strong></div>';
// $output .= ' <br><span style="color:red">logged_in: ' . bp_loggedin_user_id() . '</span> | <span style="color:orange">asistencias: ' . $asistencias . '</span>';
 
    echo $output;
}

add_action( 'bp_after_member_header', 'ShowAssistanceMedals', 10, 1 );


/**
 * Show Twitter timeline
 *
 * @return html
 */
function ShowTwitterTimeline()
{
  // Gets some values
  $twitter_user   = xprofile_get_field_data( 'Usuario de Twitter', bp_displayed_user_id() );
  $twitter_widget = xprofile_get_field_data( 'Widget ID', bp_displayed_user_id() );
  // Extract widget ID from string
  $widget_pre_id  = strstr($twitter_widget,'data-widget-id="');
  // Create a function for extrar only numbers
  function get_numerics ($str) {
    preg_match_all('/\d+/', $str, $matches);
    return $matches[0];
  }

  $widget_post_id = get_numerics( $widget_pre_id );
  $widget_id      = $widget_post_id[0];

  $output = '<a class="twitter-timeline"
            data-widget-id="' . $widget_id . '"
            href="https://twitter.com/' . $twitter_user . '"
            data-screen-name="' . $twitter_user . '">
            Tweets de @' . $twitter_user . '
            </a>';

  $output .= '<script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?\'http\':\'https\';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+"://platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");</script>';

  echo $output;
}
add_action( 'bp_after_member_header', 'ShowTwitterTimeline', 15, 1 );


/**
 * Redirect user after successful login.
 *
 * @param string $redirect_to URL to redirect to.
 * @param string $request URL the user is coming from.
 * @param object $user Logged user's data.
 * @return string
 */
function my_login_redirect( $redirect_to, $request, $user ) {
  //is there a user to check?
  if ( isset( $user->roles ) && is_array( $user->roles ) ) {
    //check for admins
    if ( in_array( 'administrator', $user->roles ) ) {
      // redirect them to the default place
      return $redirect_to;

    } else {
      return home_url();

    }

  } else {
    return $redirect_to;

  }
}
add_filter( 'login_redirect', 'my_login_redirect', 10, 3 );