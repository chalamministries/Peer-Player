<?php
/*
Plugin Name: Peervadoo
Plugin URI:  https://github.com/chalamministries/Peervadoo
Description: Reduce CDN Costs and Rebuffering In Video Streaming Using Hybrid P2P Streaming and Multi-CDN With A Plug And Play Solution!
Version:     2.0.1
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
			'https://github.com/chalamministries/Peervadoo/',
			__FILE__,
			'Peervadoo'
		);
		$myUpdateChecker->setAuthentication('58eb460e13d855807d762307586acd4db6782ca3');
	}
}

add_action('plugins_loaded', 'check_upgrade');

function register_vadoo_script() {
	   $plugin_url = plugin_dir_url( __FILE__ );

	   wp_register_script( 'clappr', 'https://cdn.jsdelivr.net/npm/clappr@latest/dist/clappr.min.js');
	   wp_register_script( 'clappr_responsive', 'https://cdn.jsdelivr.net/npm/clappr-responsive-container-plugin@1.0.0/dist/clappr-responsive-container-plugin.min.js', array('clappr', 'jquery'));
	   wp_register_script( 'videojs', "https://unpkg.com/video.js/dist/video.min.js");
	   wp_register_script( 'videojshls', 'https://cdn.jsdelivr.net/npm/videojs-contrib-hls.js@latest');
	   wp_register_script( 'videojswatermark', $plugin_url . "js/videojs.watermark.js?3", array('videojs'));
	   wp_register_script( 'vadoosdk', 'https://jssdk.peervadoo.com/vadoosdk.js');
	   wp_enqueue_script( 'clappr' );
	   wp_enqueue_script( 'clappr_responsive' );
	   wp_enqueue_script( 'videojs' );
	   wp_enqueue_script( 'videojshls' );
	   wp_enqueue_script( 'videojswatermark' );
	   wp_enqueue_script( 'vadoosdk' );

	   wp_enqueue_style( 'style',  $plugin_url . "css/embed.css");
	   wp_enqueue_style('videojswatermarkstyle', $plugin_url . "css/videojs.watermark.css");
	   wp_enqueue_style('videojscss', 'https://unpkg.com/video.js/dist/video-js.min.css');

}

add_action( 'wp_enqueue_scripts', 'register_vadoo_script' );

function peervadoo_shortcode($atts) {
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
	$player = $vadoo_tv_options['video_player_1'];

	switch($player) {
		case "videojs":
			return vadoo_videojs_player($token, $a);
			break;
		case "clappr":
		default:
			return vadoo_clappr_player($token, $a);
			break;
	}
}

function vadoo_clappr_player($token, $a) {

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

function vadoo_videojs_player($token, $a) {


	$playerID = "player" . rand(100,999);
	$autoplay = $a['autoplay'] == true ? "autoplay" : "";

	$html = '<video id="'.$playerID.'" class="video-js vjs-default-skin" preload="none" '.$autoplay.' controls></video>
	  <script>
		var mixer = new vadoo.base.Mixer(token="'.$token.'");

		var '.$playerID.' = videojs("'.$playerID.'", {
			muted: '.$a['muted'].',
			fluid: true,
			autoplay: '.$a['autoplay'].',
			responsive: true,
			html5: {
				hlsjsConfig: {
					liveSyncDurationCount: 5,
					loader: mixer.createMixer()
				}
			}
		});

		vadoo.base.init_videojs_player('.$playerID.');

		'.$playerID.'.src({
			src: "'.$a['url'].'",
			type: "application/x-mpegURL"
		});';

		if($a['watermark'] != "") {
		  $html .= $playerID . '.watermark({
			  file: "'.$a['watermark'].'",
			  xpos: 100,
			  ypos: 100,
			  xrepeat: 0,
			  opacity: 0.5,
		  });';
		}

	  $html .= '</script>';

	  return $html;
}

add_shortcode('peervadoo', 'peervadoo_shortcode');


?>