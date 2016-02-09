<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'WC_Email_Vendor_New_Product_Added' ) ) :

/**
 * Customer New Account
 *
 * An email sent to the customer when they create an account.
 *
 * @class 		WC_Email_Vendor_New_Product_Added
 * @version		2.0.0
 * @package		WooCommerce/Classes/Emails
 * @author 		WooThemes
 * @extends 	WC_Email
 */
class WC_Email_Vendor_New_Product_Added extends WC_Email {

	var $user_login;
	var $user_email;
	var $user_pass;

	/**
	 * Constructor
	 *
	 * @access public
	 * @return void
	 */
	function __construct() {
		global $WCMp;
		
		$this->id 				= 'admin_new_vendor_product';
		
		$this->title       = __( 'New Vendor Product', $WCMp->text_domain );
		$this->description = __( 'Notification emails are sent when a new product is submitted by a vendor.', $WCMp->text_domain );

		$this->heading = __( 'New product submitted: {product_name}', $WCMp->text_domain );
		$this->subject = __( '[{blogname}] New product submitted by {vendor_name} - {product_name}', $WCMp->text_domain );

		$this->template_base = $WCMp->plugin_path . 'templates/';
		$this->template_html 	= 'emails/new-product.php';
		$this->template_plain 	= 'emails/plain/new-product.php';
		
		
		// Call parent constuctor
		parent::__construct();
	}

	/**
	 * trigger function.
	 *
	 * @access public
	 * @return void
	 *
	 * @param unknown $order_id
	 */
	function trigger( $post_id, $post, $vendor )	{
		global $WCMp;
		
		if ( !$this->is_enabled() ) return;

		$this->find[ ]      = '{product_name}';
		$this->product_name = $post->post_title;
		$this->replace[ ]   = $this->product_name;

		$this->find[ ]     = '{vendor_name}';
		$this->vendor_name = $vendor->user_data->display_name;
		$this->replace[ ]  = $this->vendor_name;

		$this->post_id = $post->ID;
		
		$post_type = get_post_type($this->post_id);
		$this->post_type = $post_type; 
		if($post_type == 'shop_coupon') {
			$this->title       = __( 'New Vendor Coupon', $WCMp->text_domain );
			$this->description = __( 'Notification emails are sent when a new coupon is submitted by a vendor.', $WCMp->text_domain );

			$this->heading = __( 'New coupon submitted: {product_name}', $WCMp->text_domain );
			$this->subject = __( '[{blogname}] New coupon submitted by {vendor_name} - {product_name}', $WCMp->text_domain );
		}
		
		// Other settings
		$this->recipient = $this->get_option( 'recipient' );

		if ( !$this->recipient )
			$this->recipient = get_option( 'admin_email' );
		
		$this->send( $this->get_recipient(), $this->get_subject(), $this->get_content(), $this->get_headers(), $this->get_attachments() );
	}

	/**
	 * get_content_html function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_html()
	{
		ob_start();
		wc_get_template( $this->template_html, array(
															 'product_name'  => $this->product_name,
															 'vendor_name'   => $this->vendor_name,
															 'post_id'       => $this->post_id,
															 'post_type'     => $this->post_type,
															 'email_heading' => $this->get_heading()
														), 'woocommerce/', $this->template_base );

		return ob_get_clean();
	}


	/**
	 * get_content_plain function.
	 *
	 * @access public
	 * @return string
	 */
	function get_content_plain()
	{
		ob_start();
		wc_get_template( $this->template_plain, array(
															  'product_name'  => $this->product_name,
															  'vendor_name'   => $this->vendor_name,
															  'post_id'       => $this->post_id,
															  'post_type'     => $this->post_type,
															  'email_heading' => $this->get_heading()
														 ), 'woocommerce/', $this->template_base );

		return ob_get_clean();
	}


	/**
	 * Initialise Settings Form Fields
	 *
	 * @access public
	 * @return void
	 */
	function init_form_fields()
	{
		global $WCMp;
		$this->form_fields = array(
			'enabled'    => array(
				'title'   => __( 'Enable/Disable', $WCMp->text_domain ),
				'type'    => 'checkbox',
				'label'   => __( 'Enable this email notification.', $WCMp->text_domain ),
				'default' => 'yes'
			),
			'recipient'  => array(
				'title'       => __( 'Recipient(s)', $WCMp->text_domain ),
				'type'        => 'text',
				'description' => sprintf( __( 'Enter recipient(s) (comma separated) for this email. Defaults to <code>%s</code>.', $WCMp->text_domain ), esc_attr( get_option( 'admin_email' ) ) ),
				'placeholder' => '',
				'default'     => ''
			),
			'subject'    => array(
				'title'       => __( 'Subject', $WCMp->text_domain ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the email subject line. Leave it blank to use the default subject: <code>%s</code>.', $WCMp->text_domain ), $this->subject ),
				'placeholder' => '',
				'default'     => ''
			),
			'heading'    => array(
				'title'       => __( 'Email Heading', $WCMp->text_domain ),
				'type'        => 'text',
				'description' => sprintf( __( 'This controls the main heading contained within the email notification. Leave it blank to use the default heading: <code>%s</code>.', $WCMp->text_domain ), $this->heading ),
				'placeholder' => '',
				'default'     => ''
			),
			'email_type' => array(
				'title'       => __( 'Email Type', $WCMp->text_domain ),
				'type'        => 'select',
				'description' => __( 'Choose which format of email to be sent.', $WCMp->text_domain ),
				'default'     => 'html',
				'class'       => 'email_type',
				'options'     => array(
					'plain'     => __( 'Plain Text', $WCMp->text_domain ),
					'html'      => __( 'HTML', $WCMp->text_domain ),
					'multipart' => __( 'Multipart', $WCMp->text_domain ),
				)
			)
		);
	}
}

endif;

return new WC_Email_Vendor_New_Product_Added();