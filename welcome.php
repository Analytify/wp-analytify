<?php

/**
 * Weclome Page Class
 *
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

/**
 * Analytify_Welcome Class
 *
 * A general class for Welcome pages.
 *
 */

class Analytify_Welcome {

	/**
	 * @var string The capability users should have to view the page
	 */
	public $minimum_capability = 'manage_options';

	/**
	 * Get things initialized
	 *
	 * @since 1.1
	 */
	public function __construct() {

		add_action( 'admin_menu', array( $this, 'admin_menus') );
		add_action( 'admin_head', array( $this, 'admin_head' ) );
		add_action( 'admin_init', array( $this, 'welcome'    ) );
	}

	/**
	 * Register the Dashboard Pages which are later hidden but these pages
	 * are used to render the Welcome and Credits pages.
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function admin_menus() {

		// What's new Page
		add_dashboard_page(
			esc_html__( 'Welcome to Analytify', 'wp-analytify' ),
			esc_html__( 'Welcome to Analytify', 'wp-analytify' ),
			$this->minimum_capability,
			'wp-analytify-new',
			array( $this, 'new_screen' )
		);

		// Getting Started Page
		add_dashboard_page(
			esc_html__( 'Getting started with Analytify', 'wp-analytify' ),
			esc_html__( 'Getting started with Analytify', 'wp-analytify' ),
			$this->minimum_capability,
			'wp-analytify-getting-started',
			array( $this, 'getting_started_screen' )
		);

		// Credits Page
		add_dashboard_page(
			esc_html__( 'The people that build Analytify', 'wp-analytify' ),
			esc_html__( 'The people that build Analytify', 'wp-analytify' ),
			$this->minimum_capability,
			'wp-analytify-credits',
			array( $this, 'credits_screen' )
		);
	}

	/**
	 * Hide Individual Dashboard Pages
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function admin_head() {

		remove_submenu_page( 'index.php', 'wp-analytify-new' );
		remove_submenu_page( 'index.php', 'wp-analytify-getting-started' );
		remove_submenu_page( 'index.php', 'wp-analytify-credits' );

		// Badge for welcome page
		//$badge_url =  . 'images/welcome/welcome.png';
		?>
		<style type="text/css" media="screen">
		/*<![CDATA[*/
		.wp-analytify-badge {
			height: 200px;
			width: 200px;
			margin: -12px -5px;
			background: url("<?php echo plugins_url( 'assets/images/welcome-analytify.png', __FILE__ ); ?>") no-repeat;
			background-size: 100% auto;
		}

		.about-wrap .wp-analytify-badge {
			position: absolute;
			top: 0;
			right: 0;
		}

		.wp-analytify-welcome-screenshots {
			float: right;
			margin-left: 10px !important;
			border:1px solid #ccc;
			padding:0;
			box-shadow:4px 4px 0px rgba(0,0,0,.05)
		}

		.about-wrap .feature-section {
			margin-top: 20px;
		}

		.about-wrap .feature-section p{
			max-width: none !important;
		}

		.analytify-welcome-settings{
			clear: both;
			padding-top: 20px;
		}
		/*]]>*/
		</style>
		<?php
	}

	/**
	 * Navigation tabs
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function tabs() {
		$selected = isset( $_GET['page'] ) ? $_GET['page'] : 'wp-analytify-new';
		?>
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $selected == 'wp-analytify-new' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wp-analytify-new' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( "What's New", 'wp-analytify' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'wp-analytify-getting-started' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wp-analytify-getting-started' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'Getting Started', 'wp-analytify' ); ?>
			</a>
			<a class="nav-tab <?php echo $selected == 'wp-analytify-credits' ? 'nav-tab-active' : ''; ?>" href="<?php echo esc_url( admin_url( add_query_arg( array( 'page' => 'wp-analytify-credits' ), 'index.php' ) ) ); ?>">
				<?php esc_html_e( 'Credits', 'wp-analytify' ); ?>
			</a>
		</h2>
		<?php
	}

	/**
	 * Render What's New Screen
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function new_screen() {

		?>
		<div class="wrap about-wrap">
			<h1><?php echo sprintf( esc_html__( 'Welcome to Analytify %1$s', 'wp-analytify' ), ANALYTIFY_VERSION ); ?></h1>
			<div class="about-text">
			<?php echo sprintf( esc_html__( '%1$s Thank you for updating to the latest version! %2$s %3$s Analytify %4$s is ready to make people lives easier for those who love Google Analytics and WordPress. It makes Analytics simple for WordPress users that everyone can understand about their Website statistics and behavior. ', 'wp-analytify' ), '<em>', '</em>', '<br />', ANALYTIFY_VERSION );?>
			</div>
			<div class="wp-analytify-badge"></div>

			<?php $this->tabs(); ?>

			<div class="newfeatures">
				<h3><?php esc_html_e( 'New features', 'wp-analytify' );?></h3>

				<div class="feature-section">

					<img width="414" src="<?php echo plugins_url( 'assets/images/authenticate2.png', __FILE__ ); ?>" class="wp-analytify-welcome-screenshots"/>

					<h4><?php esc_html_e( 'Simple Authentication', 'wp-analytify' );?></h4>
					<p>
					<?php echo sprintf( esc_html__( 'You don\'t need to worry about copy and paste access code any more. ', 'wp-analytify' ) );?>
					</p>

					<h4><?php esc_html_e( 'Translation ready', 'wp-analytify' );?></h4>
					<p>
					<?php echo sprintf( esc_html__( 'Analytify %1$s contains all the strings ready to translate in any language. %2$s Click here %3$s to start translating this plugin into your language and don\'t forget to share your language files with us to ease for other users.', 'wp-analytify' ), ANALYTIFY_VERSION, '<a href="https://analytify.io/doc/can-translate-analytify-language/" style="text-decoration:none">', '</a>' ); ?>
					</p>

				</div>
			</div>

			<div class="newfeatures">
				<h3><?php esc_html_e( 'New features', 'wp-analytify' );?></h3>

				<div class="feature-section">

					  <img src="<?php echo plugins_url( 'assets/images/analytify-shortcodes-dropdown.png', __FILE__ ); ?>" class="wp-analytify-welcome-screenshots"/>

					<h4><?php esc_html_e( 'Analytify Shortcodes', 'wp-analytify' );?></h4>
					<p>
					<?php echo sprintf( esc_html__( 'Analytify shortcodes can be used for multiple purposes. It gives you more flexibility than the front-end statistics. You can disable front-end stats and use these shortcodes in any of your pages/posts to show Analytics in tabular style. If you are familiar with CSS, It can be easily modified to match with your website look and feel. %1$s Following are two types of shortcodes:', 'wp-analytify' ), '<br /><br />' );?>
					</p>

					<h4><?php esc_html_e( 'a) Simple:', 'wp-analytify' );?></h4>
					<p>
					<?php echo sprintf( esc_html__( 'Simple Analytify shortcode returns you only the numbers i.e results. It is useful when you want results of only 1 Metrics and can apply permissions using roles. You can fetch numbers of %1$s Sessions, Pageviews, Bounce Rate and Users %2$s etc', 'wp-analytify' ), '<em>', '</em> ');?>
					</p>

					<h4><?php esc_html_e( 'b) Advanced:', 'wp-analytify' );?></h4>
					<p>
					<?php echo sprintf( esc_html__( 'With this Advanced option, You can choose %1$s more than 1 Metrics and Dimensions, Sort the results, get maximum rows, apply permissions and fetch Analytics with specified start-date and end-date. %2$s Advanced shortcode returns results in table form which can be styled according to your website.', 'wp-analytify' ), '<em>', '</em>' );?>
					</p>

				</div>
			</div>

			<div class="newfeatures">
				<img width="414" height="" src="<?php echo plugins_url( 'assets/images/front-end.png', __FILE__ ); ?>" class="wp-analytify-welcome-screenshots"/>
				<h3><?php esc_html_e( 'Front-end Statistics', 'wp-analytify' );?></h3>
				<p>
				<?php echo sprintf( esc_html__( 'We have introduced the %1$s New Front-end Analytics %2$s which will fetch the stats like backend but with an awesome UI for front-end stats. It was a long awaited feature which was under development but finally It is here. It can be customized (if you are good at CSS) according to your website look and feel. %3$s below are it\'s important options: ', 'wp-analytify' ), '<em>', '</em>', '<br /><br />' );?>
				</p>

				<div  style="clear:both" class="feature-section col three-col">
					<div>
						<h4><?php esc_html_e( 'Disable front-end', 'wp-analytify' );?></h4>
						<p>
						<?php esc_html_e( 'We introduced the ability to disable the front-end stats on the full site. Check it, if you don\'t want to load Analytics at all. Remember, you can use Shortcodes still.', 'wp-analytify' );?>
						</p>

						<h4><?php esc_html_e( 'Display Analytics to Users', 'wp-analytify' );?></h4>
						<p>
						<?php echo sprintf( esc_html__( 'Show %1$s front-end %2$s Analytics to selected user roles only.', 'wp-analytify' ), '<em>', '</em>' );?>
						</p>
					</div>

					<div>
						<h4><?php esc_html_e( 'Analytics on Post types', 'wp-analytify' );?></h4>
						<p><?php esc_html_e( 'Show Analytics under the selected post types only. You can select your custom post types as well.' ,'wp-analytify' );?></p>

						<h4><?php esc_html_e( 'Front-end Analytics panels', 'wp-analytify' );?></h4>
						<p><?php esc_html_e( 'Select which Stats panels you want to display at front-end. We recommend to use upto 8 Panels for better page speed.', 'wp-analytify' );?></p>
					</div>

					<div class="last-feature">
						<h4><?php esc_html_e( 'Exclude Analytics on specific pages', 'wp-analytify' );?></h4>
						<p><?php esc_html_e( 'Select posts or pages on which you don\'t want to show Analytics.', 'wp-analytify' );?></p>
					</div>
				</div>
			</div>



			<div class="analytify-welcome-settings">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=analytify-settings') ); ?>"><?php esc_html_e( 'Go to Analytify Settings', 'wp-analytify' ); ?></a> &middot;
				<a href="http://analytify.io/changelog/" target="_blank"><?php esc_html_e( 'View the Full Changelog', 'wp-analytify' ); ?></a>
			</div>
		</div>
		<?php
	}

	/**
	 * Render Getting Started Screen
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */

	public function getting_started_screen() {

		?>

		<div class="wrap about-wrap">
			<h1><?php echo sprintf( esc_html__( 'Welcome to Analytify %1$s', 'wp-analytify'), ANALYTIFY_VERSION);?></h1>
			<div class="about-text">
			<?php echo sprintf( esc_html__( ' %1$s Thank you for updating to the latest version! %2$s %3$s Analytify %4$s is ready to make people lives easier for those who love Google Analytics and WordPress. It makes Analytics simple for WordPress users that everyone can understand about their Website statistics and behavior. ', 'wp-analytify' ), '<em>', '</em>', '<br />', ANALYTIFY_VERSION);?>
			</div>
			<div class="wp-analytify-badge"></div>

			<?php $this->tabs(); ?>

			<p class="about-description">
			<?php echo sprintf( esc_html__( 'Getting started with Analytify plugin settings are very easy to use and ready to go in minutes. If there is something you don\'t understand or confusing, do ask us on %1$sWordPress.org support forum%2$s without hesitation.', 'wp-analytify' ), ' <a href="http://wordpress.org/support/plugin/wp-analytify/" tagget="_blank">', '</a>');?>
			</p>

			<div class="changelog">
				<h3><?php echo sprintf( esc_html__( '%1$s Connect with Analytify %2$s', 'wp-analytify' ), '<a href="' . admin_url( 'admin.php?page=analytify-settings'). '" tagget="_blank">', '</a>' );?></h3>

				<div class="feature-section">
				 <img width="414" src="<?php echo plugins_url( 'assets/images/authenticate2.png', __FILE__ );?>" alt="" class="wp-analytify-welcome-screenshots">

					<h4><?php esc_html_e( 'Authentication', 'wp-analytify' );?></h4>
					<p><?php esc_html_e( 'You must have a registered Google Analytics account and setup your website profiles to use with Analytify on your WordPress based sites. Click on Login button and It will redirect you to separate Google access page. Allow this plugin to fetch the data from Google Analytics and It will take you back to your profile tab.', 'wp-analytify' );?></p>

				</div>
			</div>



			<div class="changelog">
				<h3><?php esc_html_e( 'Setting up profiles', 'wp-analytify' );?></h3>

				<div class="feature-section">
						<img src="<?php echo plugins_url( 'assets/images/profile-data.png', __FILE__ );?>" alt="" class="wp-analytify-welcome-screenshots">
					<h4><?php esc_html_e( 'Profile Tab', 'wp-analytify' );?></h4>
					<p><?php esc_html_e( 'This tab section consists of many important settings. After Authentication, Setup your profiles here. All of the options are easy to understand.', 'wp-analytify' );?></p>

					<h5><?php esc_html_e( 'a) Install Google Analytics tracking code', 'wp-analytify' );?></h5>
					<p><?php esc_html_e( 'First, you need to check if you want to install Google Analytics tracking code by Analytify or uncheck it if you have already installed it manually or via some other way. ', 'wp-analytify');?></p>

					<h5><?php esc_html_e( 'b) Exclude users from tracking', 'wp-analytify' );?></h5>
					<p><?php esc_html_e( 'Usually Administrators don\'t want to track themselves. So, this option can be commonly used if you want to exclude your special users from tracking data.', 'wp-analytify');?></p>

					<h5><?php esc_html_e( 'c) Tracking code type', 'wp-analytify' );?></h5>
					<p><?php esc_html_e( 'Google recommends to use Analytics.js (Universal Tracking) now. So, we recommend to use this option.', 'wp-analytify');?></p>

					<h5><?php esc_html_e( 'd) Profile for posts (Backend/Front-end)', 'wp-analytify' );?></h5>
					<p><?php esc_html_e( 'Select your website profile for wp-admin edit pages and Front-end pages. Select profile which matches your current WordPress website.', 'wp-analytify');?></p>

					<h5><?php esc_html_e( 'e) Profile for Dashboard', 'wp-analytify' );?></h5>
					<p><?php esc_html_e( 'Select your website profile for Dashboard Stats. You can select your any Website profile. It will show Analytics for your selected website profile.', 'wp-analytify');?></p>

				</div>
			</div>

			<div class="changelog">
				<h3><?php esc_html_e( 'Display Statistics at front-end pages', 'wp-analytify' );?></h3>

				<div class="feature-section col three-col">

					<img src="<?php echo plugins_url( 'assets/images/front-end.png', __FILE__ );?>" alt="" class="wp-analytify-welcome-screenshots">

					<h4><?php esc_html_e( 'Front Tab', 'wp-analytify' );?></h4>
					<p><?php echo sprintf( esc_html__( 'In version %4$s, We have introduced the %1$s New Front-end Analytics%2$s which will fetch the stats like backend but with an awesome UI for front-end stats. It was a long awaited feature which was under development since 1.0 but finally It is here. It can be customized (if you are good at CSS) according to your website look and feel. %3$s Below are it\'s important options: ', 'wp-analytify' ), '<em>', '</em>', '<br /><br />', ANALYTIFY_VERSION);?></p>

					<div style="clear:both;">
						<h4><?php esc_html_e( 'Disable front-end', 'wp-analytify' );?></h4>
						<p><?php esc_html_e( 'We introduced the ability to disable the front-end stats on the full site. Check it, if you don\'t want to load Analytics at all. Remember, you can use Shortcodes still.', 'wp-analytify' );?></p>

						<h4><?php esc_html_e( 'Display Analytics to Users', 'wp-analytify' );?></h4>
						<p><?php echo sprintf(esc_html__( 'Show %1$sfront-end%2$s Analytics to selected user roles only.', 'wp-analytify' ), '<em>', '</em>');?></p>
					</div>

					<div>
						<h4><?php esc_html_e( 'Analytics on Post types', 'wp-analytify' );?></h4>
						<p><?php esc_html_e( 'Show Analytics under the selected post types only. You can select your custom post types as well.' ,'wp-analytify' );?></p>

						<h4><?php esc_html_e( 'Front-end Analytics panels', 'wp-analytify' );?></h4>
						<p><?php esc_html_e( 'Select which Stats panels you want to display at front-end. We recommend to use upto 8 Panels for better page speed.', 'wp-analytify' );?></p>
					</div>

					<div class="last-feature">
						<h4><?php esc_html_e( 'Exclude Analytics on specific pages', 'wp-analytify' );?></h4>
						<p><?php esc_html_e( 'Select posts or pages on which you don\'t want to show Analytics.', 'wp-analytify' );?></p>
					</div>
				</div>
			</div>

			<div class="changelog">
				<h3><?php esc_html_e( 'Display Statistics under Edit pages (backend)', 'wp-analytify' );?></h3>

				<div class="feature-section col three-col">

					<img src="<?php echo plugins_url( 'assets/images/admin-end.png', __FILE__ );?>" alt="" class="wp-analytify-welcome-screenshots">

					<h4><?php esc_html_e( 'Admin Tab', 'wp-analytify' );?></h4>
					<p><?php esc_html_e( 'Admin tab section is to manage the Analytics panels under EDIT screen of post/pages in wp-admin.', 'wp-analytify' );?></p>


					<div style="clear:both">
						<h4><?php esc_html_e( 'Disable Analytics under posts/pages.', 'wp-analytify' );?></h4>
						<p><?php esc_html_e( 'Check it, If you don\'t want to load Stats by default on all pages. If you disable it, you can still fetch the stats under each post/page on single click.', 'wp-analytify' );?></p>

						<h4><?php esc_html_e( 'Display Analytics to Users', 'wp-analytify' );?></h4>
						<p><?php echo sprintf( esc_html__( 'Show %1$s Google %2$s Analytics to selected user roles only.', 'wp-analytify' ), '<em>', '</em>');?></p>
					</div>

					<div>
						<h4><?php esc_html_e( 'Analytics on Post types', 'wp-analytify' );?></h4>
						<p><?php esc_html_e( 'Show Analytics under the selected post types only. You can select your custom post types as well.' ,'wp-analytify' );?></p>

						<h4><?php esc_html_e( 'Edit pages/posts Analytics panels', 'wp-analytify' );?></h4>
						<p><?php esc_html_e( 'Select which Stats panels you want to display at backend. We recommend to use upto 10 Panels for better page speed.', 'wp-analytify' );?></p>
					</div>

					<div class="last-feature">
						<h4><?php esc_html_e( 'Exclude Analytics on specific pages', 'wp-analytify' );?></h4>
						<p><?php esc_html_e( 'Select posts or pages on which you don\'t want to show Analytics.', 'wp-analytify' );?></p>
					</div>

				</div>
			</div>

			<div class="changelog">
				<h3><?php esc_html_e( 'Licensing', 'wp-analytify' );?></h3>

				<div class="feature-section">
					<p><?php esc_html_e( 'We have a year based pricing model as we think this is the best model to keep our normal lives easier and down the costs of support servers etc. You need a valid license key if you want to get support and updates in the future for a year. After a year, you will get discount to renew your license. ', 'wp-analytify' );?></p>
				</div>
			</div>

			<div class="analytify-welcome-settings">
				<a href="<?php echo esc_url( admin_url( 'admin.php?page=analytify-settings') ); ?>"><?php esc_html_e( 'Go to Analytify Settings', 'wp-analytify' ); ?></a> &middot;
				<a href="http://analytify.io/changelog/" target="_blank"><?php esc_html_e( 'View the Full Changelog', 'wp-analytify' ); ?></a>
			</div>

		</div>
		<?php
	}

	/**
	 * Render Credits Screen
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function credits_screen() {
		list( $display_version ) = explode( '-', ANALYTIFY_VERSION );
		?>
		<div class="wrap about-wrap">
			<h1><?php echo sprintf( esc_html__( 'Welcome to Analytify %1$s', 'wp-analytify' ), ANALYTIFY_VERSION ); ?></h1>
			<div class="about-text">
			<?php echo sprintf( esc_html__( '%1$s Thank you for updating to the latest version! %2$s %3$s Analytify %4$s is ready to make people lives easier for those who love Google Analytics and WordPress. It makes Analytics simple for WordPress users that everyone can understand about their Website statistics and behavior. ', 'wp-analytify' ), '<em>', '</em>', '<br />', ANALYTIFY_VERSION);?>
			</div>
			<div class="wp-analytify-badge"></div>

			<?php $this->tabs(); ?>

			<p class="about-description"><?php echo sprintf( esc_html__( 'Analytify is developed by %1$s WPBrigade%2$s i.e brigade of WordPress developers.', 'wp-analytify' ), '<a href="http://wpbrigade.com/">', '</a>');?></p>
		</div>
		<?php
	}

	/**
	 * Sends user to the Welcome page on first activation of Analytify as well as each
	 * time Analytify is upgraded to a new version
	 *
	 * @access public
	 * @since 1.1
	 * @return void
	 */
	public function welcome() {
		
		$is_show = get_option( 'show_welcome_page' );

		if( ! $is_show ) { // First time install

			update_option( 'show_welcome_page' ,  1 );
			// wp_redirect( admin_url( 'index.php?page=wp-analytify-getting-started' ) );
			wp_redirect( admin_url( 'index.php?page=analytify-optin' ) );
			exit;
		}
	}
}
new Analytify_Welcome();
