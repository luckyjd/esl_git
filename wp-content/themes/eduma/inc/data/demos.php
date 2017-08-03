<?php

$thim_uri_screenshot = THIM_URI . 'inc/data/demos/demo-so/';

$page_builder_choosen = get_theme_mod( 'thim_page_builder_chosen', '' );

$demo_datas = array(
	'demo-so/demo-01'               => array(
		'title'            => esc_html__( 'Demo Main Demo', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-01.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'paid-memberships-pro',
			'learnpress-course-review',
			'learnpress-paid-membership-pro',
			'learnpress-wishlist',
			'bbpress',
			'learnpress-bbpress',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-page.zip'
		),
	),
	'demo-so/demo-02'               => array(
		'title'            => esc_html__( 'Demo Course Era', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-2/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-02.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
	),
	'demo-so/demo-courses-hub'      => array(
		'title'            => esc_html__( 'Demo Courses Hub', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-courses-hub/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-courses-hub.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'learnpress-collections',
			'widget-logic',
		),
	),
	'demo-so/demo-one-instructor'   => array(
		'title'            => esc_html__( 'Demo One Instructor', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-one-instructor/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-one-instructor.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-one-instructor.zip'
		),
	),
	'demo-so/demo-one-course'       => array(
		'title'            => esc_html__( 'Demo One Course', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-one-course/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-one-course.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-one-course.zip'
		),
	),
	'demo-so/demo-03'               => array(
		'title'            => esc_html__( 'Demo Online School', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-3/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-03.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-page-video.zip'
		),
	),
	'demo-so/demo-university'       => array(
		'title'            => esc_html__( 'Demo Classic University', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-university/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-university.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-university.zip'
		),
	),
	'demo-so/demo-university-2'     => array(
		'title'            => esc_html__( 'Demo Modern University', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-university-2/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-university-2.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
			'woocommerce-products-filter',
		),
		'revsliders'       => array(
			'home-university-2.zip'
		),
	),
	'demo-so/demo-kindergarten'     => array(
		'title'            => esc_html__( 'Demo Kindergarten', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-kindergarten/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-kindergarten.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-kindergarten.zip'
		),
	),
	'demo-so/demo-languages-school' => array(
		'title'            => esc_html__( 'Demo Languages School', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-languages-school/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-languages-school.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-languages-school.zip'
		),
	),
	'demo-so/demo-boxed'            => array(
		'title'            => esc_html__( 'Demo Boxed', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-boxed/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-boxed.png',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-page-boxed.zip'
		),
	),
	'demo-so/demo-rtl'              => array(
		'title'            => esc_html__( 'Demo RTL', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-rtl/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-rtl.png',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
	),
	'demo-so/demo-university-3'     => array(
		'title'            => esc_html__( 'Demo Ivy League', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-university-3/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-university-3.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
			'thim-twitter',
			'instagram-feed',
		),
		'revsliders'       => array(
			'home-university-3.zip'
		),
	),
	'demo-so/demo-university-4'     => array(
		'title'            => esc_html__( 'Demo Stanford', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-university-4/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-university-4.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'siteorigin-panels',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
			'thim-twitter',
			'instagram-feed',
		),
		'revsliders'       => array(
			'home-university-4.zip'
		),
	),

	//For demo using visual composer
	'demo-vc/demo-01'               => array(
		'title'            => esc_html__( 'Demo Main Demo', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-01.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'paid-memberships-pro',
			'learnpress-course-review',
			'learnpress-paid-membership-pro',
			'learnpress-wishlist',
			'bbpress',
			'learnpress-bbpress',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-page.zip'
		),
	),
	'demo-vc/demo-02'               => array(
		'title'            => esc_html__( 'Demo Course Era', 'eduma' ),
		'demo_url'         => 'https://educationwp.thimpress.com/demo-2/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-02.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
	),
	'demo-vc/demo-courses-hub'      => array(
		'title'            => esc_html__( 'Demo Courses Hub', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-courses-hub/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-courses-hub.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'learnpress-collections',
			'widget-logic',
		),
	),
	'demo-vc/demo-one-instructor'   => array(
		'title'            => esc_html__( 'Demo One Instructor', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-one-instructor/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-one-instructor.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-one-instructor.zip'
		),
	),
	'demo-vc/demo-one-course'       => array(
		'title'            => esc_html__( 'Demo One Course', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-one-course/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-one-course.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-one-course.zip'
		),
	),
	'demo-vc/demo-03'               => array(
		'title'            => esc_html__( 'Demo Online School', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-3/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-03.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-page-video.zip'
		),
	),
	'demo-vc/demo-university'       => array(
		'title'            => esc_html__( 'Demo Classic University', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-university/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-university.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-university.zip'
		),
	),
	'demo-vc/demo-university-2'     => array(
		'title'            => esc_html__( 'Demo Modern University', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-university-2/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-university-2.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
			'woocommerce-products-filter',
		),
		'revsliders'       => array(
			'home-university-2.zip'
		),
	),
	'demo-vc/demo-kindergarten'     => array(
		'title'            => esc_html__( 'Demo Kindergarten', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-kindergarten/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-kindergarten.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-kindergarten.zip'
		),
	),
	'demo-vc/demo-languages-school' => array(
		'title'            => esc_html__( 'Demo Languages School', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-languages-school/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-languages-school.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-languages-school.zip'
		),
	),
	'demo-vc/demo-boxed'            => array(
		'title'            => esc_html__( 'Demo Boxed', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-boxed/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-boxed.png',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
		'revsliders'       => array(
			'home-page-boxed.zip'
		),
	),
	'demo-vc/demo-rtl'              => array(
		'title'            => esc_html__( 'Demo RTL', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-rtl/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-rtl.png',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
		),
	),
	'demo-vc/demo-university-3'     => array(
		'title'            => esc_html__( 'Demo Ivy League', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-university-3/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-university-3.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
			'thim-twitter',
			'instagram-feed',
		),
		'revsliders'       => array(
			'home-university-3.zip'
		),
	),
	'demo-vc/demo-university-4'     => array(
		'title'            => esc_html__( 'Demo Stanford', 'eduma' ),
		'demo_url'         => 'http://educationwp.thimpress.com/demo-university-4/',
		'thumbnail_url'    => 'https://updates.thimpress.com/wp-content/uploads/2017/06/eduma-demo-university-4.jpg',
		'plugins_required' => array(
			'eduma-demo-data',
			'js_composer',
			'learnpress',
			'mailchimp-for-wp',
			'contact-form-7',
			'woocommerce',
			'revslider',
			'miniorange-login-openid',
			'wp-events-manager',
			'tp-portfolio',
			'thim-testimonials',
			'thim-our-team',
			'learnpress-course-review',
			'learnpress-wishlist',
			'widget-logic',
			'thim-twitter',
			'instagram-feed',
		),
		'revsliders'       => array(
			'home-university-4.zip'
		),
	),
);

return $demo_datas;