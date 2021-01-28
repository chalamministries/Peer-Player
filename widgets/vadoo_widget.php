<?php

namespace Vadoo\Widgets;

use Elementor\Widget_Base;
use Elementor\Controls_Manager;

if (!defined('ABSPATH')) exit; // Exit if accessed directly


class VadooTV extends Widget_Base{

  public function get_name(){
	return 'vadoo-player';
  }

  public function get_title(){
	return 'Vadoo Player';
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
	  'vadoo_url',
	  [
		'label' => 'Video URL',
		'type' => \Elementor\Controls_Manager::TEXT
	  ]
	);

	$this->add_control(
		'vadoo_autoplay',
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
		'vadoo_muted',
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
		'vadoo_watermark',
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

	echo do_shortcode('[vadootv url="'.$settings['vadoo_url'].'" autoplay="'.$settings['vadoo_autoplay'].'" muted="'.$settings['vadoo_muted'].'" watermark="'.$settings['vadoo_watermark']['url'].'"]');
  }


}