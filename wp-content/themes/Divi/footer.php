<?php
/**
 * Fires after the main content, before the footer is output.
 *
 * @since 3.10
 */
?>
<div class="top_footer">
 <?php get_sidebar( 'footer' ); ?>
</div>
<?php
do_action( 'et_after_main_content' );

if ( 'on' === et_get_option( 'divi_back_to_top', 'false' ) ) : ?>

	<span class="et_pb_scroll_top et-pb-icon"></span>

<?php endif;

if ( ! is_page_template( 'page-template-blank.php' ) ) : ?>

			<footer id="main-footer">


				<?php get_sidebar( 'footer' ); ?>


		<?php
			if ( has_nav_menu( 'footer-menu' ) ) : ?>

				<div id="et-footer-nav">
					<div class="container">
						<?php
							wp_nav_menu( array(
								'theme_location' => 'footer-menu',
								'depth'          => '1',
								'menu_class'     => 'bottom-nav',
								'container'      => '',
								'fallback_cb'    => '',
							) );
						?>
					</div>
				</div> <!-- #et-footer-nav -->

			<?php endif; ?>

				<div id="footer-bottom">
					<div class="container clearfix">
				<?php
					if ( false !== et_get_option( 'show_footer_social_icons', true ) ) {
						get_template_part( 'includes/social_icons', 'footer' );
					}

					// phpcs:disable WordPress.Security.EscapeOutput.OutputNotEscaped
					echo et_core_fix_unclosed_html_tags( et_core_esc_previously( et_get_footer_credits() ) );
					// phpcs:enable
				?>
					</div>	<!-- .container -->
				</div>
			</footer> <!-- #main-footer -->
		</div> <!-- #et-main-area -->

<?php endif; // ! is_page_template( 'page-template-blank.php' ) ?>

	</div> <!-- #page-container -->

	<?php wp_footer(); ?>
	<!--<script type="text/javascript" src="<?php bloginfo('url'); ?>/wp-content/themes/Divi/js/cookie.js"></script>-->
	<script type="text/javascript">
	function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}
function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}
//alert(readCookie('uploaded_file_url'));
 jQuery('#file_upload_url').val(readCookie('uploaded_file_url'));
	   jQuery(document).on('change', '#file_upload', function (e) {

         var file_data = jQuery('#file_upload').prop('files')[0];

          var form_data = new FormData();
          jQuery(".Success-div").show();
      

          form_data.append('file', file_data);
          form_data.append('action', 'woocommerce_checkout_file_upload');
          jQuery.ajax({
              url: '<?php echo admin_url('admin-ajax.php'); ?>',
              type: 'post',
              contentType: false,
              processData: false,
              data: form_data,
              success: function (response) {
                  jQuery('#file_upload_url').val(response);
                  createCookie('uploaded_file_url',response,1);
                  jQuery(".Success-div").hide();
                  jQuery(".success-div-response").html('<div class="checkout_message"><div class="woocommerce-message add-message" role="success"><p class="woocommerce-success">File Uploaded Successfully!!</p></div></div>');
                  
                
              },  
              error: function (response) {
               console.log('error');
               jQuery(".success-div-response").html('<div class="checkout_message"><div class="woocommerce-notices-wrapper"><ul class="woocommerce-error" role="alert"><li>Some thing wents wrong when uploading a file!!</li></ul></div></div>');
                jQuery(".Success-div").hide();
              }

          });
                    });
         jQuery(document).ready(function () {
         	 jQuery('#place_order').focus(function () {
         	 	createCookie('uploaded_file_url'," ",-1);
         	 	});
         	});
         	/*jQuery(document).on('change', '#order_file_upload', function (e) {
         		
//var file_data= jQuery.serialize(order_file_upload);
         var file_data = jQuery('#order_file_upload').val();
         alert(file_data);

          var form_data = new FormData();
          jQuery(".Success-div").show();
      

          form_data.append('file', file_data);
          form_data.append('action', 'woocommerce_order_file_upload');
          jQuery.ajax({
              url: '<?php echo admin_url('admin-ajax.php'); ?>',
              type: 'post',
              contentType: false,
              processData: false,
              data: form_data,
              success: function (response) {
              	alert(response);
                  jQuery('#fileorder_upload_url').val(response);
                  jQuery(".Success-div").hide();
                
              },  
              error: function (response) {
               console.log('error');
              }

          });
                    });*/
   


jQuery(document).on('click', '.downloaded_link', function (e) {
 	 var dataurl= jQuery(this).attr("data-url");
 	 window.location.href="/response-downloader?link="+dataurl;

 });

// jQuery(document).on('click', '.downloaded_link', function (e) {
// 	 var dataurl= jQuery(this).attr("data-url");
// 	 var form_data = new FormData();
// 	 form_data.append('action', 'downloaded_uploaded_file');
// 	 form_data.append('data_url', dataurl);
// 	jQuery.ajax({
//               url: '<?php echo admin_url('admin-ajax.php'); ?>',
//               type: 'post',
//               contentType: false,
//               processData: false,
//               data: form_data,
//               success: function (response) {
//                       xhttp = new XMLHttpRequest();
//                           xhttp.setRequestHeader("Content-Type", "application/json");
//     					// You should set responseType as blob for binary responses
// 					    xhttp.responseType = 'blob';
// 					    xhttp.send(JSON.stringify(response));


//                   //jQuery(".Success-div").hide();
                
//               },  
//               error: function (response) {
//                console.log('error');
//               }

//           });



// });

jQuery("#open_tabs").click(function(){
	if(jQuery(".sidebar-shop").hasClass("active")){
		jQuery(this).text("Show all Categoría")
		jQuery(this).removeClass('show');

		jQuery(".sidebar-shop").removeClass("active");	
	}else{
		jQuery(this).addClass('show');
		jQuery(this).text("Hide all Categoría")
			jQuery(".sidebar-shop").addClass("active");	
	}
	

})

jQuery('label.tab-label').click(function () {
	if(jQuery(this).parent().hasClass("active")){
		jQuery(this).parent().removeClass('active')
	}
	else{
		jQuery(this).parent().addClass("active")
	}
})


jQuery(document).on('click', '.delete_order_attachment', function (e) {
	 var dataurl= jQuery(this).attr("id");
	 var dataorder_id= jQuery(this).attr("data-order-id");
	 var form_data = new FormData();
	 form_data.append('action', 'delete_order_attachment');
	 form_data.append('data_url', dataurl);
	 form_data.append('order_id', dataorder_id);
	jQuery.ajax({
              url: '<?php echo admin_url('admin-ajax.php'); ?>',
              type: 'post',
              contentType: false,
              processData: false,
              data: form_data,
              success: function (response) {
                    location.reload();

                  //jQuery(".Success-div").hide();
                
              },  
              error: function (response) {
               console.log('error');
              }

          });



});

	</script>
</body>
</html>
