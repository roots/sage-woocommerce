@extends('layouts.app')

@section('content')
  @php
    do_action('get_header', 'shop');
    do_action('woocommerce_before_main_content');
  @endphp

  @while(have_posts())
    @php
      the_post();
      do_action('woocommerce_shop_loop');
      wc_get_template_part('content', 'single-product');
    @endphp
  @endwhile

  @php
    do_action('woocommerce_after_main_content');
    do_action('get_sidebar', 'shop');
    do_action('get_footer', 'shop');
  @endphp
@endsection
