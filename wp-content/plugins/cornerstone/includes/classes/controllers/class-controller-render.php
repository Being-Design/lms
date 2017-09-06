<?php

class Cornerstone_Controller_Render extends Cornerstone_Plugin_Component {

  public function bar_module( $data ) {

    if ( ! isset( $data['batch'] ) ) {
      return array();
    }

    do_action('cs_bar_preview_setup');
    $renderer = $this->plugin->loadComponent( 'App_Renderer' );
    $renderer->start();

    foreach ($data['batch'] as $key => $value) {
      $data['batch'][$key]['response'] = $renderer->bar_module( $value['data'] );
    }

    $extractions = $renderer->get_extractions();
    $renderer->end();

    return array(
      'batch' => $data['batch'],
      'extractions' => $extractions,
      'debug' => $renderer->enqueue_extractor->counter
    );
  }

}
