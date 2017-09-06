<?php

class Cornerstone_Element_Manager extends Cornerstone_Plugin_Component {

  protected $elements = array();
  protected $class_prefixes = array();

  public function setup() {
    $this->register_internal_elements();
  }

  public function register_element( $name, $element ) {
    if ( isset( $this->elements[ $name ] ) ) {
      $this->elements[ $name ]->update( $element );
    }
    $this->elements[ $name ] = new Cornerstone_Element_Definition( $name, $element );
  }

  public function unregister_element( $name ) {
    unset( $this->elements[ $name ] );
  }

  public function get_element( $name ) {
    return isset( $this->elements[ $name ] ) ? $this->elements[ $name ] : $this->elements['undefined'];
  }

  public function get_elements() {
    $elements = array();

    foreach ($this->elements as $element) {
      $elements[] = $element->serialize();
    }

    return $elements;
  }

  public function register_internal_elements() {

    $this->register_element('undefined', array(
      'title' => csi18n('elements.undefined-title')
    ) );
    $this->register_element('root', array() );
    $this->register_element('region', array() );

    $this->register_element('bar', array(
      'title' => csi18n('elements.bar-title')
    ) );

    $this->register_element('container', array(
      'title' => csi18n('elements.container-title')
    ) );

  }

  public function set_class_prefix( $mode, $class_prefix ) {
    $this->class_prefixes[$mode] = $class_prefix;
  }

  public function generate_styles( $mode, $elements ) {

     $class_prefix = isset( $this->class_prefixes[$mode] ) ? $this->class_prefixes[$mode] : 'el';
     $sorted = $this->sort_into_types( $elements );

     $coalescence = $this->plugin->loadComponent( 'Coalescence' )->start();


     foreach ($sorted as $type => $elements) {

        // Load the style template for each type being used
        $type_definition = $this->get_element( $type );
        // $coalescence->add_precompiled_template( $type, $type_definition->get_compiled_style() );

        $coalescence->add_template( $type, $type_definition->get_style_template() );

        // Preprocess styles.
        // This applies defaults and wraps retroactive properties
        // in a way that they can be expanded later
        foreach ($elements as $index => $data) {
          $sorted[$type][$index] = $type_definition->preprocess_style( $data, $class_prefix );
        }

        $coalescence->add_items( $type, $sorted[$type] );
     }
     //die();
    //  echo '<pre>';var_dump($coalescence->run());var_dump($sorted['bar']);die();

    return $coalescence->run();

  }

  public function compile_style_template( $template_string ) {
    $template = $this->plugin->loadComponent( 'Coalescence' )->create_template( $template_string );
    return $template->serialize();
  }

  public function sort_into_types( $elements ) {

    $this->sorting_sets = array();

    $walker = new Cornerstone_Walker( array(
      '_modules' => $elements
    ) );

    $walker->walk( array( $this, 'sort_into_types_callback' ) );
    ksort($this->sorting_sets);

    $sorting_sets = $this->sorting_sets;
    unset($this->sorting_sets);

    return $sorting_sets;

  }

  public function sort_into_types_callback( $walker ) {
    $data = $walker->data();
    if ( ! isset( $data['_type'] ) ) {
      return;
    }

    if ( ! isset( $this->sorting_sets[$data['_type']] ) ) {
      $this->sorting_sets[$data['_type']] = array();
    }

    unset($data['_modules']);
    $this->sorting_sets[$data['_type']][] = $data;

  }


  public function get_elements_of_type( $type, $elements ) {
    $types = $this->sort_into_types( $elements );
    return $types[$type];
  }

  public function sanitize_element( $data ) {
    $definition = $this->get_element( isset( $data['_type'] ) ? $data['_type'] : 'undefined' );
    return $definition->sanitize( $data );
  }

  public function sanitize_elements( $elements ) {
    $sanitized = array();
    foreach ($elements as $element) {
      if ( isset( $element['_modules'] ) ) {
        $element['_modules'] = $this->sanitize_elements( $element['_modules'] );
      }
      $sanitized[] = $this->sanitize_element( $element );
    }
    return $sanitized;
  }

}
