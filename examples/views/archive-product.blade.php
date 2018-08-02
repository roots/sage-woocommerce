@extends('layouts.app')

@section('content')
  @php
    do_action('get_header', 'shop');
    do_action('woocommerce_before_main_content');
  @endphp

  <header class="woocommerce-products-header">
    @if(apply_filters('woocommerce_show_page_title', true))
      <h1 class="woocommerce-products-header__title page-title"><?php woocommerce_page_title(); ?></h1>
    @endif

    @php
      do_action('woocommerce_archive_description');
    @endphp
  </header>

  @if(woocommerce_product_loop())
    @php
      do_action('woocommerce_before_shop_loop');
      woocommerce_product_loop_start();
    @endphp

    @if(wc_get_loop_prop('total'))
      @while(have_posts())
        @php
          the_post();
          do_action('woocommerce_shop_loop');
          wc_get_template_part('content', 'product');
        @endphp
      @endwhile
    @endif

    @php
      woocommerce_product_loop_end();
      do_action('woocommerce_after_shop_loop');
    @endphp
  @else
    @php
      do_action('woocommerce_no_products_found');
    @endphp
  @endif

  @php
    do_action('woocommerce_after_main_content');
    do_action('get_sidebar', 'shop');
    do_action('get_footer', 'shop');
  @endphp
@endsection
