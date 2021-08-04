<?php
function get_compared_colors( $results, $compare_results, $date_different, $stats_for = '' ) {
	if ( $compare_results != 0 ) {
		$compare = number_format( ( ( $results - $compare_results ) / $compare_results ) * 100, 2 ) . "%";
	} else {
		return;
	}

	// Invert results for bounce rate.
	if ( ! empty( $stats_for ) && 'bounce_rate' === $stats_for ) {
		return array(
			$compare < 0 ? '#00c853' : '#fa5825',
			$compare < 0 ? '#4ed98817' : '#ffffff'
		);
	}

	return array(
		$compare > 0 ? '#00c853' : '#fa5825',
		$compare > 0 ? '#4ed98817' : '#ffffff'
	);
}

function get_compare_email_stats( $results, $compare_results, $date_different, $stats_for = '' ) {
	if ( $compare_results != 0 ) {
		$compare = number_format( ( ( $results - $compare_results ) / $compare_results ) * 100, 2 ) . "%";
	} else {
		return;
	}

	$image_name = $compare > 0 ? 'analytify_green_arrow.png' : 'analytify_red_arrow.png';
	$color      = $compare > 0 ? '#00c853' : '#fa5825';

	// Invert results for bounce rate.
	if ( ! empty( $stats_for ) && 'bounce_rate' === $stats_for ) {
		$image_name = $compare < 0 ? 'analytify_green_arrow_down.png' : 'analytify_red_arrow_up.png';
		$color      = $compare < 0 ? '#00c853' : '#fa5825';
	}
	
	echo '<tr>
				 <td colspan="3">
					<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
					 <tbody><tr>
						<td valign="bottom" style="padding: 10px 10px 3px; font: 700 16px "Roboto", Arial, Helvetica, sans-serif;" align="center"><font color=" ' . $color . ' "><img src="' . ANALYTIFY_IMAGES_PATH  . $image_name. '" alt="" style="padding-right:10px; width:10px">' . $compare . '</font></td>
					 </tr>
					 <tr>
						<td style="padding: 3px 10px 10px; font: 700 10px "Roboto", Arial, Helvetica, sans-serif;text-transform:uppercase;" align="center"><font color="#909090">' . $date_different . ' ago</font></td>
					 </tr>
					</tbody></table>
				 </td>
				</tr>';
}


function pa_email_include_general( $current, $stats, $old_stats, $date_different ) {
	ob_start();	?>

	<!-- <tr>
		<td valign="top" style="border: 1px solid #e2e5e8;">
			<table width="100%" cellspacing="0" cellpadding="0" border="0" align="center" bgcolor="#f9fafa">
				<tr>
					<td style="font: normal 16px 'Roboto slab', Arial, Helvetica, sans-serif; padding: 11px 20px;">
						<font color="#444444"><?php // analytify_e( 'General Statistics', 'wp-analytify' ) ?></font>
					</td>
				</tr>
			</table>
		</td>
	</tr> -->
	
	<tr>
		<td bgcolor="#ffffff" class="session-table">
			<table cellspacing="20" cellpadding="0" border="0" align="center" bgcolor="#ffffff" width="100%" class="box-table">

				<tr>
				
				<?php	
				$compared_colors = get_compared_colors( $stats->totalsForAllResults['ga:sessions'], 
					$old_stats->totalsForAllResults['ga:sessions'], 
					$date_different 
				); 
				?>

					<td style="border: 1px solid <?php echo $compared_colors[0]; ?>; background-color: <?php echo $compared_colors[1]; ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php analytify_e( 'Sessions', 'wp-analytify' ) ?></font>                       
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color="#444444"><?php echo WPANALYTIFY_Utils::pretty_numbers( $stats->totalsForAllResults['ga:sessions'] ) ?></font>
								</td>
							</tr>
								<?php if ( $old_stats ): ?>
									<?php get_compare_email_stats( $stats->totalsForAllResults['ga:sessions'], $old_stats->totalsForAllResults['ga:sessions']  , $date_different ) ?>
								<?php endif; ?>
						</table>
					</td>
					
					<?php	
					$compared_colors = get_compared_colors( $stats->totalsForAllResults['ga:users'], 
						$old_stats->totalsForAllResults['ga:users'], 
						$date_different 
					); 
					?>

					<td style="border: 1px solid <?php echo $compared_colors[0]; ?>; background-color: <?php echo $compared_colors[1]; ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php analytify_e( 'Visitors', 'wp-analytify' ); ?></font>
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>

							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color="#444444"><?php echo WPANALYTIFY_Utils::pretty_numbers( $stats->totalsForAllResults['ga:users'] ) ?></font>
								</td>
							</tr>

							<?php if ( $old_stats ): ?>
								<?php get_compare_email_stats( $stats->totalsForAllResults['ga:users'], $old_stats->totalsForAllResults['ga:users']  , $date_different ) ?>
							<?php endif; ?>

						</table>
					</td>

					<?php	
					$compared_colors = get_compared_colors( $stats->totalsForAllResults['ga:bounceRate'], 
						$old_stats->totalsForAllResults['ga:bounceRate'], 
						$date_different ,
						'bounce_rate'
					); 
					?>

					<td style="border: 1px solid <?php echo $compared_colors[0]; ?>; background-color: <?php echo $compared_colors[1]; ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php analytify_e( 'Bounce rate', 'wp-analytify' ); ?></font>
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;"><font color="#444444"><?php echo number_format( $stats->totalsForAllResults['ga:bounceRate'], 2 ) ?>%</font></td>
							</tr>

							<?php if ( $old_stats ): ?>
								<?php get_compare_email_stats( $stats->totalsForAllResults['ga:bounceRate'], $old_stats->totalsForAllResults['ga:bounceRate'], $date_different, 'bounce_rate' ) ?>
							<?php endif; ?>

						</table>
					</td>
				</tr>

				<tr>

				<?php	
				$compared_colors = get_compared_colors( $stats->totalsForAllResults['ga:avgSessionDuration'], 
					$old_stats->totalsForAllResults['ga:avgSessionDuration'], 
					$date_different 
				); 
				?>
				
					<td style="border: 1px solid <?php echo $compared_colors[0]; ?>; background-color: <?php echo $compared_colors[1]; ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php _e( 'Avg time on site', 'wp-analytify-email' ); ?></font>
								</td>
							</tr>
							<tr>
							<td width="45"></td>
							<td align="center">
								<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
							</td>
							<td width="45"></td>
						</tr>
						<tr>
							<td align="center" colspan="3" style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
								<font color="#444444"><?php echo WPANALYTIFY_Utils::pretty_time( $stats->totalsForAllResults['ga:avgSessionDuration'] ) ?></font>
							</td>
						</tr>

							<?php if ( $old_stats ): ?>
								<?php get_compare_email_stats( $stats->totalsForAllResults['ga:avgSessionDuration'], $old_stats->totalsForAllResults['ga:avgSessionDuration']  , $date_different ) ?>

							<?php endif; ?>

						</table>
					</td>

					<?php	
					$compared_colors = get_compared_colors( $stats->totalsForAllResults['ga:pageviewsPerSession'], 
						$old_stats->totalsForAllResults['ga:pageviewsPerSession'], 
						$date_different 
					); 
					?>

					<td style="border: 1px solid <?php echo $compared_colors[0]; ?>; background-color: <?php echo $compared_colors[1]; ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php _e( 'Average pages', 'wp-analytify-email' ) ?></font>
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color="#444444"><?php echo WPANALYTIFY_Utils::pretty_numbers( $stats->totalsForAllResults['ga:pageviewsPerSession'] ) ?></font></td>
							</tr>

							<?php if ( $old_stats ): ?>
								<?php get_compare_email_stats( $stats->totalsForAllResults['ga:pageviewsPerSession'], $old_stats->totalsForAllResults['ga:pageviewsPerSession']  , $date_different ) ?>
							<?php endif; ?>

						</table>
					</td>

				<?php	
				$compared_colors = get_compared_colors( $stats->totalsForAllResults['ga:pageviews'], 
					$old_stats->totalsForAllResults['ga:pageviews'], 
					$date_different 
				); 
				?>

					<td style="border: 1px solid <?php echo $compared_colors[0]; ?>; background-color: <?php echo $compared_colors[1]; ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php analytify_e( 'Pageviews', 'wp-analytify' ) ?></font></td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color="#444444"><?php echo WPANALYTIFY_Utils::pretty_numbers( $stats->totalsForAllResults['ga:pageviews'] ); ?></font></td>
							</tr>

							<?php if ( $old_stats ): ?>
								<?php get_compare_email_stats( $stats->totalsForAllResults['ga:pageviews'], $old_stats->totalsForAllResults['ga:pageviews']  , $date_different ) ?>
							<?php endif; ?>

						</table>
					</td>
				</tr>

				<tr>

				<?php	
				$compared_colors = get_compared_colors( $stats->totalsForAllResults['ga:percentNewSessions'], 
					$old_stats->totalsForAllResults['ga:percentNewSessions'], 
					$date_different ); 
				?>

					<td style="border: 1px solid <?php echo $compared_colors[0]; ?>; background-color: <?php echo $compared_colors[1]; ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php analytify_e( '% New sessions', 'wp-analytify' ) ?></font>
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color=""><?php echo number_format( $stats->totalsForAllResults['ga:percentNewSessions'], 2) ?>%</font></td>
							</tr>

							<?php if ( $old_stats ): ?>
								<?php get_compare_email_stats( $stats->totalsForAllResults['ga:percentNewSessions'], $old_stats->totalsForAllResults['ga:percentNewSessions']  , $date_different ) ?>
							<?php endif; ?>

						</table>
					</td>

				<?php	
				$compared_colors = get_compared_colors( $stats->totalsForAllResults['ga:newUsers'], 
					$old_stats->totalsForAllResults['ga:newUsers'],
					$date_different 
				); 
				?>

					<td style="border: 1px solid <?php echo $compared_colors[0]; ?>; background-color: <?php echo $compared_colors[1]; ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php _e( 'New visitors', 'wp-analytify-email' ) ?></font>
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color="#444444"><?php echo WPANALYTIFY_Utils::pretty_numbers( $stats->totalsForAllResults['ga:newUsers'] ) ?></font>
								</td>
							</tr>

							<?php if ( $old_stats ): ?>
								<?php get_compare_email_stats( $stats->totalsForAllResults['ga:newUsers'], $old_stats->totalsForAllResults['ga:newUsers'], $date_different ); ?>
							<?php endif; ?>

						</table>
					</td>

				<?php	
				$compared_colors = get_compared_colors( $stats->totalsForAllResults['ga:sessions'] - $stats->totalsForAllResults['ga:newUsers'], 
					$old_stats->totalsForAllResults['ga:sessions'] - $old_stats->totalsForAllResults['ga:newUsers'], 
					$date_different ); ?>

					<td style="border: 1px solid <?php echo $compared_colors[0]; ?>; background-color: <?php echo $compared_colors[1]; ?>" width="33.333%">
						<table width="100%" cellpadding="0" cellspacing="0" border="0">
							<tr>
								<td align="center" colspan="3"
									style="font: 500 14px 'Roboto', Arial, Helvetica, sans-serif;padding: 16px 5px 5px; text-transform: uppercase; letter-spacing: 0.01em;">
									<font color="#848484"><?php _e( 'Returning visitors', 'wp-analytify-email' ) ?></font>
								</td>
							</tr>
							<tr>
								<td width="45"></td>
								<td align="center">
									<hr style="margin:0;border:0;border-top: 1px solid #e5e5e5;" />
								</td>
								<td width="45"></td>
							</tr>
							<tr>
								<td align="center" colspan="3"
									style="padding: 13px 5px 10px; font: 400 24px 'Roboto', Arial, Helvetica, sans-serif;">
									<font color="#444444"><?php echo absint(WPANALYTIFY_Utils::pretty_numbers( $stats->totalsForAllResults['ga:sessions'] - $stats->totalsForAllResults['ga:newUsers'] )); ?>
									</font>
								</td>
							</tr>

							<?php if ( $old_stats ): ?>
								<?php get_compare_email_stats( $stats->totalsForAllResults['ga:sessions'] - $stats->totalsForAllResults['ga:newUsers'],  $old_stats->totalsForAllResults['ga:sessions'] - $old_stats->totalsForAllResults['ga:newUsers'] , $date_different ) ?>
							<?php endif; ?>

						</table>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td>
			<table cellpadding="0" cellspacing="16" border="0" width="100%" bgcolor="#f9fafa">
				<tr>
					<td width="32" style="text-align: right;"><img src="<?php echo ANALYTIFY_IMAGES_PATH . "anlytify_about_icon.png" ?>" alt=""></td>
					<td style="font: normal 13px 'Roboto', Arial, Helvetica, sans-serif;"><font color="#444"><?php analytify_e( 'Did you know that total time on your site is', 'wp-analytify' ) ?> <?php echo WPANALYTIFY_Utils::pretty_time( $stats->totalsForAllResults['ga:sessionDuration'] ); ?></font></td>
				</tr>
			</table>
		</td>
	</tr>

	<!-- <tr>
		<td style="padding:15px;"></td>
	</tr> -->

	<?php if ( ! class_exists( 'WP_Analytify_Email' ) ) : ?>
		<tr>
			<td style="padding:15px;"></td>
		</tr>

		<tr>
			<td valign="top" class="analytify-promo-inner-table" style="padding: 30px 45px;" bgcolor="#ffffff">
				<table style="margin: 0 auto;" cellspacing="0" cellpadding="0" width="100%" align="center">
					<tbody><tr>
						<td valign="top" colspan="2" style="font-size: 26px; font-family: 'Roboto'; font-weight: bold; line-height: 26px; padding-bottom: 24px;" align="center " class="analytify-promo-heading"><font color="#313133"><?php analytify_e( 'Customize weekly and monthly reports' ); ?></font> </td>
					</tr>
					<tr>
						<td valign="top" colspan="2" style="font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px; padding-bottom: 15px;"><font color="#383b3d"><?php analytify_e( 'Email notifications add-on extends the Analytify Pro, and enables more control on customizing Analytics Email reports for your websites, delivers Analytics summaries straight in your inbox weekly and monthly.'); ?></font></td>
					</tr>
					<tr>
						<td valign="top" class="analytify-promo-lists" width="40%">
							<table cellspacing="0" cellpadding="0" width="100%" align="center">
								<tbody><tr>
										<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="<?php esc_attr_e( 'checkmark', 'wp-analytify' ); ?>"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d"><?php analytify_e( 'Add your logo'); ?></font></td>
								</tr>
								<tr>
										<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="<?php esc_attr_e( 'checkmark', 'wp-analytify' ); ?>"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d"><?php analytify_e( 'Edit Email Subject'); ?></font></td>
								</tr>
								<tr>
										<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="<?php esc_attr_e( 'checkmark', 'wp-analytify' ); ?>"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d"><?php analytify_e( 'Choose your own metrics to display in reports'); ?></font></td>
								</tr>
						</tbody></table>
					</td>
					<td valign="top" class="analytify-promo-lists" width="52%" style="padding-left: 8%;">
						<table cellspacing="0" cellpadding="0" width="100%" align="center">
							<tbody><tr>
								<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="<?php esc_attr_e( 'checkmark', 'wp-analytify' ); ?>"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d"><?php analytify_e( 'Add personal note' ); ?></font></td>
							</tr>
							<tr>
								<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="checkmark"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d"><?php analytify_e( 'Schedule weekly reports' ); ?></font></td>
							</tr>
							<tr>
								<td valign="top" style="padding-top: 6px; padding-right: 5px;" width="15"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/bef57c22-a546-4d5e-b209-f028a24a1642.png" alt="checkmark"></td><td style="padding-bottom: 5px;font-size: 14px; font-family: 'Segoe UI'; font-weight: normal; line-height: 20px;"><font color="#383b3d"><?php analytify_e( 'Schedule monthly reports' ); ?></font></td>
							</tr>
						</tbody></table>
					</td>
					</tr> 
					<?php if ( class_exists( 'WP_Analytify_Pro_Base' ) ) : ?>
						<tr>
							<td valign="top" colspan="2" align="center" style="padding-top: 24px;"><a href="<?php echo esc_url( 'https://analytify.io/add-ons/email-notifications?utm_source=analytify-pro&utm_medium=email-reports&utm_content=cta&utm_campaign=addons-upgrade' ); ?>"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/c29b00f7-b5fa-4e04-9a28-e9d77c69ba15.png" alt="<?php esc_attr_e( 'Buy Email Notifications addon', 'wp-analytify' ); ?>"></a></td>
						</tr>
					<?php else : ?>
						<tr>
							<td valign="top" colspan="2" align="center" style="padding-top: 24px;"><a href="<?php echo esc_url( 'https://analytify.io/add-ons/email-notifications?utm_source=analytify-lite&utm_medium=email-reports&utm_content=cta&utm_campaign=bundle-upgrade' ); ?>"><img src="https://mcusercontent.com/16d94a7b1c408429988343325/images/3c067584-abb3-4c6b-8c28-4cc265e67bfa.png" alt="<?php esc_attr_e( 'Upgrade to Analytify Pro + Email Notifications bundle', 'wp-analytify' ); ?>" class="analytify-update-pro"></a></td>
						</tr>
					<?php endif; ?>
			</tbody></table>
		</td>
	</tr>
	<?php endif; ?>

	<!-- <tr>
		<td style="padding:15px;"></td>
	</tr> -->

	<?php
	$message = ob_get_clean();
	return $message;
}