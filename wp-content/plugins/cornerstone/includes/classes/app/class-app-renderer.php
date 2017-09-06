<?php

class Cornerstone_App_Renderer extends Cornerstone_Plugin_Component {

  public $dependencies = array( 'Front_End' );
  public $zones = array();
  public $zone_output = array();

  public function start() {
    $this->enqueue_extractor = $this->plugin->loadComponent( 'Enqueue_Extractor' );
    $this->enqueue_extractor->start();
  }

  public function zone_siphen_start() {
    ob_start();
  }

  public function zone_siphen_end() {

    $content = ob_get_clean();

    if ( $content ) {
      $this->zone_output[current_action()] = $content;
    }

  }

  public function end() {

  }

  public function register_zones( $zones ) {
    $this->zones = array_unique( array_merge( $this->zones, $zones ) );
  }

  public function get_extractions() {
    return array(
      'scripts' => $this->enqueue_extractor->get_scripts(),
      'styles'  => $this->enqueue_extractor->get_styles()
    );
  }

  public function bar_module( $data ) {

    $response = '';
    $this->zone_output = array();

    if ( 'markup' === $data['action'] ) {

      $module = array();
      $definition = CS()->loadComponent('Element_Manager')->get_element( $data['model']['_type'] );

      /**
       * Attach zone output siphens
       */

      foreach ( $this->zones as $zone ) {
        remove_all_actions( $zone );
        add_action( $zone, array( $this, 'zone_siphen_start' ), 0 );
      }

      $attr_keys = $definition->get_designated_keys( 'attr' );
      $html_keys = $definition->get_designated_keys('markup', 'html' );

      /**
       * Replace keys designated as attributes with {{atts.key_name}}
       */


      foreach ($attr_keys as $key) {
        $module[$key] = "{{model.atts.$key}}"; //"{{model.{{camelize::attr_$key}}}}";
      }

      foreach ($data['model'] as $key => $value) {

        if ( in_array($key, $attr_keys, true) ) {
          continue;
        }

        if ( in_array($key, $html_keys, true) ) {
          $module[$key] = $this->isolate_html( $value );
          continue;
        }

        $module[$key] = $value;

      }

      if ( isset($module['_id'])) {
        $module['_id'] = '{{model.id}}';
      }

      /**
       * Render the module using a registered filter
       */
      ob_start();
      $definition->render( $module );
      $response = ob_get_clean();

      /**
       * Add data-cs-observeable on root element if not supplied by view
       */
      if ( -1 !== strpos($response, 'data-cs-observeable' ) ) {
        $response = preg_replace('/<\s*?\w+\s?/', "$0 data-cs-observeable=\"{{observer}}\" ", $response, 1 );
      }


      /**
       * Capture output that was deffered into any registered zones
       */

      foreach ( $this->zones as $zone ) {
        add_action( $zone, array( $this, 'zone_siphen_end' ), 9999999 );
        do_action( $zone );
      }

      foreach ($this->zone_output as $key => $value) {
        $html = preg_replace('/<!--(.|\n)*?-->/', '', $value);
        $encoded = json_encode( array( 'markup' => base64_encode($html) ) );
        $response .= "{{#preview/zone-pipe model=model zone=\"$key\"}}$encoded{{/preview/zone-pipe}}";
      }

    }

    return array(
      'template' => $response,
      'extractions' => array(
        'scripts' => $this->enqueue_extractor->extract_scripts(),
        'styles' => $this->enqueue_extractor->extract_styles()
      )
    );
  }

  public function isolate_html( $content ) {
    $content = base64_encode( do_shortcode( $content ) );
    return "{{base64content \"$content\" }}";
  }

}
