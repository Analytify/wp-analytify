<?php
/**
 * Admin View: Page - Status Logs
 *
 * @package Analytify
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<style media="screen">
#analytify-log-viewer-wrap{
  margin: 10px 20px 0 2px;
}
#log-viewer-select {
  padding: 10px 0 8px;
  line-height: 28px;
  margin-top: 10px;
}
#log-viewer {
  background: #fff;
  border: 1px solid #e5e5e5;
  box-shadow: 0 1px 1px rgba(0,0,0,.04);
  padding: 5px 20px;
}
#log-viewer pre {
  font-family: monospace;
  white-space: pre-wrap;
  word-wrap: break-word;
}
#analytify-log-viewer-wrap .page-title-action{
  margin-left: 4px;
  padding: 4px 8px;
  padding-top: 4px;
  padding-right: 8px;
  padding-bottom: 4px;
  padding-left: 8px;
  position: relative;
  top: -3px;
  text-decoration: none;
  border: none;
  border: 1px solid #ccc;
  border-radius: 2px;
  background: #f7f7f7;
  text-shadow: none;
  font-weight: 600;
  font-size: 13px;
  line-height: normal;
  color: #0073aa;
  cursor: pointer;
  outline: 0;
}
#analytify-log-viewer-wrap .page-title-action:hover {
  border-color: #008EC2;
  background: #00a0d2;
  color: #fff;
}
</style>

<div id='analytify-log-viewer-wrap'>
<?php if ( $logs ) : ?>

	<div id="log-viewer-select">
		<div class="alignleft">
			<h2>
				<?php echo esc_html( $viewed_log ); ?>
				<?php if ( ! empty( $viewed_log ) ) : ?>
					<a class="page-title-action" href="<?php echo esc_url( wp_nonce_url( add_query_arg( array( 'handle' => sanitize_title( $viewed_log ) ), admin_url( 'admin.php?page=analytify-logs' ) ), 'remove_log' ) ); ?>" class="button"><?php esc_html_e( 'Delete log', 'wp-analytify' ); ?></a>
				<?php endif; ?>
			</h2>
		</div>
		<div class="alignright">
			<form action="<?php echo esc_url( admin_url( 'admin.php?page=analytify-logs' ) ); ?>" method="post">
				<select name="log_file">
					<?php foreach ( $logs as $log_key => $log_file ) : ?>
						<?php
							$timestamp = filemtime( ANALYTIFY_LOG_DIR . $log_file );
							/* translators: 1: last access date 2: last access time */
							$date = sprintf( __( '%1$s at %2$s', 'wp-analytify' ), date_i18n( 'F j, Y', $timestamp ), date_i18n( 'g:i a', $timestamp ) );
						?>
						<option value="<?php echo esc_attr( $log_key ); ?>" <?php selected( sanitize_title( $viewed_log ), $log_key ); ?>><?php echo esc_html( $log_file ); ?> (<?php echo esc_html( $date ); ?>)</option>
					<?php endforeach; ?>
				</select>
				<button type="submit" class="button" value="<?php esc_attr_e( 'View', 'wp-analytify' ); ?>"><?php esc_html_e( 'View', 'wp-analytify' ); ?></button>
			</form>
		</div>
		<div class="clear"></div>
	</div>
	<div id="log-viewer">
		<pre><?php echo esc_html( file_get_contents( ANALYTIFY_LOG_DIR . $viewed_log ) ); ?></pre>
	</div>
<?php else : ?>
	<div class="updated wp-analytify-message inline"><p><?php esc_html_e( 'There are currently no logs to view.', 'wp-analytify' ); ?></p></div>
<?php endif; ?>
</div>
<?php
