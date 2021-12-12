<?php
/*
Plugin Name: Insta contact us
Description: A plugin that automatically generates a contact form
Plugin URI:
Version: 1.0
Author: Justine Kenji Dela Cruz & Maclane Raley
Author URI:
License: GPLv2 or later
Requires at least: 4.9
Requires PHP: 5.6
*/
    if(!defined('ABSPATH')){
        exit;
    }
	
class ContactUs{
	public function __construct(){
		add_action('init',array($this, 'custom_post'));

        add_action('wp_enqueue_scripts',array($this, 'load_assets'));

        add_shortcode('insta-contact-us',array($this,'load_shortcode'));

        add_action('wp_footer',array($this,'load_scripts'));

        add_action('rest_api_init',array($this,'register_api'));
	}
	public function custom_post(){
		$elem = array(
			'public' => true,
			'has_archive' => true,
			'supports' => array('title','excerpt'),
			'exclude_from_search' => true,
			'publicly_queryable' => false,
			'capability' => 'manage_options',
			'labels' => array(
				'name' => 'Contact Us',
				'singular_name' => 'Feedback'
			),
			'menu_icon' => 'dashicons-email',
		);
		
		register_post_type('contact_us', $elem);
	}
    public function load_assets(){
        wp_enqueue_style('insta-contact-us',
                        plugin_dir_url(__FILE__).'css/form.css',
                        array(),
                        1,
                        'all'
        );
        wp_enqueue_script('insta-contact-us',
            plugin_dir_url(__FILE__).'js/formjs.js',
            array('jquery'),
            1,
            true
        );
    }

    public function load_shortcode(){
        require_once 'form-html.php';
    }

    public function load_scripts()
    {
        require_once 'scripts.php';
    }

    public function register_api(){
        register_rest_route('insta-contact-us/v1','send-email',array(
           'methods' => 'POST',
           'callback' => array($this, 'handle_contact_form')
        ));

    }
    public function handle_contact_form($data){
        $headers = $data->get_headers();
        $params = $data->get_params();
       $nonce = $headers['x_wp_nonce'][0];
        if(!wp_verify_nonce($nonce,'wp_rest')){
            return new WP_REST_RESPONSE('Message not sent', 422);
        }
        $post_id = wp_insert_post([
           'post_type' => 'contact_us',
            'post_title' =>'Feedback from user',
            'post_status' => 'publish',
            'post_excerpt' => "Name: ".$params['Name']."\nEmail: ".$params['Email']."\nPhone: ".$params['Phone']."\nFeedback: "."\n".$params['Message']

        ]);

        if($post_id){
            return new WP_REST_Response('Thank for your feedback',200);
        }
    }

	
}new ContactUs;
	
    


?>