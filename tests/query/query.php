<?php

class Tests_Post_Query extends WP_UnitTestCase {

	/**
	 *
	 * @ticket 17065
	 */
	function test_orderby_array() {
		global $wpdb;

		$q1 = new ES_WP_Query( array(
			'orderby' => array(
				'type' => 'DESC',
				'name' => 'ASC'
			)
		) );
		$this->assertEquals( 'desc', $q1->es_args['sort'][0]['post_type'] );
		$this->assertEquals( 'asc', $q1->es_args['sort'][1]['post_name'] );

		$q2 = new ES_WP_Query( array( 'orderby' => array() ) );
		$this->assertFalse( isset( $q2->es_args['sort'] ) );

		$q3 = new ES_WP_Query( array( 'post_type' => 'post' ) );
		$this->assertEquals( 'desc', $q3->es_args['sort'][0]['post_date.date'] );
	}

	/**
	 *
	 * @ticket 17065
	 */
	function test_order() {
		global $wpdb;

		$q1 = new ES_WP_Query( array(
			'orderby' => array(
				'post_type' => 'foo'
			)
		) );
		$this->assertEquals( 'desc', $q1->es_args['sort'][0]['post_type'] );

		$q2 = new ES_WP_Query( array(
			'orderby' => 'title',
			'order'   => 'foo'
		) );
		$this->assertEquals( 'desc', $q2->es_args['sort'][0]['post_title'] );

		$q3 = new ES_WP_Query( array(
			'order' => 'asc'
		) );
		$this->assertEquals( 'asc', $q3->es_args['sort'][0]['post_date.date'] );
	}

	/**
	 * @ticket 29629
	 */
	function test_orderby() {
		// 'none' is a valid value
		$q3 = new ES_WP_Query( array( 'orderby' => 'none' ) );
		$this->assertFalse( isset( $q3->es_args['sort'] ) );

		// false is a valid value
		$q4 = new ES_WP_Query( array( 'orderby' => false ) );
		$this->assertFalse( isset( $q4->es_args['sort'] ) );

		// empty array() is a valid value
		$q5 = new ES_WP_Query( array( 'orderby' => array() ) );
		$this->assertFalse( isset( $q5->es_args['sort'] ) );
	}

	function test_orderby_post__in() {
		$p_a = $this->factory->post->create();
		$p_b = $this->factory->post->create();
		$p_c = $this->factory->post->create();
		$p_d = $this->factory->post->create();
		es_wp_query_index_test_data();

		$post__in = [
			$p_c,
			$p_a,
			$p_d,
			$p_b,
		];

		$q = new ES_WP_Query( [
			'post__in' => $post__in,
			'orderby' => 'post__in',
			'order' => 'ASC',
			'posts_per_page' => 4,
		] );

		$this->assertNotEmpty( $q->posts );

		// Verify that the post is in the proper array.
		foreach ( $q->posts as $post ) {
			$this->assertTrue( in_array( $post->ID, $post__in, true ) );
		}

		// Assert that the order matches
		foreach ( $post__in as $i => $post_ID ) {
			$this->assertEquals(
				$post_ID,
				$q->posts[ $i ]->ID,
				'Post not in expected order from `post__in`.'
			);
		}
	}

	function test_post_name__in() {
		$post_a = $this->factory->post->create( [ 'post_name' => 'post-a' ] );
		$post_b = $this->factory->post->create( [ 'post_name' => 'post-b' ] );
		$post_c = $this->factory->post->create( [ 'post_name' => 'post-c' ] );
		es_wp_query_index_test_data();

		$post_name__in = [
			'post-c',
			'post-a',
			'post-b',
		];

		$q = new ES_WP_Query( [
			'post_name__in' => $post_name__in,
			'orderby' => 'post_name__in',
			'order' => 'ASC',
		] );

		$this->assertNotEmpty( $q->posts );

		// Verify that the post name is in the proper array.
		foreach ( $q->posts as $post ) {
			$this->assertTrue( in_array( $post->post_name, $post_name__in, true ) );
		}

		// Assert that the order matches
		foreach ( $post_name__in as $i => $post_name ) {
			$this->assertEquals(
				$post_name,
				$q->posts[ $i ]->post_name,
				'Post not in expected order from `post_name__in`.'
			);
		}
	}

	public function test_orderby_post_parent__in() {
		
	}
}
