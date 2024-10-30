<?php 
/**
* Plugin Name: Milmila
* Plugin URI: https://affiliate.milmila.com/
* Description: This is Milmila Affiliate Plugin.
* Version: 1.0
* Author: Milmila Tech India Pvt. Ltd.
* Author URI: https://milmila.com/
**/

// If this file is called directly, abort. //
if ( ! defined( 'WPINC' ) ) {die;} // end if
// Define Our Constants
define('MILMILA_CORE_INC',dirname( __FILE__ ).'/assets/inc/');
define('MILNILA_CORE_IMG',plugins_url( 'assets/img/', __FILE__ ));
define('MILMILA_CORE_CSS',plugins_url( 'assets/css/', __FILE__ ));
define('MILMILA_CORE_JS',plugins_url( 'assets/js/', __FILE__ ));
/*
*
*  Register CSS
*
*/
function milmila_register_core_css(){
wp_enqueue_style('milmila-core', MILMILA_CORE_CSS . 'milmila-core-css.css',null,time(),'all');
};
add_action( 'wp_enqueue_scripts', 'milmila_register_core_css' );    
/*
*
*  Register JS/Jquery Ready
*
*/
function milmila_register_core_js(){
// Register Core Plugin JS	
wp_enqueue_script('milmila-core', MILMILA_CORE_JS . 'milmila-core-js.js','jquery',time(),true);
};
add_action( 'wp_enqueue_scripts', 'milmila_register_core_js' );    
/*
*
*  Includes
*
*/ 
// Load the Functions
if ( file_exists( MILMILA_CORE_INC . 'milmila-core-functions.php' ) ) {
	require_once MILMILA_CORE_INC . 'milmila-core-functions.php';
}     
// Load the ajax Request
if ( file_exists( MILMILA_CORE_INC . 'milmila-ajax-request.php' ) ) {
	require_once MILMILA_CORE_INC . 'milmila-ajax-request.php';
} 
// Load the Shortcodes
if ( file_exists( MILMILA_CORE_INC . 'milmila-shortcodes.php' ) ) {
	require_once MILMILA_CORE_INC . 'milmila-shortcodes.php';
}

add_action('admin_menu', 'milmila_setup_menu');
 
function milmila_setup_menu(){
        add_menu_page( 'Milmila', 'Milmila', 'manage_options', 'milmila', 'milmila_init',plugins_url( '/milmila-authentication/assets/icon-20x20.png' ));
}
 
function milmila_init(){
	/**
 * Check if WooCommerce is active
 **/
	if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		if( class_exists( 'Woo_Variation_Swatches' ) ) {
			?>
			<!DOCTYPE HTML>  
			<html>
			<head>
			<style>
			.error {color: #FF0000;}
			
			.tooltip {
			  position: relative;
			  display: inline-block;
			  border-bottom: 1px dotted black;
			}

			.tooltip .tooltiptext {
			  visibility: hidden;
			  width: 120px;
			  background-color: #555;
			  color: #fff;
			  text-align: center;
			  border-radius: 6px;
			  padding: 5px 0;
			  position: absolute;
			  z-index: 1;
			  bottom: 125%;
			  left: 50%;
			  margin-left: -60px;
			  opacity: 0;
			  transition: opacity 0.3s;
			}

			.tooltip .tooltiptext::after {
			  content: "";
			  position: absolute;
			  top: 100%;
			  left: 50%;
			  margin-left: -5px;
			  border-width: 5px;
			  border-style: solid;
			  border-color: #555 transparent transparent transparent;
			}

			.tooltip:hover .tooltiptext {
			  visibility: visible;
			  opacity: 1;
			}
			</style>
			<script type="text/javascript">
				function fetch_is_affiliate(){
					var id = document.getElementById("domain").value;
					var cKey = document.getElementById("cKey").value;
					var cSecret = document.getElementById("cSecret").value;
					console.log(id);
					if(id==null || id==""){
						alert("Domain Name Is Required");
						return;
					}
					if(cKey==null || cKey==""){
						alert("Customer Key Is Required");
						return;
					}
					if(cSecret==null || cSecret==""){
						alert("Customer Secret Is Required");
						return;
					}
					
					jQuery.ajax({
						type: "GET",
						url: "https://5003448dbc097484cfc716e706383294.m.pipedream.net",
						data: {"domain":id, "cKey":cKey, "cSecret":cSecret},
						crossDomain : true,
						headers: {
							'Content-Type': 'application/json'
						},
						success : function (data) {
							if(data.data=="success") {
								jQuery("#milmila_form").css("display","none");
								jQuery(".error").css("display","none");
								jQuery("#sRes").css("display","block");
								jQuery("#sRes").append(data.msg);
							} else {
								jQuery("#milmila_form").css("display","none");
								jQuery(".error").css("display","none");
								jQuery("#sRes").html(data.msg);
								jQuery("#sRes").css("display","block");
							}
						},
						error: function () {
							console.log("error");
						}
					});
				}
			</script>
			</head>
			<body>  

			<?php
			// define variables and set to empty values
			$domainErr = $cKeyErr = $cSecretErr = "";
			$domain = $cKey = $cSecret = "";

			
			if ($_SERVER["REQUEST_METHOD"] == "POST") {
			  if (empty($_POST["domain"])) {
				$domainErr = "Domain Name is required";
			  }
			  
			  if (empty($_POST["cKey"])) {
				$cKeyErr = "Customer Key is required";
			  }
				
			  if (empty($_POST["cSecret"])) {
				$cSecretErr = "Customer Secret is required";
			  } else {
				// check if URL address syntax is valid (this regular expression also allows dashes in the URL)
				if (!preg_match("/\b(?:(?:https?|ftp):\/\/|www\.)[-a-z0-9+&@#\/%?=_|!:,.;]*[-a-z0-9+&@#\/%=_|]/i",$cSecret)) {
				  $cSecretErr = "Invalid URL";
				}
			  }
			}
				
			?>
			<h1>Milmila Dashboard</h1>
			<h2>Milmila API Check</h2>
			<p><span class="error">* required field</span></p>
			<form id="milmila_form">  
			  Domain: <input type="text" id="domain" value="<?php echo $domain;?>">
			  <span class="error">* <?php echo $domainErr;?></span>
				<div class="tooltip"><i class="fa fa-question-circle"></i>
				  <span class="tooltiptext">Enter your domain name</span>
				</div>
			  <br><br>
			  Customer Key: <input type="text" id="cKey" value="<?php echo $cKey;?>">
			  <span class="error">* <?php echo $cKeyErr;?></span>
			  <div class="tooltip"><i class="fa fa-question-circle"></i>
				  <span class="tooltiptext">Enter WooCommerce REST API Customer Key</span>
				</div>
			  <br><br>
			  Customer Secret: <input type="text" id="cSecret" value="<?php echo $cSecret;?>">
			  <span class="error">* <?php echo $cSecretErr;?></span>
			  <div class="tooltip"><i class="fa fa-question-circle"></i>
				  <span class="tooltiptext">Enter WooCommerce REST API Customer Secret</span>
				</div>
			  <br><br>
			  <input type="button" onclick="fetch_is_affiliate()" id="submit" value="Submit">  
			</form>
			<div id="sRes" style="display:none;">
				<span>Congratulation you are registered as affiliate in milmila.com</span>
			</div>
			</body>
			</html>
			<?php
		} else {
			$pluginSlugs = array(
				'woo-variation-swatches'
			);

			require_once(ABSPATH . 'wp-admin/includes/plugin-install.php');
			require_once(ABSPATH . 'wp-admin/includes/file.php');
			require_once(ABSPATH . 'wp-admin/includes/misc.php');
			require_once(ABSPATH . 'wp-admin/includes/plugin.php');
			require_once(ABSPATH . 'wp-admin/includes/class-wp-upgrader.php');
			
			/**
			 * Download, install and activate a plugin
			 * 
			 * If the plugin directory already exists, this will only try to activate the plugin
			 * 
			 * @param string $slug The slug of the plugin (should be the same as the plugin's directory name
			 */
			function sswInstallActivatePlugin($slug)
			{
				$pluginDir = WP_PLUGIN_DIR . '/' . $slug;    
				/* 
				 * Don't try installing plugins that already exist (wastes time downloading files that 
				 * won't be used 
				 */
				if (!is_dir($pluginDir)) {
					$api = plugins_api(
						'plugin_information',
						array(
							'slug' => $slug,
							'fields' => array(
								'short_description' => false,
								'sections' => false,
								'requires' => false,
								'rating' => false,
								'ratings' => false,
								'downloaded' => false,
								'last_updated' => false,
								'added' => false,
								'tags' => false,
								'compatibility' => false,
								'homepage' => false,
								'donate_link' => false,
							),
						)
					);
					
					// Replace with new QuietSkin for no output
					$skin = new Plugin_Installer_Skin(array('api' => $api));

					$upgrader = new Plugin_Upgrader($skin);

					$install = $upgrader->install($api->download_link);

					if ($install !== true) {
						echo 'Error: Install process failed (' . $slug . '). var_dump of result follows.<br>' 
							. "\n";
						var_dump($install); // can be 'null' or WP_Error
					}
				}
			/*
			 * The install results don't indicate what the main plugin file is, so we just try to
			 * activate based on the slug. It may fail, in which case the plugin will have to be activated
			 * manually from the admin screen.
			 */
			$pluginPath = $pluginDir . '/' . $slug . '.php';
			if (file_exists($pluginPath)) {
				activate_plugin($pluginPath);
				echo '<h1>Milmila Active</h1>';
			} else {
				echo 'Error: Plugin file not activated (' . $slug . '). This probably means the main '
					. 'file\'s name does not match the slug. Check the plugins listing in wp-admin.<br>' 
					. "\n";
			}
		}

		foreach ($pluginSlugs as $pluginSlug) {
			sswInstallActivatePlugin($pluginSlug);
		}	
		}
		

	}
}