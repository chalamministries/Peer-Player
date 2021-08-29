<?php

namespace PeerPlayer\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


class PeerPlayer extends Widget_Base{

  public function get_name(){
	return 'Peer-player';
  }

  public function get_title(){
	return 'Peer Player';
  }

  public function get_icon(){
	return 'fa fa-tv';
  }

  public function get_categories(){
	return ['general'];

  }

  protected function _register_controls(){

	$this->start_controls_section(
	  'section_content',
	  [
		'label' => 'Settings',
	  ]
	);

	$this->add_control(
	  'Peerplayer_url',
	  [
		'label' => 'Video URL',
		'type' => \Elementor\Controls_Manager::TEXT
	  ]
	);

	$this->add_control(
		'Peerplayer_autoplay',
		[
			'label' => __( 'Auto Play', 'plugin-domain' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'true',
			'options' => [
				'true'  => __( 'True', 'plugin-domain' ),
				'false' => __( 'False', 'plugin-domain' )
			],
		]
	);

	$this->add_control(
		'Peerplayer_muted',
		[
			'label' => __( 'Muted', 'plugin-domain' ),
			'type' => \Elementor\Controls_Manager::SELECT,
			'default' => 'false',
			'options' => [
				'true'  => __( 'True', 'plugin-domain' ),
				'false' => __( 'False', 'plugin-domain' )
			],
		]
	);

	$this->add_control(
		'Peerplayer_watermark',
		[
			'label' => __( 'Watermark', 'plugin-domain' ),
			'type' => \Elementor\Controls_Manager::MEDIA,
			'default' => [
				'url' => '',
			],
		]
	);

	$this->end_controls_section();
  }


  protected function render(){
	$settings = $this->get_settings_for_display();

	echo do_shortcode('[peerplayer url="'.$settings['Peerplayer_url'].'" autoplay="'.$settings['Peerplayer_autoplay'].'" muted="'.$settings['Peerplayer_muted'].'" watermark="'.$settings['Peerplayer_watermark']['url'].'"]');
  }


}