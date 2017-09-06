<?php

class Cornerstone_Model_Content_Content extends Cornerstone_Plugin_Component {

  public $dependencies = array( 'Regions', 'Footer_Assignments' );
  public $resources = array();
  public $name = 'content/content';

  public function load_all() {

    $posts = get_posts( array(
      'post_type' => $this->plugin->common()->getAllowedPostTypes(),
      'post_status' => 'any',
      'orderby' => 'type',
      'posts_per_page' => 2500
    ) );

    foreach ($posts as $post) {
      $records[] = $this->post_to_record( $post );
    }

    foreach ($records as $record) {
      $this->resources[] = $this->to_resource( $record );
    }
  }

  public function post_to_record( $post ) {
    $post_type_obj = get_post_type_object( $post->post_type );

    return array(
      'id' => "$post->ID",
      'title' => $post->post_title,
      'post-type' => $post->post_type,
      'post-type-label' => isset( $post_type_obj->labels ) ? $post_type_obj->labels->singular_name : $post->post_type,
      'modified' => date_i18n( get_option( 'date_format' ), strtotime( $post->post_modified ) ),
      'permalink' => get_permalink( $post )
    );
  }

  public function query( $params ) {

    // Find All
    if ( empty( $params ) || ! isset( $params['query'] ) ) {
      $this->load_all();
      return $this->make_response( $this->resources );
    }

    $queried = array();
    $this->included = array();

    if ( isset( $params['query']['id'] ) ) {
      $post = get_post( (int) $params['query']['id'] );

      if ( ! is_null( $post ) ) {
        $queried[] = $this->to_resource( $this->post_to_record( $post ) );
      }

    }

    return $this->make_response( ( isset( $params['single'] ) && isset( $queried[0] ) ) ? $queried[0] : $queried );

  }


  public function make_response( $data ) {

    $response = array(
      'data' => $data
    );

    if ( isset( $this->included ) ) {
      $response['included'] = $this->included;
    }

    return $response;

  }

  public function to_resource( $record ) {

    $resource = array(
      'id' => $record['id'],
      'type' => $this->name
    );

    unset( $record['id'] );
    $resource['attributes'] = $record;

    return $resource;

  }

  public function create( $params ) {
    $atts = $this->atts_from_request( $params );
    $footer = new Cornerstone_Footer( $atts );
    return $this->make_response( $this->to_resource( $footer->save() ) );
  }

  protected function atts_from_request( $params ) {

    if ( ! isset( $params['model'] ) || ! isset( $params['model']['data'] ) || ! isset( $params['model']['data']['attributes'] ) ) {
      throw new Exception( 'Request to Footer model missing attributes.' );
    }

    $atts = $params['model']['data']['attributes'];

    if ( isset( $params['model']['data']['id'] ) ) {
      $atts['id'] = $params['model']['data']['id'];
    }

    return $atts;
  }

  public function delete( $params ) {
    $atts = $this->atts_from_request( $params );

    if ( ! $atts['id'] ) {
      throw new Exception( 'Attempting to delete Footer without specifying an ID.' );
    }

    $id = (int) $atts['id'];

    $footer = new Cornerstone_Footer( $id );
    $footer->delete();

    return $this->make_response( array( 'id' => $id, 'type' => $this->name ) );
  }

  public function update( $params ) {

    $atts = $this->atts_from_request( $params );

    if ( ! $atts['id'] ) {
      throw new Exception( 'Attempting to update Footer without specifying an ID.' );
    }

    $id = (int) $atts['id'];

    $footer = new Cornerstone_Footer( $id );

    if ( isset( $atts['regions'] ) ) {
      $footer->set_regions( $atts['regions'] );
    }

    return $this->make_response( $this->to_resource( $footer->save() ) );
  }

}
