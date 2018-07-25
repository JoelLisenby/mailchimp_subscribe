<?php

class MailChimpSubscribe {

  public $output;
  private $merge_fields;
  private static $mailchimp_data_center = 'YOUR_LIST_DATA_CENTER';
  private static $mailchimp_api_key = 'YOUR_API_KEY';
  private static $mailchimp_list_id = 'YOUR_LIST_ID';

  public function __construct() {

    $this->merge_fields = array();
    $this->output['success'] = false;
    $this->output['message'] = 'Failed to subscribe.';

    if( !empty( $_GET['email'] ) ) {

      if( !empty( $_GET['FNAME'] ) ) {
        $this->merge_fields['FNAME'] = $_GET['FNAME'];
      }

      if( !empty( $_GET['LNAME'] ) ) {
        $this->merge_fields['LNAME'] = $_GET['LNAME'];
      }

      if( !empty( $_GET['COMPANY'] ) ) {
        $this->merge_fields['COMPANY'] = $_GET['COMPANY'];
      }

      if( self::subscribe( $_GET['email'], $this->merge_fields ) ) {
        $this->output['success'] = true;
        $this->output['message'] = 'Successfully Subscribed!';
      }

    }
    
    header('Content-Type: application/json');
    echo json_encode( $this->output );

  }
  
  private function subscribe( $email, $merge_fields = null ) {
    $auth = base64_encode( 'user:'. self::$mailchimp_api_key );
    $data = array(
      'email_address' => $email,
      'status'        => 'pending'
    );
    if( !empty( $merge_fields ) ) {
      $data['merge_fields'] = $merge_fields;
    }
    $json_data = json_encode( $data );

    $ch = curl_init();
    curl_setopt( $ch, CURLOPT_URL, 'https://'. self::$mailchimp_data_center .'.api.mailchimp.com/3.0/lists/'. self::$mailchimp_list_id .'/members/' );
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Content-Type: application/json', 'Authorization: Basic '. $auth ) );
    curl_setopt( $ch, CURLOPT_USERAGENT, 'PHP-MCAPI/3.0' );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_TIMEOUT, 10 );
    curl_setopt( $ch, CURLOPT_POST, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_POSTFIELDS, $json_data );

    $result = curl_exec( $ch );

    $subscribed = false;
    $result_array = json_decode($result, true);
    foreach( $result_array as $key => $value ) {
      if( $key == "status" && ( $value == 'pending' || $value == 400 ) ) {
        $subscribed = true;
      }
    }

    return $subscribed;
  }

}

new MailChimpSubscribe();

?>
