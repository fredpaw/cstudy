<?php
/**
 * Default output for a download via the [download] shortcode
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
} // Exit if accessed directly

?>
<a class="download-link" title="<?php if ( $dlm_download->has_version_number() ) {
	printf( __( 'Version %s', 'download-monitor' ), $dlm_download->get_the_version_number() );
} ?>" href="<?php $dlm_download->the_download_link(); ?>" rel="nofollow">
	<img src="<?php echo get_stylesheet_directory_uri(); ?>/images/csa_list_004.png"/> 下载
</a>