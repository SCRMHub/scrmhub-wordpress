<?php
/**
 * Run the SCRM Hub Cron Job for hourly
 */
function scrmhub_cronjob() {
  $scrmhub = $GLOBALS['scrmhub'];
  $scrmhub->cron->hourly();
}

/**
 * Run the SCRM Hub Cron Job for twice daily
 */
function scrmhub_cronjob_twice_daily() {
  $scrmhub = $GLOBALS['scrmhub'];
  $scrmhub->cron->twice_daily();
}

/**
 * Run the SCRM Hub Cron Job for daily
 */
function scrmhub_cronjob_daily() {
  $scrmhub = $GLOBALS['scrmhub'];
  $scrmhub->cron->daily();
}


/**
 * When a post updates, actions to run
 * @param  [type] $post_id [description]
 * @return [type]          [description]
 */
function scrmhub_post_updated($post_id) {
  //Ok, fetch the short url
  $scrmhub = $GLOBALS['scrmhub'];

  // If this is just a revision, don't update.
  if ( wp_is_post_revision( $post_id ) ) {
    return;
  }

  //Call the API even if the permalink didn't change (but just in case it did)
  $scrmhub->shorturls->fetchPostUrl($post_id);
}

/**
 * Legacy render function
 * @return strong   The HTML
 */
function scrmhub_legacy_render($network = 'linkedin', $label = 'Sign in with LinkedIn') {
  do_action( 'scrmhub_login_button', array(
    'network' => $network,
    'label'   => $label 
  ));
}

//Cron jobs
add_action('scrmhub_cronjob',             'scrmhub_cronjob' );
add_action('scrmhub_cronjob_twice_daily', 'scrmhub_cronjob_twice_daily' );
add_action('scrmhub_cronjob_daily',       'scrmhub_cronjob_daily' );

//Legacy connection
add_action('wordpress_social_login', 'scrmhub_legacy_render');

//Called on every post save
add_action('save_post', 'scrmhub_post_updated' );