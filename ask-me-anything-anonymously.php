<?php
/*
Plugin Name: Ask Me Anything (Anonymously)
Plugin URI: http://millionclues.com
Description: Let your visitors as you questions anonymously and list all questions in a neat list. 
Author: Arun Basil Lal
Author URI: http://millionclues.com
Version: 1.6
Text Domain: ask-me-anything-anonymously
Domain Path: /languages
License: GPL v2 - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
*/

/*------------------------------------------*/
/*			Plugin Setup Functions			*/
/*------------------------------------------*/

// Exit If Accessed Directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Add Admin Menu Pages
function amamc_add_menu_links() {
	add_options_page ( __('Ask Me Anything','ask-me-anything-anonymously'), __('Ask Me Anything','ask-me-anything-anonymously'), 'moderate_comments', 'ask-me-anything-shortcode-generator','amamc_admin_interface_render'  );
}
add_action( 'admin_menu', 'amamc_add_menu_links' );

// Print Direct Link To Plugin Settings In Plugins List In Admin
function amamc_settings_link( $links ) {
	return array_merge(
		array(
			'settings' => '<a href="' . admin_url( 'options-general.php?page=ask-me-anything-shortcode-generator' ) . '">' . __( 'Generate Shortcode', 'ask-me-anything-anonymously' ) . '</a>'
		),
		$links
	);
}
add_filter( 'plugin_action_links_' . plugin_basename(__FILE__), 'amamc_settings_link' );

// Add Donate Link to Plugins list
function amamc_plugin_row_meta( $links, $file ) {
	if ( strpos( $file, 'amamc_ask-me-anything.php' ) !== false ) {
		$new_links = array(
				'donate' => '<a href="http://millionclues.com/donate/" target="_blank">Donate</a>',
				'hireme' => '<a href="http://millionclues.com/portfolio/" target="_blank">Hire Me For A Project</a>',
				);
		$links = array_merge( $links, $new_links );
	}
	return $links;
}
add_filter( 'plugin_row_meta', 'amamc_plugin_row_meta', 10, 2 );

// Load Text Domain
function amamc_load_plugin_textdomain() {
    load_plugin_textdomain( 'ask-me-anything-anonymously', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'amamc_load_plugin_textdomain' );

// Register Settings
function amamc_register_settings() {
	register_setting( 'amamc_settings_group', 'amamc_custom_css', 'amamc_clean_css_with_csstidy' );
}
add_action( 'admin_init', 'amamc_register_settings' );

// Delete Options During Uninstall
function amamc_uninstall_plugin() {
	delete_option( 'amamc_custom_css' );
}
register_uninstall_hook(__FILE__, 'amamc_uninstall_plugin' );

/*--------------------------------------*/
/*			Admin Options Page			*/
/*--------------------------------------*/

// Load Syntax Highlighter
function amamc_register_highlighter( $hook ) {
	if ( 'settings_page_ask-me-anything-shortcode-generator' === $hook ) {
		wp_enqueue_style( 'highlighter-css', plugins_url( basename( dirname( __FILE__ ) ) . '/inc/highlighter/codemirror.css' ) );
		wp_enqueue_script( 'highlighter-js', plugins_url( basename( dirname( __FILE__ ) ) . '/inc/highlighter/codemirror.js' ), array(), '20140329', true );
		wp_enqueue_script( 'highlighter-css-js', plugins_url( basename( dirname( __FILE__ ) ) . '/inc/highlighter/css.js' ), array(), '20140329', true );
	}
}
add_action( 'admin_enqueue_scripts', 'amamc_register_highlighter' );

// Sanitize CSS with CSS Tidy - Uses CSS Tidy Modified By The Jetpack Team. 
function amamc_clean_css_with_csstidy ( $input ) {
	$input['amamc_admin_css'] 		= amamc_csstidy_helper ( $input['amamc_admin_css'] );
	return $input;
}

// Scrub And Clean With CSS Tidy
function amamc_csstidy_helper ( $css, $minify=false ) {
	
	include_once('inc/csstidy/class.csstidy.php');
	
	$csstidy = new csstidy();
	$csstidy->set_cfg( 'remove_bslash',              false );
	$csstidy->set_cfg( 'compress_colors',            false );
	$csstidy->set_cfg( 'compress_font-weight',       false );
	$csstidy->set_cfg( 'optimise_shorthands',        0 );
	$csstidy->set_cfg( 'remove_last_;',              false );
	$csstidy->set_cfg( 'case_properties',            false );
	$csstidy->set_cfg( 'discard_invalid_properties', true );
	$csstidy->set_cfg( 'css_level',                  'CSS3.0' );
	$csstidy->set_cfg( 'preserve_css',               true );
	
	if ($minify === false) {
		$csstidy->set_cfg( 'template', dirname( __FILE__ ) . '/inc/csstidy/wordpress-standard.tpl' );
	} else {
		$csstidy->set_cfg( 'template', 'highest');
	}
	
	$css = preg_replace( '/\\\\([0-9a-fA-F]{4})/', '\\\\\\\\$1', $css );
	$css = str_replace( '<=', '&lt;=', $css );
	$css = wp_kses_split( $css, array(), array() );
	$css = str_replace( '&gt;', '>', $css ); // kses replaces lone '>' with &gt;
	$css = strip_tags( $css );
	
	$csstidy->parse( $css );
	$css = $csstidy->print->plain();

	return $css;
}

// Admin Interface Renderer
function amamc_admin_interface_render () { ?>
	<div class="wrap">
	
		<h1><?php _e('Ask Me Anything Shortcode Generator And Settings','ask-me-anything-anonymously') ?></h1>
		<p><?php _e('You can choose your settings below and create a shortcode to display the Ask Me Anything form.','ask-me-anything-anonymously') ?></p>
		
		<?php
		// Get the tab query variable. If it's not set, set it to the first tab 
		$active_tab = isset( $_GET[ 'tab' ] ) ? $_GET[ 'tab' ] : 'generator';
		?>
		
		<h2 class="nav-tab-wrapper">
			<a class="nav-tab <?php echo $active_tab == 'generator' ? 'nav-tab-active' : ''; ?>" href="?page=ask-me-anything-shortcode-generator&tab=generator">Shortcode Generator</a>
			<a class="nav-tab <?php echo $active_tab == 'settings' ? 'nav-tab-active' : ''; ?>" href="?page=ask-me-anything-shortcode-generator&tab=settings">Custom CSS</a>
		</h2>
		
		<?php if( $active_tab == 'generator' ) { ?>
	
			<script type="text/javascript">
			function amamc_make_shortcode(){
			
				var post_or_page_id_attribute 		= '';
				var latest_first_attribute			= '';
				var anonymous_name_attribute		= '';
				var success_text_attribute			= '';
				var question_box_title_attribute 	= '';
				var placeholder_attribute			= '';
				var	test_question_attribute			= '';
				var test_answer_attribute			= '';
				var test_placeholder_attribute		= '';
				var ask_button_text_attribute		= '';
				var answer_list_title_attribute		= '';
				var no_answers_text_attribute		= '';
				var questions_per_page_attribute	= '';
				var avatar_attribute				= '';
				var avatar_size_attribute			= '';
				var show_question_box_attribute		= '';
				var show_answers_attribute			= '';
				var show_navigation_attribute		= '';
				var show_test_question_attribute	= '';
				var show_do_action_attribute		= '';
				var give_thanks_attribute			= '';
				var post_or_page_id 				= '';
				var latest_first 					= '';
				var anonymous_name					= '';
				var success_text					= '';
				var question_box_title 				= '';
				var placeholder						= '';
				var	test_question					= '';
				var test_answer						= '';
				var test_placeholder				= '';
				var ask_button_text					= '';
				var answer_list_title				= '';
				var no_answers_text					= '';
				var questions_per_page				= '';
				var avatar							= '';
				var avatar_size						= '';
				var show_question_box				= '';
				var show_answers					= '';
				var show_navigation					= '';
				var show_test_question				= '';
				var show_do_action					= '';
				var give_thanks						= '';
				
				document.getElementById("ama-post-page-id-hidden").style.display = 'none';
				document.getElementById("ama-hidden-thanks").style.display = 'none';
				
				if ( document.getElementById('loop_or_sidebar').value == 'loop' ) {
					document.getElementById('post_or_page_id').value = '0';
				}
				if ( document.getElementById('loop_or_sidebar').value == 'sidebar' ) {
					document.getElementById("ama-post-page-id-hidden").style.display = 'table-row';
				}
				if ( ( attribute_value = document.getElementById('post_or_page_id').value ) != 0 ) {
					post_or_page_id_attribute 	= ' post_or_page_id=';
					post_or_page_id 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('latest_first').value ) != 'true' ) {
					latest_first_attribute 		= ' latest_first=';
					latest_first 				= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('anonymous_name').value ) != 'Someone' ) {
					anonymous_name_attribute 	= ' anonymous_name=';
					anonymous_name 				= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('success_text').value ) != 'Your question is saved and will appear when it is answered.' ) {
					success_text_attribute 		= ' success_text=';
					success_text 				= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('question_box_title').value ) != 'Your Question' ) {
					question_box_title_attribute= ' question_box_title=';
					question_box_title 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('placeholder').value ) != 'In the spirit of keeping it anonymous, please do not leave any personal information.' ) {
					placeholder_attribute 		= ' placeholder=';
					placeholder 				= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('test_question').value ) != 'What is 7+5?' ) {
					test_question_attribute 	= ' test_question=';
					test_question	 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('test_answer').value ) != '12' ) {
					test_answer_attribute 		= ' test_answer=';
					test_answer		 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('test_placeholder').value ) != 'Enter your answer.' ) {
					test_placeholder_attribute 	= ' test_placeholder=';
					test_placeholder 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('ask_button_text').value ) != 'Ask Anonymously' ) {
					ask_button_text_attribute 	= ' ask_button_text=';
					ask_button_text 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('answer_list_title').value ) != 'Answers So Far..' ) {
					answer_list_title_attribute = ' answer_list_title=';
					answer_list_title 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('no_answers_text').value ) != 'Be the first to ask!' ) {
					no_answers_text_attribute 	= ' no_answers_text=';
					no_answers_text 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('questions_per_page').value ) != 10 ) {
					questions_per_page_attribute= ' questions_per_page=';
					questions_per_page 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('avatar').value ) != 'monsterid' ) {
					avatar_attribute 			= ' avatar=';
					avatar 						= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('avatar_size').value ) != 96 ) {
					avatar_size_attribute 		= ' avatar_size=';
					avatar_size 				= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('show_question_box').value ) != 'true' ) {
					show_question_box_attribute = ' show_question_box=';
					show_question_box 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('show_answers').value ) != 'true' ) {
					show_answers_attribute 		= ' show_answers=';
					show_answers	 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('show_navigation').value ) != 'true' ) {
					show_navigation_attribute 	= ' show_navigation=';
					show_navigation 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('show_test_question').value ) != 'true' ) {
					show_test_question_attribute= ' show_test_question=';
					show_test_question 			= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('show_do_action').value ) != 'false' ) {
					show_do_action_attribute 	= ' show_do_action=';
					show_do_action 				= '"'+attribute_value+'"';
				}
				if ( ( attribute_value = document.getElementById('give_thanks').value ) != 'false' ) {
					give_thanks_attribute 		= ' give_thanks=';
					give_thanks 				= '"'+attribute_value+'"';
					document.getElementById("ama-hidden-thanks").style.display = 'block';
				}
				
				document.getElementById("amamc_shortcode").value = '[askmeanythingpeople'+post_or_page_id_attribute+post_or_page_id+latest_first_attribute+latest_first+anonymous_name_attribute+anonymous_name+success_text_attribute+success_text+question_box_title_attribute+question_box_title+placeholder_attribute+placeholder+test_question_attribute+test_question+test_answer_attribute+test_answer+test_placeholder_attribute+test_placeholder+ask_button_text_attribute+ask_button_text+answer_list_title_attribute+answer_list_title+no_answers_text_attribute+no_answers_text+questions_per_page_attribute+questions_per_page+avatar_attribute+avatar+avatar_size_attribute+avatar_size+show_question_box_attribute+show_question_box+show_answers_attribute+show_answers+show_navigation_attribute+show_navigation+show_test_question_attribute+show_test_question+show_do_action_attribute+show_do_action+give_thanks_attribute+give_thanks+']';
			}
			</script>
			
			<form novalidate="novalidate" method="post" enctype="multipart/form-data">
				
				<h2 class="title"><?php _e('Your Shortcode','ask-me-anything-anonymously') ?></h2>
				
				<p><label for="amamc_shortcode"><?php _e('Copy the following shortcode into a post or page or sidebar text widget to generate the Ask Me Anything page.','ask-me-anything-anonymously') ?></label></p>
				<textarea rows="5" class="large-text code" id="amamc_shortcode" name="amamc_shortcode"><?php echo '[askmeanythingpeople]'; ?></textarea>
				
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><label for="loop_or_sidebar"><?php _e('Choose Location','ask-me-anything-anonymously') ?></label></th>
							<td>
								<select id="loop_or_sidebar" name="loop_or_sidebar" onchange="amamc_make_shortcode()">
									<option value="loop" selected="selected">Page / Post</option>
									<option value="sidebar">Sidebar Widget</option>
								</select>
								<p class="description"><?php _e('Choose the location where you will be using the shortcode.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr id="ama-post-page-id-hidden" style="display:none;">
							<th scope="row"><label for="post_or_page_id"><?php _e('Page / Post ID','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="0" id="post_or_page_id" name="post_or_page_id" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('Questions are saved as WordPress comments and all comments need to be associated with a page or a post. 
								<br>- First, generate a shortcode with location set as "Page / Post" and add it to a page or a post. 
								<br>- Then generate a second shortcode to use on a "Sidebar Widget" and enter the ID of the page or post from the previous step. Use this shortcode on the sidebar.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="latest_first"><?php _e('Answers List Order','ask-me-anything-anonymously') ?></label></th>
							<td>
								<select id="latest_first" name="latest_first" onchange="amamc_make_shortcode()">
									<option value="true" selected="selected"><?php _e('Display Newest Question First','ask-me-anything-anonymously') ?></option>
									<option value="false"><?php _e('Display Oldest Question First','ask-me-anything-anonymously') ?></option>
								</select>
								<p class="description"><?php _e('Choose the order in which questions are to be displayed.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="anonymous_name"><?php _e('Author Name','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="<?php _e('Someone','ask-me-anything-anonymously') ?>" id="anonymous_name" name="anonymous_name" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('This will be the name of every question author.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="success_text"><?php _e('Success Message','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="<?php _e('Your question is saved and will appear when it is answered.','ask-me-anything-anonymously') ?>" id="success_text" name="success_text" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('Enter a message that will be displayed when a user submits a question successfully.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="question_box_title"><?php _e('Question Box Title','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="<?php _e('Your Question','ask-me-anything-anonymously') ?>" id="question_box_title" name="question_box_title" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('Enter the title of the question box.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="placeholder"><?php _e('Question Box Placeholder','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="<?php _e('In the spirit of keeping it anonymous, please do not leave any personal information.','ask-me-anything-anonymously') ?>" id="placeholder" name="placeholder" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('Enter the text to be displayed within the question box as a placeholder.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="test_question"><?php _e('Verification Question','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="<?php _e('What is 7+5?','ask-me-anything-anonymously') ?>" id="test_question" name="test_question" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('Enter a test question to verify the user is human.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="test_answer"><?php _e('Verification Answer','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="12" id="test_answer" name="test_answer" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('Enter the answer to the verification question.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="test_placeholder"><?php _e('Verification Box Placeholder','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="Enter your answer." id="test_placeholder" name="test_placeholder" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('Enter the text to be displayed within the verification answer box as a placeholder.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="ask_button_text"><?php _e('Submit Button Text','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="<?php _e('Ask Anonymously','ask-me-anything-anonymously') ?>" id="ask_button_text" name="ask_button_text" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('Enter the text to be displayed on the form submit button.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="answer_list_title"><?php _e('Answers List Title','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="<?php _e('Answers So Far..','ask-me-anything-anonymously') ?>" id="answer_list_title" name="answer_list_title" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('Enter the title to the list of questions and answers.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="no_answers_text"><?php _e('No Questions Text','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="<?php _e('Be the first to ask!','ask-me-anything-anonymously') ?>" id="no_answers_text" name="no_answers_text" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('Enter the text to be displayed when there are no questions yet.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="questions_per_page"><?php _e('Question Per Page','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="10" id="questions_per_page" name="questions_per_page" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('Enter the number of questions per page.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="avatar"><?php _e('Avatar Image','ask-me-anything-anonymously') ?></label></th>
							<td>
								<select id="avatar" name="avatar" onchange="amamc_make_shortcode()">
									<option value="monsterid" selected="selected">Monster</option>
									<option value="retro">8 Bit</option>
									<option value="blank">Blank</option>
									<option value="wavatar">Cartoon Face</option>
									<option value="indenticon">Identicon</option>
									<option value="mysteryman">Mystery Man</option>
								</select>
								<p class="description"><?php _e('Choose the avatar image to be displayed for questions.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="avatar_size"><?php _e('Avatar Size','ask-me-anything-anonymously') ?></label></th>
							<td>
								<input type="text" class="regular-text" value="96" id="avatar_size" name="avatar_size" onchange="amamc_make_shortcode()">
								<p class="description"><?php _e('Enter the size of the avatar image. 0 to hide. Maximum value is 512.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="show_question_box"><?php _e('Show Question Box','ask-me-anything-anonymously') ?></label></th>
							<td>
								<select id="show_question_box" name="show_question_box" onchange="amamc_make_shortcode()">
									<option value="true" selected="selected">Show</option>
									<option value="false">Hide</option>
								</select>
								<p class="description"><?php _e('Show or hide the question box. Hide if you want to display just a list of questions, on the sidebar (for example).','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="show_answers"><?php _e('Show Questions List','ask-me-anything-anonymously') ?></label></th>
							<td>
								<select id="show_answers" name="show_answers" onchange="amamc_make_shortcode()">
									<option value="true" selected="selected">Show</option>
									<option value="false">Hide</option>
								</select>
								<p class="description"><?php _e('Show or hide the questions (and answers). Hide if you want to display just the question box, on the sidebar (for example).','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="show_navigation"><?php _e('Show Navigation','ask-me-anything-anonymously') ?></label></th>
							<td>
								<select id="show_navigation" name="show_navigation" onchange="amamc_make_shortcode()">
									<option value="true" selected="selected">Show</option>
									<option value="false">Hide</option>
								</select>
								<p class="description"><?php _e('Show or hide the navigation. Navigation is automatically hidden if the questions list is hidden.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="show_test_question"><?php _e('Show Test Question','ask-me-anything-anonymously') ?></label></th>
							<td>
								<select id="show_test_question" name="show_test_question" onchange="amamc_make_shortcode()">
									<option value="true" selected="selected">Show</option>
									<option value="false">Hide</option>
								</select>
								<p class="description"><?php _e('Show or hide the test question. Hide if you have other ways to check for spam, like a captcha plugin.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="show_do_action"><?php _e('Additional Form Elements','ask-me-anything-anonymously') ?></label></th>
							<td>
								<select id="show_do_action" name="show_do_action" onchange="amamc_make_shortcode()">
									<option value="true">Show</option>
									<option value="false" selected="selected">Hide</option>
								</select>
								<p class="description"><?php _e('Show or hide additional form elements added by other plugins. For instance, captcha plugins add a captcha to the default comment form.','ask-me-anything-anonymously') ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><label for="give_thanks"><?php _e('Show Credit Link','ask-me-anything-anonymously') ?></label></th>
							<td>
								<p class="description" id="ama-hidden-thanks" style="display:none; color:#E14D43;"><?php _e('Thank you! I appreciate that.','ask-me-anything-anonymously') ?></p>
								<select id="give_thanks" name="give_thanks" onchange="amamc_make_shortcode()">
									<option value="true">Show</option>
									<option value="false" selected="selected">Hide</option>
								</select>
								<p class="description"><?php printf(__('If you like this plugin, show your appreciation by displaying a tiny link to <a href="%s" target="_blank">my website</a> at the bottom of the page.','ask-me-anything-anonymously'),'http://millionclues.com/'); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
			</form>
			
		<?php }
		
		else { // i.e $active_tab == 'settings' ?>
		
			<h2><?php _e('Custom CSS','ask-me-anything-anonymously') ?></h2>
			<p><?php _e('To customize your AMA page add your custom CSS here.','ask-me-anything-anonymously') ?></p>
			
			<?php 
			// Load CSS From Database
			if ( is_multisite() ) {
				$amamc_custom_css_option = get_blog_option( get_current_blog_id(), 'amamc_custom_css' );
			}
			else {
				$amamc_custom_css_option = get_option( 'amamc_custom_css' );
			}
			
			$amamc_admin_css_content = isset( $amamc_custom_css_option['amamc_admin_css'] ) && ! empty( $amamc_custom_css_option['amamc_admin_css'] ) ? $amamc_custom_css_option['amamc_admin_css'] : __( "/* Custom CSS For Ask Me Anything Page*/\r\n", 'ask-me-anything-anonymously' );
			?>
			
			<form method="post" action="options.php" method="post" enctype="multipart/form-data">
			
				<?php settings_fields( 'amamc_settings_group' ); ?>

				<textarea rows="10" class="large-text code" id="amamc_custom_css[amamc_admin_css]" name="amamc_custom_css[amamc_admin_css]"><?php echo esc_textarea( $amamc_admin_css_content ); ?></textarea>
				
				<div style="margin-top: 5px;">
					<input type="checkbox" name="amamc_custom_css[minfy_css]" id="amamc_custom_css[minfy_css]" value="1"
						<?php if ( isset( $amamc_custom_css_option['minfy_css'] ) ) { checked( '1', $amamc_custom_css_option['minfy_css'] ); } ?>>
						<label for="amamc_custom_css[minfy_css]" style="vertical-align: unset;"><?php _e('Minify output', 'ask-me-anything-anonymously') ?></label>
				</div>
				
				<?php submit_button( __( 'Save CSS', 'ask-me-anything-anonymously' ), 'primary', 'submit', true ); ?>
			</form>
			
			<?php // Highlighter ?>
			<script language="javascript">
				jQuery( document ).ready( function() {
					var editor_admin_css = CodeMirror.fromTextArea( document.getElementById( "amamc_custom_css[amamc_admin_css]" ), {lineNumbers: true, lineWrapping: true} );
				});
			</script>
		
		<?php } ?>
		
	</div><?php
}

/*--------------------------------------*/
/*			Plugin Operations			*/
/*--------------------------------------*/

// Generate Shortcode: [askmeanythingpeople]
function amamc_do_askmeanythingpeople( $atts ) {

	// Attributes
	extract( shortcode_atts(
		array(
			'post_or_page_id'	=> 0,
			'latest_first' 		=> true,
			'anonymous_name'	=> __('Someone','ask-me-anything-anonymously'),
			'success_text'		=> __('Your question is saved and will appear when it is answered.','ask-me-anything-anonymously'),
			'question_box_title'=> __('Your Question','ask-me-anything-anonymously'),
			'placeholder'		=> __('In the spirit of keeping it anonymous, please do not leave any personal information.','ask-me-anything-anonymously'),
			'test_question'		=> __('What is 7+5?','ask-me-anything-anonymously'),
			'test_answer'		=> __('12','ask-me-anything-anonymously'),
			'test_placeholder'	=> __('Enter your answer.','ask-me-anything-anonymously'),
			'ask_button_text'	=> __('Ask Anonymously','ask-me-anything-anonymously'),
			'answer_list_title'	=> __('Answers So Far..','ask-me-anything-anonymously'),
			'no_answers_text'	=> __('Be the first to ask!','ask-me-anything-anonymously'),
			'questions_per_page'=> 10,
			'avatar'			=> 'monsterid', // 'retro' (8bit), 'monsterid' (monster), 'wavatar' (cartoon face), 'indenticon', 'mysteryman', 'blank', 'gravatar_default'
			'avatar_size'		=> 96,			// max is 512
			'show_question_box'	=> true,
			'show_answers'		=> true,
			'show_navigation'	=> true,
			'show_test_question'=> true,
			'show_do_action'	=> false,
			'give_thanks'		=> false,
		), $atts )
	);

	// Code
	ob_start(); // This function returns everything from here until ob_get_clean() ?>
	
		<?php 
		
		if ( $post_or_page_id == 0 && !in_the_loop() ) {
			_e('The attribute post_or_page_id must be set to use on the sidebar and outside the WordPress loop. <br><br>Please refer Installation instructions.','ask-me-anything-anonymously');
		}
		else if ( $post_or_page_id != 0 || in_the_loop() ) {
			
			if ( $post_or_page_id == 0 ) {
				$post_or_page_id = get_the_ID();	// Assign current page or post id if post_or_page_id attribute is not set. 
			}
		
			// Load The CSS To Style The AMA Form
			wp_enqueue_style('amamc_ask-me-anything', plugin_dir_url( __FILE__ ) . 'css/amamc_ask-me-anything.css'); 
			amamc_load_custom_css();
			
			// Disable Default Comments By Loading A Blank Comments Template As Defined In amamc_disable_default_comments() When AMA Form Is Used On A Page Or Post (in_the_loop())
			if ( in_the_loop() ) {
				add_filter( "comments_template", "amamc_disable_default_comments" );
			}
			
			?>
			
			<div id="comments" class="ask-me-anything-people page-id-<?php echo $post_or_page_id; ?>"> <?php
				
				// Die if comments are accessed directly
				if (!empty($_SERVER['SCRIPT_FILENAME']) && 'comments.php' == basename($_SERVER['SCRIPT_FILENAME'])) {
					die ('Please do not load this page directly. Thanks!');
				}
				
				if ( $show_question_box == 'true' ) {

					// Check If #questionsent Exist In Url Using JS. Added To The End Of This Div 
					// When Comment / Question Is Posted #questionsent Is Added To The End Of The Url Using amamc_replace_url_after_ama_comment(). ?>
					<div id="ama-success" class="ama-notice ama-success"><?php echo $success_text; ?></div> <?php
				
					// Warn If User Is Logged In 
					if ( is_user_logged_in() ) { ?>
						<div id="ama-loggedinwarning" class="ama-notice ama-loggedinwarning">
						<?php _e('Since you are currently logged in, your question will not be anonymous. Please log out or open this page in a private window if you wish to stay anonymous.','ask-me-anything-anonymously'); ?>
						</div> <?php
					} ?>
				
					<div id="respond" class="ama-question-box comment-respond">
					
						<form id="commentform" class="amaform comment-form" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post">

							<p class="comment-form-comment">
								<label for="comment"><?php echo $question_box_title; ?></label>
								<textarea name="comment" id="comment" class="ama-question-textarea" required="required" aria-required="true" maxlength="65525" rows="8" cols="45" placeholder="<?php echo $placeholder; ?>" onkeydown="document.getElementById('email').value=Math.random()+'@noreply.com'"></textarea>
							</p> <?php
							
							if ( $show_test_question == 'true' ) { ?>
								<p class="comment-form-comment">
									<label for="ama-spam-check"><?php echo $test_question; ?></label>
									<input type="text" name="amatestquestion" id="ama-testquestion" class="ama-testquestion" placeholder="<?php echo $test_placeholder; ?>" />
									
								</p> <?php
							} ?>
							
							<p class="form-submit">
								<input name="submit" type="submit" id="submit" class="amasubmit submit" value="<?php echo $ask_button_text; ?>">
							
								<input type="hidden" name="author" id="author" value="<?php echo $anonymous_name; ?>" />
								<input type="hidden" name="email" id="email" value="<?php echo rand() ?>@noreply.com" />
								<input type="hidden" name="comment_post_ID" id="comment_post_ID" value="<?php echo $post_or_page_id; ?>" />
								<input type="hidden" name="comment_parent" id="comment_parent" value="0" />
								<input type="hidden" name="amashowtestquestion" id="amashowtestquestion" value="<?php echo $show_test_question; ?>" />
								<input type="hidden" name="amatestanswer" id="amatestanswer" value="<?php echo $test_answer; ?>" />
								<input type="hidden" name="askmeanythingpeople" id="askmeanythingpeople" value="askmeanythingpeople" />
							</p> <?php 
							
							if ( $show_do_action == 'true' ) { 
								do_action('comment_form', $post_or_page_id); 
							} ?>

						</form>
					
					</div><?php
				} ?>
				
				<?php if ( $show_answers == 'true' ) {
					if ( get_comments_number($post_or_page_id) ) : ?>
						<div id="ama-answers" class="ama-has-answers">
							
							<h2><?php echo $answer_list_title; ?></h2>
							
							<div class="ama-answers-list">
								<ul id="comment_list" class="commentlist comment-list ama-questions-list">
								
									<?php
									
									$page_number 		= isset($_GET['questions']) ? $_GET['questions'] : 1;
									$offset				= $questions_per_page * ($page_number - 1);
									$order				= ($latest_first == 'true') ? 'DESC' : 'ASC';
									
									// Load Questions
									$questions = get_comments(array(
										'post_id'	=> $post_or_page_id,
										'status' 	=> 'approve',
										'order'		=> $order,
										'parent'	=> 0,					// Questions are parent comments.
										'number'	=> $questions_per_page,
										'offset'	=> $offset,
									));
									
									$class = 'alternate';
									foreach ($questions as $question) {
									
										$class = ('alternate' == $class) ? '' : 'alternate'; ?>

										<li id="comment-<?php echo $question->comment_ID; ?>" class="comment amaquestion <?php echo $class; ?>">
											<div class="comment-body comment">
												<article class="comment-body" id="div-comment-<?php echo $question->comment_ID; ?>">
													<div class="ama-question-meta">
														<div class="comment-author vcard amaavatar">
															<?php echo get_avatar( $question->comment_author_email, $avatar_size, $avatar ); ?>
															<b class="fn"><cite class="fn ama-anonymous-name"><?php echo $anonymous_name; ?></cite></b> <span class="amasays amaasked"><?php _e('asked:','ask-me-anything-anonymously') ?></span>
														</div>
											
														<div class="comment-meta comment-metadata amaquestiondate time">
															<?php printf( __('on %1$s'), get_comment_date( 'F j, Y g:i a',$question->comment_ID )); ?>
														</div>
													</div>
										

													<div class="comment-content ama-question-content">
														<?php echo $question->comment_content; ?>
													</div>
												</article>
											</div>											
												
											<?php // Load Answers
												$answers = get_comments(array(
																'post_id'	=> $post_or_page_id,
																'status' 	=> 'approve',
																'order'		=> 'DESC',
																'parent'	=> $question->comment_ID,
												)); ?>
												
												<ul class="children ama-answers-list"> <?php
													foreach ($answers as $answer) { ?>
														<li id="comment-<?php echo $answer->comment_ID; ?>" class="comment amaanswer">
															<div class="comment-body comment">
																<article class="comment-body" id="div-comment-<?php echo $answer->comment_ID; ?>">
																	<div class="ama-answer-meta">
																		<div class="comment-author vcard amaavatar">
																			<?php echo get_avatar( $answer->comment_author_email, $avatar_size ); ?>
																			<b class="fn"><cite class="fn ama-author-name"><?php echo $answer->comment_author; ?></cite></b> <span class="amasays amareply"><?php _e('replied:','ask-me-anything-anonymously') ?></span>
																		</div>
															
																		<div class="comment-meta comment-metadata amaanswer time">
																			<?php printf( __('on %1$s'), get_comment_date( 'F j, Y g:i a',$answer->comment_ID )); ?>
																		</div>
																	</div>
														

																	<div class="comment-content ama-answer-content">
																		<?php echo $answer->comment_content; ?>
																	</div>
																</article>	
															</div>
														</li> <?php
													} ?>	
												</ul>
										</li> <?php 
									} ?>
								</ul> 
								
								<?php
								// Navigation / Pagination
								if ( $show_navigation == 'true' ) {
									global $wpdb;
									$number_of_questions = $wpdb->get_var( "SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID=$post_or_page_id AND comment_approved=1 AND comment_parent=0");
									
									if ( $number_of_questions > $questions_per_page ) {
									
										echo '<nav role="navigation" class="navigation comment-navigation ama-navigation">';
										echo '<div class="nav-links">';
										
										// Pagination - Previous Page
										if ( $page_number > 1 ) {
											echo '<div class="nav-previous"><a href="'.get_permalink().'?questions='.($page_number-1).'">' . __("Previous Questions", "ask-me-anything-anonymously") . '</a></div>';
										}
										
										// Pagination - Next Page
										if ( ( $number_of_questions - $offset ) > $questions_per_page ) {
											echo '<div class="nav-next"><a href="'.get_permalink().'?questions='.($page_number+1).'">' . __("Next Questions", "ask-me-anything-anonymously") . '</a></div>';
										}
										
										echo '</div>';
										echo '</nav>';
									}
								}
								?>
								
							</div>
						</div>
					<?php else : // this is displayed if there are no comments so far 
						if( $no_answers_text != '' ) { ?>
							<div id="ama-answers" class="ama-has-no-answers">
								<p><?php echo $no_answers_text; ?></p>
							</div><?php
						}
					endif;
				}
				
				// Credits Link - Opt in
				if( $give_thanks == true ) {
					echo '<span class="amacredit"><a href="http://millionclues.com/" target="_blank">AMA by Million Clues!</a></span>';
				} ?>
				
				<?php if ( $show_question_box == 'true' ) { // Load This Only If The Question Box Is Displayed ?>
					<script type="text/javascript">
						var end_of_url 			= window.location.hash; 
						// var split_end_of_url 	= end_of_url.split("-"); // Returns an array. Had used it before introducing amamc_replace_url_after_ama_comment()
						if ( end_of_url == '#questionsent' ) {

							// Selecting via class instead of document.getElementById("ama-success").style.display = 'block'; so that it works when AMA form is used on both sidebar and a page.
							var ama_success_classes = document.getElementsByClassName('ama-success');
							for(var i = 0; i < ama_success_classes.length; i++) {
								ama_success_classes[i].style.display = 'block';
							}
							
							var ama_loggedinwarning_classes = document.getElementsByClassName('ama-loggedinwarning');
							for(var i = 0; i < ama_loggedinwarning_classes.length; i++) {
								ama_loggedinwarning_classes[i].style.marginTop = '20px';
							}
						}
					</script>
				<?php } ?>
				
			</div><?php
		}
	return ob_get_clean();
}
add_shortcode( 'askmeanythingpeople', 'amamc_do_askmeanythingpeople' );

// Disable Default Comments By Loading A Blank Comments Template
// Used In amamc_do_askmeanythingpeople()
function amamc_disable_default_comments( $comment_template ) {
	return dirname(__FILE__) . '/inc/amamc_blank-comment-template.php';
}

// Spam Check On The AMA Form
function amamc_bot_exterminator() {
    if ( isset( $_POST['amashowtestquestion'] ) && $_POST['amashowtestquestion'] == 1 ) {
		if ( $_POST['amatestquestion'] != $_POST['amatestanswer'] ) {
			wp_die( __('Error: Wrong answer, are you a bot?') );
		}
	}
}
add_action('pre_comment_on_post', 'amamc_bot_exterminator');

// Replace #comment-## with #questionsaved for AMA Page When Comment Is Saved
function amamc_replace_url_after_ama_comment($location) {
	if (isset($_POST['askmeanythingpeople'])) {
	
		// Remove Cookies for Comment Author (Which Wil Say 'Someone') And Author Email (Which Will Say 'rand()@noreply.com')
		$secure = ( 'https' === parse_url( home_url(), PHP_URL_SCHEME ) );
		setcookie( 'comment_author_' . COOKIEHASH, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, $secure );
		setcookie( 'comment_author_email_' . COOKIEHASH, '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN, $secure );
		
		// Replace #comment-## With #questionsent
		return preg_replace("/#comment-([\d]+)/", "#questionsent", $location);
	}
	return $location;
}
add_filter('comment_post_redirect', 'amamc_replace_url_after_ama_comment');

// Load Custom CSS
function amamc_load_custom_css() {
	
	if ( is_multisite() ) {
		$amamc_custom_css_option = get_blog_option( get_current_blog_id(), 'amamc_custom_css' );
	}
	else {
		$amamc_custom_css_option = get_option( 'amamc_custom_css' );
	}
	
	$amamc_admin_css_content = isset( $amamc_custom_css_option['amamc_admin_css'] ) && ! empty( $amamc_custom_css_option['amamc_admin_css'] ) ? $amamc_custom_css_option['amamc_admin_css'] : '' ; 
	
	$amamc_admin_css_content = wp_kses( $amamc_admin_css_content, array( '\'', '\"' ) );
	$amamc_admin_css_content = str_replace( '&gt;', '>', $amamc_admin_css_content );
	
	// Minify
	if ( (isset($amamc_custom_css_option['minfy_css'])) && (boolval($amamc_custom_css_option['minfy_css'])) ) {
		$amamc_admin_css_content = amamc_csstidy_helper($amamc_admin_css_content, true);
	} 
	
	?>
    <style type="text/css">
        <?php echo $amamc_admin_css_content; ?>
    </style><?php 
}
//add_filter( 'wp_enqueue_scripts' , 'amamc_load_custom_css' ); // Uncomment this to load the CSS on all frontend pages.