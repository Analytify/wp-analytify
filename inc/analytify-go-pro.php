<style type="text/css">
	.analytify_go_pro_wrap {
		box-sizing: border-box;
		margin: 0 auto;
		max-width: 1260px;
		padding: 24px 20px 40px;
	}

	.analytify_go_pro_inner {
		overflow-x: auto;
		-webkit-overflow-scrolling: touch;
	}

	.analytify_go_pro_table {
		background-color: #fff;
		border: 1px solid #e2e5e8;
		border-collapse: collapse;
		table-layout: fixed;
		width: 100%;
	}

	.analytify_go_pro_table thead th {
		background-color: #f5f6f7;
		border-bottom: 1px solid #e2e5e8;
		border-right: 1px solid #e2e5e8;
		color: #1d2327;
		font-size: 15px;
		font-weight: 600;
		line-height: 1.35;
		padding: 14px 18px;
		text-align: left;
		vertical-align: bottom;
	}

	.analytify_go_pro_table thead th:last-child {
		border-right: 0;
	}

	.analytify_go_pro_table thead .analytify_go_pro_table__col--feature {
		width: 52%;
	}

	.analytify_go_pro_table thead .analytify_go_pro_table__col--tier {
		font-weight: 600;
		text-align: center;
		vertical-align: middle;
		width: 24%;
	}

	.analytify_go_pro_table thead .analytify_go_pro_table__tier-sub {
		color: #646970;
		display: block;
		font-size: 13px;
		font-weight: 400;
		margin-top: 4px;
	}

	.analytify_go_pro_table tbody td {
		border-bottom: 1px solid #e2e5e8;
		border-right: 1px solid #e2e5e8;
		color: #50575e;
		font-size: 14px;
		line-height: 1.45;
		padding: 14px 18px;
		vertical-align: middle;
		word-wrap: break-word;
	}

	.analytify_go_pro_table tbody td:last-child {
		border-right: 0;
	}

	.analytify_go_pro_table tbody td.analytify_go_pro_table__col--tier {
		text-align: center;
	}

	.analytify_go_pro_table tbody tr:last-child td {
		border-bottom: 0;
	}

	.analytify_go_pro_icon {
		align-items: center;
		display: inline-flex;
		flex-wrap: wrap;
		gap: 6px;
		justify-content: center;
		max-width: 100%;
	}

	.analytify_go_pro_icon .dashicons {
		font-size: 22px;
		height: 22px;
		line-height: 1;
		width: 22px;
	}

	.analytify_go_pro_icon--yes .dashicons {
		color: #00a32a;
	}

	.analytify_go_pro_icon--no .dashicons {
		color: #d63638;
	}

	.analytify_go_pro_icon__note {
		color: #787c82;
		display: inline-block;
		font-size: 12px;
		font-weight: 400;
		line-height: 1.3;
		max-width: 100%;
		text-align: center;
	}

	.analytify_go_pro_feat {
		align-items: center;
		display: inline;
		line-height: 1.45;
	}

	.analytify_go_pro_feat .analytify_go_pro_feat-hint {
		display: inline-block;
		line-height: 1;
		margin-left: 4px;
		vertical-align: middle;
	}

	.analytify_go_pro_feat .analytify_go_pro_feat-hint .dashicons {
		color: #787c82;
		font-size: 16px;
		height: 18px;
		width: 18px;
	}

	.analytify_go_pro_feat .analytify_go_pro_feat-hint:focus {
		outline: none;
	}

	.analytify_go_pro_feat .analytify_go_pro_feat-hint:focus-visible {
		border-radius: 2px;
		box-shadow: 0 0 0 1px #2271b1;
	}

	.analytify_go_pro_feat .analytify_go_pro_feat-hint:focus-within .analytify_tooltiptext {
		opacity: 1;
		visibility: visible;
	}

	.analytify_go_pro_cta {
		margin-top: 28px;
		text-align: center;
	}

	.analytify_go_pro_cta .analytify_btn_buy {
		background: #00c853;
		border: 0;
		border-radius: 4px;
		box-shadow: 0 2px 3px rgba(0, 0, 0, 0.15);
		color: #fff;
		display: inline-block;
		font: 600 18px -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
		line-height: 1.25;
		margin: 0 auto 16px;
		padding: 14px 32px;
		text-align: center;
		text-decoration: none;
	}

	.analytify_go_pro_cta .analytify_btn_buy:hover,
	.analytify_go_pro_cta .analytify_btn_buy:focus {
		box-shadow: 0 1px 2px rgba(0, 0, 0, 0.12);
		color: #fff;
	}

	.analytify_go_pro_discount {
		color: #50575e;
		font-size: 15px;
		line-height: 1.5;
		margin: 0;
	}

	.analytify_go_pro_discount span {
		color: #00a32a;
		font-weight: 700;
	}

	@media screen and (max-width: 782px) {
		.analytify_go_pro_wrap {
			padding-left: 12px;
			padding-right: 12px;
		}

		.analytify_go_pro_table thead .analytify_go_pro_table__col--feature {
			width: 46%;
		}
	}
</style>
<div class="analytify_go_pro_wrap">
	<?php
	/**
	 * Analytify Go Pro Page (Free vs Pro comparison).
	 *
	 * Each row: `feature` (string), optional `feature_hint` (short tooltip for info icon),
	 * `free` and `pro` as arrays: `check` (bool), optional `note` (string).
	 *
	 * Legacy filter rows may use `pro`, `mid`, `free` keys; they are normalized when `feature` is absent.
	 *
	 * @package WP_Analytify
	 */

	/**
	 * Normalize a comparison row for rendering (supports legacy pro/mid/free shape).
	 *
	 * @param array<string, mixed> $row Row data.
	 * @return array<string, mixed>|null
	 */
	$analytify_go_pro_normalize_row = static function ( $row ) {
		if ( ! is_array( $row ) ) {
			return null;
		}
		if ( isset( $row['feature'] ) && is_string( $row['feature'] ) ) {
			$out = array(
				'feature' => $row['feature'],
				'free'    => isset( $row['free'] ) && is_array( $row['free'] ) ? $row['free'] : array( 'check' => false ),
				'pro'     => isset( $row['pro'] ) && is_array( $row['pro'] ) && isset( $row['pro']['check'] ) ? $row['pro'] : array( 'check' => true ),
			);
			if ( ! empty( $row['feature_hint'] ) && is_string( $row['feature_hint'] ) ) {
				$out['feature_hint'] = $row['feature_hint'];
			}
			return $out;
		}
		if ( ! isset( $row['pro'] ) || ! is_string( $row['pro'] ) ) {
			return null;
		}
		$cell_mid  = isset( $row['mid'] ) ? (string) $row['mid'] : '';
		$cell_free = isset( $row['free'] ) ? (string) $row['free'] : '';
		$no_label  = __( 'No', 'wp-analytify' );
		$free_yes  = ( $no_label !== $cell_free && '' !== trim( $cell_free ) );
		$free_spec = array(
			'check' => $free_yes,
			'note'  => '',
		);
		if ( $free_yes && '' !== $cell_mid ) {
			$free_spec['note'] = $cell_mid;
		} elseif ( $free_yes && $cell_free !== $row['pro'] ) {
			$free_spec['note'] = $cell_free;
		}
		return array(
			'feature' => $row['pro'],
			'free'    => $free_spec,
			'pro'     => array(
				'check' => true,
				'note'  => '',
			),
		);
	};

	$analytify_compare_rows = array(
		array(
			'feature' => __( 'Overview Dashboard', 'wp-analytify' ),
			'free'    => array(
				'check' => true,
				'note'  => __( 'Limited', 'wp-analytify' ),
			),
			'pro'     => array(
				'check' => true,
				'note'  => __( 'Complete Stats', 'wp-analytify' ),
			),
		),
		array(
			'feature' => __( 'Analytics under Posts (admin)', 'wp-analytify' ),
			'free'    => array( 'check' => true ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Analytics under Pages (admin)', 'wp-analytify' ),
			'free'    => array( 'check' => true ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Comparison Stats (Visitors & Views monthly/yearly)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Analytics under Custom Post Types (front/admin)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Authors Tracking (addon)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Forms Tracking (addon)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Enhanced E-Commerce for WooCommerce (addon)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			// translators: EDD is the usual abbreviation for Easy Digital Downloads.
			'feature' => __( 'Enhanced E-Commerce for EDD (addon)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Events Tracking (addon)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Google Ads Tracking', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Pixels Tracking (addon)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Paid Memberships Pro Tracking (addon)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'LearnDash Tracking (addon)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'LifterLMS Tracking (addon)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Goals (Key Events) Tracking (addon)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Campaigns Tracking (addon)', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Email Notifications (addon)', 'wp-analytify' ),
			'free'    => array(
				'check' => true,
				'note'  => __( 'Lite', 'wp-analytify' ),
			),
			'pro'     => array(
				'check' => true,
				'note'  => __( 'Full', 'wp-analytify' ),
			),
		),
		array(
			'feature'      => __( 'Custom Dimensions (addon)', 'wp-analytify' ),
			'feature_hint' => __( 'Extra GA dimensions for posts, pages, and custom types.', 'wp-analytify' ),
			'free'         => array( 'check' => false ),
			'pro'          => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Real-Time Live Stats', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Google Search Console Reports', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'PageSpeed Insights Dashboard', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Video Tracking', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Demographics Stats', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Search Terms Tracking', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Shortcodes', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( '404 Page, Ajax & JS Error Stats', 'wp-analytify' ),
			'free'    => array( 'check' => false ),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Google AMP Support', 'wp-analytify' ),
			'free'    => array(
				'check' => true,
				'note'  => __( 'Tracking only', 'wp-analytify' ),
			),
			'pro'     => array( 'check' => true ),
		),
		array(
			'feature' => __( 'Priority Email Support', 'wp-analytify' ),
			'free'    => array(
				'check' => false,
				'note'  => __( 'wordpress.org support only', 'wp-analytify' ),
			),
			'pro'     => array( 'check' => true ),
		),
	);

	$analytify_compare_rows = apply_filters( 'analytify_go_pro_compare_rows', $analytify_compare_rows );

	/**
	 * Render one Free/Pro status cell.
	 *
	 * @param array<string, mixed> $spec Cell spec.
	 * @return string Safe HTML.
	 */
	$analytify_go_pro_render_cell = static function ( $spec ) {
		$check = isset( $spec['check'] ) && $spec['check'];
		$note  = isset( $spec['note'] ) ? (string) $spec['note'] : '';
		$class = $check ? 'analytify_go_pro_icon analytify_go_pro_icon--yes' : 'analytify_go_pro_icon analytify_go_pro_icon--no';
		$icon  = $check ? 'yes' : 'no-alt';
		$label = $check ? __( 'Included', 'wp-analytify' ) : __( 'Not included', 'wp-analytify' );
		$html  = '<span class="' . esc_attr( $class ) . '">';
		$html .= '<span class="dashicons dashicons-' . esc_attr( $icon ) . '" aria-hidden="true"></span>';
		$html .= '<span class="screen-reader-text">' . esc_html( $label ) . '</span>';
		if ( '' !== $note ) {
			$html .= '<span class="analytify_go_pro_icon__note">(' . esc_html( $note ) . ')</span>';
		}
		$html .= '</span>';
		return $html;
	};
	?>
	<div class="analytify_go_pro_inner">
		<table class="analytify_go_pro_table">
			<thead>
				<tr>
					<th scope="col" class="analytify_go_pro_table__col--feature">
						<?php esc_html_e( 'Features Comparison', 'wp-analytify' ); ?>
					</th>
					<th scope="col" class="analytify_go_pro_table__col--tier">
						<?php esc_html_e( 'Free', 'wp-analytify' ); ?>
						<span class="analytify_go_pro_table__tier-sub"><?php esc_html_e( 'Basic Tracking', 'wp-analytify' ); ?></span>
					</th>
					<th scope="col" class="analytify_go_pro_table__col--tier">
						<?php esc_html_e( 'Pro', 'wp-analytify' ); ?>
						<span class="analytify_go_pro_table__tier-sub"><?php esc_html_e( 'Advanced Tracking', 'wp-analytify' ); ?></span>
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				if ( is_array( $analytify_compare_rows ) ) {
					foreach ( $analytify_compare_rows as $analytify_row ) {
						$norm = $analytify_go_pro_normalize_row( $analytify_row );
						if ( null === $norm ) {
							continue;
						}
						$feat = $norm['feature'];
						$hint = isset( $norm['feature_hint'] ) ? (string) $norm['feature_hint'] : '';
						?>
				<tr>
					<td>
						<span class="analytify_go_pro_feat">
							<?php echo esc_html( $feat ); ?>
							<?php
							if ( '' !== trim( wp_strip_all_tags( $hint ) ) ) {
								$hint_plain = sanitize_text_field( wp_strip_all_tags( $hint ) );
								?>
							<span class="analytify_go_pro_feat-hint analytify_tooltip" tabindex="0" role="button" aria-label="<?php echo esc_attr( $hint_plain ); ?>">
								<span class="dashicons dashicons-info" aria-hidden="true"></span>
								<span class="analytify_tooltiptext"><?php echo esc_html( $hint_plain ); ?></span>
							</span>
								<?php
							}
							?>
						</span>
					</td>
					<td class="analytify_go_pro_table__col--tier"><?php echo $analytify_go_pro_render_cell( $norm['free'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
					<td class="analytify_go_pro_table__col--tier"><?php echo $analytify_go_pro_render_cell( $norm['pro'] ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></td>
				</tr>
						<?php
					}
				}
				?>
			</tbody>
		</table>
		<div class="analytify_go_pro_cta">
			<a class="analytify_btn_buy" target="_blank" rel="noopener noreferrer" href="<?php echo esc_url( 'https://analytify.io/free-vs-pro/?utm_source=analytify-lite&utm_medium=pro-vs-free-page&utm_campaign=pro-upgrade&utm_content=Upgrade+Now+CTA' ); ?>">
				<?php esc_html_e( 'Upgrade Now', 'wp-analytify' ); ?>
			</a>
			<p class="analytify_go_pro_discount">
				<?php
				printf(
					/* translators: 1: discount code. */
					esc_html__( 'Use %1$s for 60%% off.', 'wp-analytify' ),
					'<span>BFCM60</span>'
				);
				?>
			</p>
		</div>
	</div>
</div>
