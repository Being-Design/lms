<?php

class Cornerstone_Preview_Frame_Loader extends Cornerstone_Plugin_Component {

  protected $state = false;
  protected $zones = array();
  protected $frame = null;

  public function setup() {


    if ( ! isset( $_POST['cs_preview_state'] ) || ! $_POST['cs_preview_state'] || 'off' === $_POST['cs_preview_state'] ) {
      return;
    }

    // Nonce verification
    if ( ! isset( $_POST['_cs_nonce'] ) || ! wp_verify_nonce( $_POST['_cs_nonce'], 'cornerstone_nonce' ) ) {
      echo -1;
      die();
    }

    add_filter( 'show_admin_bar', '__return_false' );
    add_action( 'template_redirect', array( $this, 'load' ), 0 );
    add_action( 'shutdown', array( $this, 'frame_signature' ), 1000 );
    add_filter( 'wp_die_handler', array( $this, 'remove_preview_signature' ) );

    $this->state = json_decode( base64_decode( $_POST['cs_preview_state'] ), true );

    $route = ( isset( $this->state['route'] ) ) ? $this->state['route'] : 'app';
    $frame_component = cs_to_component_name( $route ) . '_Preview_Frame';
    $this->frame = $this->plugin->loadComponent( $frame_component );

    if ( ! $this->frame ) {
      throw new Exception( "Requested frame handler '$frame_component' does not exist." );
    }

    if ( isset( $this->state['noClient'] ) ) {
      return;
    }

    add_action( 'wp_enqueue_scripts', array( $this, 'enqueue' ) );
    add_action( 'wp_footer', array( $this, 'route_config' ) );

    foreach ( $this->zones as $zone ) {
      add_action( $zone, array( $this, 'zone_output' ) );
    }

  }

  public function load() {
    nocache_headers();
  }

  public function register_zones( $zones ) {
    $this->zones = array_unique( array_merge( $this->zones, $zones ) );
  }

  public function zone_output() {
    echo '<div data-cs-zone="' . current_action() . '"></div>';
  }

  public function get_state() {
    return $this->state;
  }

  public function data() {

    if ( ! $this->state ) {
      return array( 'timestamp' => $this->state);
    }

    return array(
      'timestamp' => $this->state['timestamp']
    );

  }

  public function frame_signature() {
    echo 'CORNERSTONE_FRAME';
  }

  public function remove_preview_signature( $return = null ) {
    remove_action( 'shutdown', array( $this, 'frame_signature' ), 1000 );
    return $return;
  }

  public function enqueue() {
    $this->plugin->loadComponent( 'App' )->register_app_scripts( $this->plugin->settings(), true );
    wp_enqueue_script( 'cs-app' );
    wp_enqueue_style( 'cs-preview', $this->plugin->css( 'preview', true ), null, $this->plugin->version() );
  }

  public function route_config() {


    if ( isset( $this->state['route'] ) ) {
      echo '<script type="application/json" data-cs-preview-route="' . $this->state['route'] . '">';
      if ( is_callable( array( $this->frame, 'config' ) ) ) {
        echo json_encode( $this->frame->config( $this->state ) );
      }
      echo '</script>';
    }

    // echo '<script type="application/json" data-cs-preview="' . $this->state['component'] . '">';
    // echo json_encode( $config );
    // echo '</script>';

  }

  public function container_output() {
    echo '{{yield}}';
  }

}
