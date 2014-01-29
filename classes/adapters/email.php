<?php

class WP_Stream_Notification_Adapter_Email extends WP_Stream_Notification_Adapter {

	public static function register( $title = '' ) {
		parent::register( __( 'Email', 'stream-notifications' ) );
	}

	public static function fields() {
		return array(
			'users' => array(
				'title'    => __( 'To users', 'stream-notifications' ),
				'type'     => 'hidden',
				'multiple' => true,
				'ajax'     => true,
				'key'      => 'author',
			),
			'emails' => array(
				'title' => __( 'To emails', 'stream-notifications' ),
				'type'  => 'text',
				'tags'  => true,
			),
			'subject' => array(
				'title' => __( 'Subject', 'stream-notifications' ),
				'type'  => 'text',
				'hint'  => __( 'ex: "%%summary%%" or "[%%created%% - %%author%%] %%summary%%", consult FAQ for documentaion.', 'stream-notifications' ),
			),
			'message' => array(
				'title' => __( 'Message', 'stream-notifications' ),
				'type'  => 'textarea',
			),
		);
	}

	public function send( $log ) {
		$users = $this->params['users'];
		$user_emails = array();
		if ( $users ) {
			$user_query = new WP_User_Query(
				array(
					'include' => $users,
					'fields'  => array( 'user_email' ),
				)
			);
			$user_emails = wp_list_pluck( $user_query->results, 'user_email' );
		}
		$emails = explode( ',', $this->params['emails'] );
		if ( ! empty( $user_emails ) ) {
			$emails = array_merge( $emails, $user_emails );
		}
		$emails  = array_filter( $emails );
		$subject = $this->replace( $this->params['subject'], $log );
		$message = $this->replace( $this->params['message'], $log );
		wp_mail( $to, $subject, $message );
	}

}

WP_Stream_Notification_Adapter_Email::register();
