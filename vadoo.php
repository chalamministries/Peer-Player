<?php
/*
Plugin Name: VadooTV
Plugin URI:  https://github.com/chalamministries/Vadootv
Description: Reduce CDN Costs and Rebuffering In Video Streaming Using Hybrid P2P Streaming and Multi-CDN With A Plug And Play Solution!
Version:     1.1.0
Author:      Steven Gauerke
Author URI:  http://github.com/chalamministries
License:     GPL2
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

include_once("widgets/elementor.php");
include_once('updater.php');
include_once('settings.php');

function check_upgrade()
{
	if (is_admin()) {
		$myUpdateChecker = Puc_v4_Factory::buildUpdateChecker(
			'https://github.com/chalamministries/Vadootv/',
			__FILE__,
			'Vadootv'
		);
		$myUpdateChecker->setAuthentication('58eb460e13d855807d762307586acd4db6782ca3');
	}
}

add_action('plugins_loaded', 'check_upgrade');

function register_vadoo_script() {
	   wp_register_script( 'clappr', 'https://cdn.jsdelivr.net/npm/clappr@latest/dist/clappr.min.js');
	   wp_register_script( 'clappr_responsive', 'https://cdn.jsdelivr.net/npm/clappr-responsive-container-plugin@1.0.0/dist/clappr-responsive-container-plugin.min.js', array('clappr', 'jquery'));
	   wp_register_script( 'vadoosdk', 'https://jssdk.peervadoo.com/vadoosdk.js');
	   wp_enqueue_script( 'clappr' );
	   wp_enqueue_script( 'clappr_responsive' );
	   wp_enqueue_script( 'vadoosdk' );

	   $plugin_url = plugin_dir_url( __FILE__ );

	   wp_enqueue_style( 'style',  $plugin_url . "/css/embed.css");
}

add_action( 'wp_enqueue_scripts', 'register_vadoo_script' );

function vadootv_shortcode($atts) {
	$a = shortcode_atts( array(
	  'url' => '',
	  'autoplay' => 'true',
	  'muted' => 'false',
	  'watermark' => 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mNkYPhfDwAChwGA60e6kgAAAABJRU5ErkJggg=='
	), $atts );

	if($a['url'] == "") {
		return "Missing URL value";
	}

	$vadoo_tv_options = get_option( 'vadoo_tv_option_name' );
	$token = $vadoo_tv_options['vadootv_api_token_0'];

	$playerID = "player" . rand(100,999);

	$html = '<div class="video"><div class="embed-responsive embed-responsive-16by9"><div id="'.$playerID.'" class="embed-responsive-item"></div></div></div>
	  <script>


		var mixer = new vadoo.base.Mixer(token="'.$token.'");

		var options = {
			parentId: "#'.$playerID.'",
			source: "'.$a['url'].'",
			mute: '.$a['muted'].',
			autoPlay: '.$a['autoplay'].',
			width: "100%",
			height: "100%",
			playback: {
				hlsjsConfig: {
					liveSyncDurationCount: 5,
					loader: mixer.createMixer()
				}
			},
			watermark: "'.$a['watermark'].'",
			plugins: {
				container: [ResponsiveContainer]
			  }
		}

		var '. $playerID .' = new Clappr.Player(options);
		vadoo.base.start_clappr_player('. $playerID .');
	  </script>';

	  return $html;
}

add_shortcode('vadootv', 'vadootv_shortcode');


?>