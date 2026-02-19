<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit; // don't access directly
};

/**
 * Load the Main Multipage Class 
 *
 * @since 0.6
 */
class Multipage {

	/** Versions **************************************************************/

	public $version    = '';
	public $db_version = 0;
	public $db_version_raw = 0;

	/** Paths *****************************************************************/

	public $file       = '';
	public $basename   = '';
	public $plugin_dir = '';
	public $plugin_url = '';

	/** Multipage Data ********************************************************/

	public $mpp_data      = false;
	public $mpp_index     = false;
	public $page          = 0;
	public $page_title    = '';
	public $max_num_pages = 0;
	public $mpp_pagename  = '';

	/**
	 * Main Multipage Instance.
	 *
	 * @since 1.4
	 */
	public static function instance() {

		// Store the instance locally to avoid private static replication
		static $instance = null;

		// Only run these methods if they haven't been run previously
		if ( null === $instance ) {
			$instance = new Multipage;
			$instance->constants();
			$instance->setup_globals();
			$instance->includes();
			$instance->setup_actions();
			$instance->frontend_init();
		}

		// Always return the instance
		return $instance;
	}

	private function __construct() { /* Do nothing */ }
	
	/**
	 * Bootstrap constants.
	 *
	 * @since 1.4
	 *
	 */
	private function constants() {

		// Path and URL
		if ( ! defined( 'MPP_PLUGIN_DIR' ) ) {
			define( 'MPP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
		}

		if ( ! defined( 'MPP_PLUGIN_URL' ) ) {
			define( 'MPP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
		}
		
		// MPP pattern constant
		if ( ! defined( 'MPP_PATTERN' ) ) {
			define( 'MPP_PATTERN', '/\[nextpage[^\]]*\]/' );
			// /(<p>)?\[nextpage[^\]]*\](</p>)?/
		}
		
		// MPP Gutenberg constant
		if ( ! defined( 'MPP_GUTENBERG_PATTERN' ) ) {
			define( 'MPP_GUTENBERG_PATTERN', '/<!-- wp:multipage\/subpage[\s\S]*?<!-- \/wp:multipage\/subpage -->/' );
		}
	}

	/**
	 * Component global variables.
	 *
	 * @since 1.4
	 *
	 */
	private function setup_globals() {

		/** Versions **********************************************************/

		$this->version    = MPP_VERSION;
		$this->db_version = 1000;
		
		/** Paths *************************************************************/

		$this->file           = constant( 'MPP_PLUGIN_DIR' ) . 'sgr-nextpage-titles.php';
		$this->basename       = basename( constant( 'MPP_PLUGIN_DIR' ) ) . '/sgr-nextpage-titles.php';
		$this->plugin_dir     = trailingslashit( constant( 'MPP_PLUGIN_DIR' ) );
		$this->plugin_url     = trailingslashit( constant( 'MPP_PLUGIN_URL' ) );
		
		/** Multipage Data ****************************************************/
		
		$this->mpp_data	= false;
		$this->mpp_index = false;
	}

	/**
	 * Include required files.
	 *
	 * @since 1.4
	 *
	 */
	private function includes() {
		// Setup the versions.
		$this->versions();
		
		// Load the admin.
		if ( is_admin() ) {
			add_action( 'init', 'mpp_admin' );
		}

		require_once $this->plugin_dir . 'classes/class-mpp-admin.php';
		require_once $this->plugin_dir . 'classes/class-mpp-shortcodes.php';

		require_once $this->plugin_dir . 'inc/mpp-admin.php';
		require_once $this->plugin_dir . 'inc/mpp-functions.php';
		require_once $this->plugin_dir . 'inc/mpp-options.php';
		require_once $this->plugin_dir . 'inc/mpp-parser.php';
		require_once $this->plugin_dir . 'inc/mpp-shortcodes.php';
		require_once $this->plugin_dir . 'inc/mpp-template.php';
		require_once $this->plugin_dir . 'inc/mpp-update.php';
	}
	
	/**
	 * Set up the hooks, actions, and filters.
	 *
	 * @since 1.5
	 *
	 */
	private function setup_actions() { 

		// Add actions to plugin activation and deactivation hooks
		add_action( 'activate_'   . $this->basename, 'mpp_activation'   );
		add_action( 'deactivate_' . $this->basename, 'mpp_deactivation' );

		// If the plugin is being deactivated, do not add any actions.
		if ( mpp_is_deactivation( $this->basename ) ) {
			return;
		}

		// Add action on save post, moved outside is_admin to preserve working for Rest API (Gutenberg).
		add_action( 'save_post', array( $this, 'save_post' ), 10, 1 );
		
		// Add fitler pre_handle_404 in order to define if the page is 404.
		add_filter( 'pre_handle_404', array( $this, 'mpp_pre_handle_404' ), 100, 2 );

		// Prevent WordPress from redirecting valid multipage subpages to the base URL.
		add_filter( 'redirect_canonical', array( $this, 'mpp_redirect_canonical' ), 10, 2 );
	}

	private function frontend_init() {
		add_action( 'wp', array( $this, 'mpp_post' ) );
	}

	public function mpp_post() {
		global $wp_query;

		$post = isset( $wp_query->post ) ? $wp_query->post : null;
		
		// If is not singular return.
		if ( is_null( $post ) || empty( $post ) ) {
			return;
		}

		// No need to process
		if ( is_feed() || is_404() )
			return;
		
		// Check if it's not a Multipage Post
		if ( empty( $this->mpp_data ) )
			return;

		// Replace eventually existing variables on the first page.
		$custom_intro = 0;
		foreach ( $this->mpp_data as $link => $title ) {
			$this->mpp_data[ $link ] = str_replace( '%%intro%%', __( 'Intro', 'sgr-nextpage-titles' ), $title, $custom_intro );
			break;
		}

		$_mpp_page_keys = array_keys( $this->mpp_data );

		// Check whether or not to hide the standard WordPress pagination.
		if ( mpp_disable_standard_pagination() )
			add_filter( 'wp_link_pages_args', array( $this, 'hide_standard_pagination' ) );

		// Check whether or not to hide comments.
		if ( ( $this->page !== 0 && mpp_get_comments_on_page() === 'first-page' ) || ( $this->page !== count( $_mpp_page_keys ) && mpp_get_comments_on_page() === 'last-page' ) )
			add_filter( 'comments_template', array( $this, 'hide_comments' ) );

		// Initialize variables
		$content = $post->post_content;

		/**
		 * Correct the content if we have a starting nextpage tag.
		 * We also have to check if it's at the starting point because if it's not a Gutenberg page
		 * we have problems on classic editor. We could solve this checking if the page is Gutenberg
		 * or if the nextpage code is at the starting.
		 **/
		if ( $custom_intro === 0 && strpos( $content, '<!-- wp:multipage/subpage' ) === 0 ) { 
			$content = trim( substr( $content, strpos( $content, 'multipage/subpage -->' ) + strlen( 'multipage/subpage -->' ) ) );
		}

		// Replace Gutenberg block markers with standard nextpage markers.
		$result = preg_replace( MPP_GUTENBERG_PATTERN, '<!-- wp:nextpage -->
<!--nextpage-->
<!-- /wp:nextpage -->', $content);
		if ( null !== $result ) {
			$content = $result;
		}

		// Replace [nextpage] shortcodes with standard nextpage markers.
		$result = preg_replace( MPP_PATTERN, '<!--nextpage-->', $content );
		if ( null !== $result ) {
			$content = $result;
		}
		
		// Remove paragraph wrappers around nextpage markers.
		$content = str_replace( '<p><!--nextpage--></p>', '<!--nextpage-->', $content );
		
		// Update the $post Object with new data.
		$post->post_content = $content;
		
		// Update also $wp_query
		$wp_query->post = $post;

		// Update Object with current post data.
		$this->page_title = $this->mpp_data[ $this->mpp_pagename ];
		$this->max_num_pages = count( $this->mpp_data );

		// Change the document title only if it's not the first page.
		if ( $this->page > 1 ) {
			add_filter( 'wp_title',					array( $this, 'mpp_the_title' ), mpp_get_rewrite_title_priority(), 1 );
			add_filter( 'pre_get_document_title',	array( $this, 'mpp_the_title' ), mpp_get_rewrite_title_priority(), 1 );
			add_filter( 'document_title_parts',		array( $this, 'mpp_document_title_parts' ), mpp_get_rewrite_title_priority(), 1 );
		}
		add_filter( 'the_content', 			array( $this, 'mpp_the_content' ), mpp_get_rewrite_content_priority(), 1 );
		add_action( 'wp_enqueue_scripts',	array( $this, 'enqueue_styles' ) );
		add_action( 'wp_head',				array( $this, 'mpp_rel_links' ) );
	}

	/**
	 * Output rel=prev and rel=next links in the <head> for SEO.
	 *
	 * @since 1.5.13
	 */
	public function mpp_rel_links() {
		if ( empty( $this->mpp_data ) || ! is_singular() ) {
			return;
		}

		$num_pages = count( $this->mpp_data );

		if ( $this->page > 1 ) {
			$prev_url = 1 === ( $this->page - 1 ) ? get_permalink() : _mpp_link_page_url( $this->page - 1 );
			echo '<link rel="prev" href="' . esc_url( $prev_url ) . '" />' . "\n";
		}

		if ( $this->page < $num_pages ) {
			$next_url = _mpp_link_page_url( $this->page + 1 );
			echo '<link rel="next" href="' . esc_url( $next_url ) . '" />' . "\n";
		}
	}

	public function save_post( $post_id ) {
		// Skip revisions and autosaves.
		if ( wp_is_post_revision( $post_id ) || wp_is_post_autosave( $post_id ) ) {
			return;
		}

		$key = '_mpp_data';
		$post = get_post( $post_id );
		$post_content = $post->post_content;
		$_mpp_data = Multipage_Parser::multipage_return_array( $post_content );
		
		if ( empty( $_mpp_data ) ) {
			delete_post_meta( $post_id, $key );
			return;
		}
		
		// Add the post meta
		update_post_meta( $post_id, $key, $_mpp_data );
		return;
	}
	
	/**
	 * Delegates to Multipage_Parser.
	 *
	 * @deprecated 1.5.14 Use Multipage_Parser::multipage_return_array() directly.
	 */
	public static function multipage_return_array( $content ) {
		return Multipage_Parser::multipage_return_array( $content );
	}
	
	/**
	 * Hide the standard pagination.
	 *
	 * @since 0.6
	 */
	public static function hide_standard_pagination( $args ) {
		$args['echo'] = 0;
		return $args;
	}
	
	/**
	 * Private method to align the active and database versions.
	 *
	 * @since 1.4
	 */
	private function versions() {
		// Get the possible DB versions.
		$versions               = array();
		$versions['1.3']		= false !== get_option( 'multipage' ) ? 999 : null; // If we found a multipage option then it's an update from the 1.3 version.
		
		// Remove empty array items
		$versions				= array_filter( $versions );
		$this->db_version_raw	= (int) ( !empty( $versions ) ) ? (int) max( $versions ) : 0;
	}

	/**
	 * Filter the document title for Multipage pages.
	 *
	 * @since 0.6
	 *
	 * @see wp_title()
	 *
	 * @param string $title       Original page title.
 	 * @return string the title modified by Multipage.
	 */
	public function mpp_the_title( $title ) {
		// Manipulate WordPress SEO by Yoast custom title, if active.
		if ( defined( 'WPSEO_VERSION' ) ) {
			$title = str_replace( sprintf( __( 'Page %1$d of %2$d', 'wordpress-seo' ), $this->page, $this->max_num_pages ), $this->page_title, $title );
		}

		return $title;
	}

	/**
	 * Filter the document title for Multipage pages.
	 *
	 * @since 1.4
	 *
	 * @param array $title The WordPress document title parts.
	 * @return array the title parts modified by Multipage.
	 */
	public function mpp_document_title_parts( $title ) {	
		// Change the page title.
		$title['page'] = $this->page_title;
		return $title;
	}

	/**
	 * Filter the WordPress post content for Multipage pages.
	 *
	 * @since 0.6
	 *
	 * @param string $content       Original page content.
 	 * @return string the content enhanced by Multipage.
	 */
	public function mpp_the_content( $content ) {
		// Table of contents should not be the only content in the post.
		if ( ! $content )
			return $content;
			
		// Only on single posts.
		if ( ! is_singular() )
			return $content;
		
		$page_title_template = apply_filters( 'mpp_page_title_template', '<h2>%s</h2>' );
		$page_title = mpp_hide_intro_title() && $this->page === 0 ? '' : sprintf( $page_title_template, esc_html( $this->page_title ) );
		$toc_labels = mpp_get_toc_row_labels();

		switch ( $toc_labels ) {
			case 'page':
				$toc_row_separator = ': ';
				$toc_row_pagelink = __( 'Page %', 'sgr-nextpage-titles' );
				break;
			case 'hidden':
				$toc_row_separator = '';
				$toc_row_pagelink = '';
				break;
			default:
				$toc_row_separator = '. ';
				$toc_row_pagelink = '%';
				break;
		}

		$continue_or_prev_next = mpp_get_continue_or_prev_next();
		if ( $continue_or_prev_next !== 'hidden' ) {
			$navigation = mpp_link_pages( $this, array(
				'before'				=> '<nav class="mpp-post-navigation ' . sanitize_html_class( $continue_or_prev_next ) . '" role="navigation"><div class="nav-links">',
				'after'					=> '</div><!-- .nav-links --></nav><!-- .mpp-post-navigation -->',
				'continue_or_prev_next'	=> $continue_or_prev_next
			) );
		} else {
			$navigation = '';
		}
		
		// Get comments link
		$comments_link = '';
		if ( mpp_comments_toc_link() ) {
			switch ( mpp_get_comments_on_page() ) {
				case 'all':
					$comments_link = '<a href="#comments">';
					break;
				case 'first-page':
					$comments_link = _mpp_link_page( 1, 'comments' );
					break;
				case 'last-page':
					$comments_link = _mpp_link_page( $this->max_num_pages, 'comments' );
					break;
				default:
					$comments_link = '';
					break;
			}
		}

		$toc = mpp_toc( $this, array(
			'hide_header'	=> mpp_hide_toc_header(),
			'comments'		=> $comments_link,
			'position'		=> mpp_get_toc_position(),
			'before'		=> '<nav class="mpp-toc toc"><ul>',
			'after'			=> '</ul></nav>',
			'separator'		=> $toc_row_separator,
			'pagelink'		=> $toc_row_pagelink,
		) );
		
		// Add the title
		$output = $page_title;
		
		// Add the table of content
		if ( mpp_get_toc_position() === 'bottom' ) {
			$output .= $content . $toc;
		} elseif ( mpp_get_toc_position() === 'hidden' || ( mpp_toc_only_on_the_first_page() && $this->page > 1 ) ) {
			$output .= $content;
		} else {
			$output .= $toc . $content;
		}

		/*
		 * Filters the multipage post content.
		 *
		 * @since 1.5.4
		 *
		 * @param string $output		The enhanced content.
		 * @param string $page_title	The subpage title.
		 * @param array  $toc_labels	The table of contents html.
		 * @param array  $content		The original content.
		 */
		$output = apply_filters( 'mpp_the_content', $output, $page_title, $toc, $content );
		
		// Add the page navigation
		$output .= $navigation;

		return $output;
	}
	
	/**
	 * Hide comments area.
	 *
	 * @since 1.0
	 */
	public function hide_comments() {
		// Return an empty file.
		return MPP_PLUGIN_DIR . 'index.php';
	}
	
	/**
	 * Styles applied to public-facing pages
	 *
	 * @since 0.6
	 * @uses enqueue_styles()
	 */
	public function enqueue_styles() {
		// LTR or RTL
		$file = is_rtl() ? 'css/multipage-rtl' : 'css/multipage';
		
		// Use minified libraries if SCRIPT_DEBUG is turned off
		$suffix  = ( defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ) ? '' : '.min';
		
		// Add extension
		$file .= $suffix . '.css';
		
		// Check child theme
		if ( file_exists( trailingslashit( get_stylesheet_directory() ) . $file ) ) {
			$location = trailingslashit( get_stylesheet_directory_uri() );
			$handle   = get_stylesheet_directory() . '-multipage';

		// Check parent theme
		} elseif ( file_exists( trailingslashit( get_template_directory() ) . $file ) ) {
			$location = trailingslashit( get_template_directory_uri() );
			$handle   = get_template_directory() . '-multipage';

		// Multipage Plugin Default Style
		} else {
			$location = trailingslashit( MPP_PLUGIN_URL );
			$handle   = 'multipage';
		}

		// Enqueue the Multipage Plugin styling
		wp_enqueue_style( $handle, $location . $file, array(), $this->version, 'screen' );
	}
	
	/**
	 * Prevent WordPress canonical redirect from redirecting valid multipage subpages.
	 *
	 * WordPress redirect_canonical checks if page number exceeds the number of
	 * <!--nextpage--> markers in post_content. Since the plugin injects these markers
	 * dynamically in mpp_post(), they may not yet be present when redirect_canonical runs,
	 * or the post object used by get_post() may hold the original content.
	 *
	 * @since 1.5.13
	 *
	 * @param string $redirect_url  The redirect URL.
	 * @param string $requested_url The requested URL.
	 * @return string|false The redirect URL, or false to cancel the redirect.
	 */
	public function mpp_redirect_canonical( $redirect_url, $requested_url ) {
		if ( ! empty( $this->mpp_data ) && is_singular() ) {
			$page = (int) get_query_var( 'page', 0 );
			if ( $page > 0 && $page <= count( $this->mpp_data ) ) {
				return false;
			}
		}
		return $redirect_url;
	}

	/**
	 * Filters whether to short-circuit default header status handling.
	 *
	 * Returning a non-false value from the filter will short-circuit the handling
	 * and return early.
	 *
	 * @since 1.6
	 *
	 * @param bool     $preempt  Whether to short-circuit default header status handling. Default false.
	 * @param WP_Query $wp_query WordPress Query object.
	 */
	public function mpp_pre_handle_404( $preempt, $wp_query ) {
		$post = isset( $wp_query->post ) ? $wp_query->post : null;

		// If is not singular return.
		if ( is_null( $post ) || ! is_singular() ) {
			return $preempt;
		}
		
		// Check if it's a Multipage Post
		$this->mpp_data = get_post_meta( $post->ID, '_mpp_data', true );
		if ( empty( $this->mpp_data ) )
			return $preempt;
		
		// Only set X-Pingback for single posts that allow pings.
		if ( $post && pings_open( $post ) && ! headers_sent() ) {
			header( 'X-Pingback: ' . get_bloginfo( 'pingback_url', 'display' ) );
		}

		$_mpp_page_keys = array_keys( $this->mpp_data );
		$this->page = $wp_query->query_vars['page'];
		$this->mpp_index = $this->page > 1 ? $this->page -1 : 0;

		// If the page doesn't exist redirect to the first page.
		if ( $this->mpp_index >= count( $_mpp_page_keys ) ) {
			return $preempt;
		}

		$this->mpp_pagename = $_mpp_page_keys[ $this->mpp_index ];

		return true;
	}
}
