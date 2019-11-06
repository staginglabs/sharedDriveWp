<?php
/**
 * Shop breadcrumb
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/global/breadcrumb.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see         https://docs.woocommerce.com/document/template-structure/
 * @package     WooCommerce/Templates
 * @version     2.3.0
 * @see         woocommerce_breadcrumb()
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

if ( $breadcrumb ) {
    if(is_product_category()){ ?>
<div class="et_pb_section  camp_back et_section_regular product_cat_breadcrum">
        <div class="et_pb_row et_pb_row_0">
            <div class="et_pb_column  et_pb_column_0  et_pb_css_mix_blend_mode_passthrough">
              <div class="et_pb_module et_pb_text et_pb_text_0 et_pb_bg_layout_light  et_pb_text_align_left">
                <div class="et_pb_text_inner">
                   <div class="breadcrumbs">
                    <h1 class="title_breadcrumbs"><?php echo $breadcrumb[count($breadcrumb)-1][0]; ?></h1>
                     <ul>
    <?php 
    foreach ( $breadcrumb as $key => $crumb ) {

        echo $before;

        if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) {
            echo '<li><a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a></li>';
        } else {
            echo esc_html( $crumb[0] );
        }

        

        if ( sizeof( $breadcrumb ) !== $key + 1 ) {
            echo $delimiter;
        }
    }
    ?>
</ul>
</div>
</div>
  </div> 
    </div>
      </div> 
                
                
            </div>

<?php }else{

        echo $after;
    // add shop home url to breadcrumbs
    if(is_product_tag() || is_product() || is_shop() ) {
        $shop_page_id = wc_get_page_id( 'shop' );
        $shop_home_arr = array( get_the_title($shop_page_id), get_permalink($shop_page_id));

        // insert to breadcrumbs array on second position
        array_splice($breadcrumb, 1, 0, array($shop_home_arr));
    }

    echo $wrap_before;
    if(is_product()){ ?>
        <h1 class="title_breadcrumbs"><?php echo $breadcrumb[count($breadcrumb)-1][0]; ?></h1>
    <?php }

    foreach ( $breadcrumb as $key => $crumb ) {

        echo $before;

        if ( ! empty( $crumb[1] ) && sizeof( $breadcrumb ) !== $key + 1 ) {
            echo '<a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a>';
        } else {
            echo esc_html( $crumb[0] );
        }

        echo $after;

        if ( sizeof( $breadcrumb ) !== $key + 1 ) {
            echo $delimiter;
        }
    }
  echo $wrap_after;

}


    
  
}