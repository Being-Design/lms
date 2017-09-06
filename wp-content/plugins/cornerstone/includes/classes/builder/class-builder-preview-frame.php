<?php

class Cornerstone_Builder_Preview_Frame extends Cornerstone_Plugin_Component {

  protected $data;

  public function setup() {

    $state = $this->plugin->component( 'Preview_Frame_Loader' )->get_state();
    add_filter('_cornerstone_classic_redirect', '__return_false' );
    add_filter('cornerstone_config_data', array( $this, 'update_config' ) );
  }

  public function update_config( $config ) {
    $config['classicMode'] = false;
    $config['previewData'] = $this->plugin->component( 'Preview_Frame_Loader' )->data();
    return $config;
  }



}
