<?php
function UWCF_Display_Filters_Block() {
    if(function_exists('render_block_core_block')){  
		wp_register_script( 'ewd-uwcf-blocks-js', plugins_url( '../blocks/ewd-uwcf-blocks.js', __FILE__ ), array( 'wp-blocks', 'wp-element', 'wp-components', 'wp-editor' ) );
		wp_register_style( 'ewd-uwcf-blocks-css', plugins_url( '../blocks/ewd-uwcf-blocks.css', __FILE__ ), array( 'wp-edit-blocks' ), filemtime( plugin_dir_path( __FILE__ ) . '../blocks/ewd-uwcf-blocks.css' ) );
		register_block_type( 'color-filters/ewd-uwcf-display-filters-block', array(
			'attributes'      => array(
			),
			'editor_script'   => 'ewd-uwcf-blocks-js',
			'editor_style'  => 'ewd-uwcf-blocks-css',
			'render_callback' => 'EWD_UWCF_Display_Filters',
		) );
	}
	// Define our shortcode, too, using the same render function as the block.
	add_shortcode('ultimate-woocommerce-filters', 'EWD_UWCF_Display_Filters');
}
add_action( 'init', 'UWCF_Display_Filters_Block' );

function EWD_UWCF_Display_Filters($atts) {
	global $wpdb;

	$Enable_Colors = get_option("EWD_UWCF_Enable_Colors");
	$Color_Filters_Display = get_option("EWD_UWCF_Color_Filters_Display");
	$Color_Filters_Show_Text = get_option("EWD_UWCF_Color_Filters_Show_Text");
	$Color_Filters_Show_Color = get_option("EWD_UWCF_Color_Filters_Show_Color");
	$Color_Filters_Hide_Empty = get_option("EWD_UWCF_Color_Filters_Hide_Empty");
	$Color_Filters_Show_Product_Count = get_option("EWD_UWCF_Color_Filters_Show_Product_Count");

	$Enable_Sizes = get_option("EWD_UWCF_Enable_Sizes");
	$Size_Filters_Display = get_option("EWD_UWCF_Size_Filters_Display");
	$Size_Filters_Show_Text = get_option("EWD_UWCF_Size_Filters_Show_Text");
	$Size_Filters_Hide_Empty = get_option("EWD_UWCF_Size_Filters_Hide_Empty");
	$Size_Filters_Show_Product_Count = get_option("EWD_UWCF_Size_Filters_Show_Product_Count");

	$Enable_Categories = get_option("EWD_UWCF_Enable_Categories");
	$Category_Filters_Display = get_option("EWD_UWCF_Category_Filters_Display");
	$Category_Filters_Show_Text = get_option("EWD_UWCF_Category_Filters_Show_Text");
	$Category_Filters_Hide_Empty = get_option("EWD_UWCF_Category_Filters_Hide_Empty");
	$Category_Filters_Show_Product_Count = get_option("EWD_UWCF_Category_Filters_Show_Product_Count");

	$Enable_Tags = get_option("EWD_UWCF_Enable_Tags");
	$Tag_Filters_Display = get_option("EWD_UWCF_Tag_Filters_Display");
	$Tag_Filters_Show_Text = get_option("EWD_UWCF_Tag_Filters_Show_Text");
	$Tag_Filters_Hide_Empty = get_option("EWD_UWCF_Tag_Filters_Hide_Empty");
	$Tag_Filters_Show_Product_Count = get_option("EWD_UWCF_Tag_Filters_Show_Product_Count");

	$Enable_Text_Search = get_option("EWD_UWCF_Enable_Text_Search");
	$Enable_Autocomplete = get_option("EWD_UWCF_Enable_Autocomplete");

	$Enable_Ratings_Filtering = get_option("EWD_UWCF_Enable_Ratings_Filtering");

	$Enable_InStock_Filtering = get_option("EWD_UWCF_Enable_InStock_Filtering");

	$Enable_OnSale_Filtering = get_option("EWD_UWCF_Enable_OnSale_Filtering");

	$Reset_All_Button = get_option("EWD_UWCF_Reset_All_Button");

	$Custom_CSS = get_option("EWD_UWCF_Custom_CSS");
	$Color_Filters_Color_Shape = get_option("EWD_UWCF_Color_Filters_Color_Shape");

	$Show_All_Colors_Label = get_option("EWD_UWCF_Show_All_Colors_Label");
	if($Show_All_Colors_Label == ''){ $Show_All_Colors_Label = __('Show All Colors', 'color-filters'); }
	$Show_All_Sizes_Label = get_option("EWD_UWCF_Show_All_Sizes_Label");
	if($Show_All_Sizes_Label == ''){ $Show_All_Sizes_Label = __('Show All Sizes', 'color-filters'); }
	$Show_All_Categories_Label = get_option("EWD_UWCF_Show_All_Categories_Label");
	if($Show_All_Categories_Label == ''){ $Show_All_Categories_Label = __('Show All Categories', 'color-filters'); }
	$Show_All_Tags_Label = get_option("EWD_UWCF_Show_All_Tags_Label");
	if($Show_All_Tags_Label == ''){ $Show_All_Tags_Label = __('Show All Tags', 'color-filters'); }
	$Show_All_Attributes_Label = get_option("EWD_UWCF_Show_All_Attributes_Label");
	if($Show_All_Attributes_Label == ''){ $Show_All_Attributes_Label = __('Show All ', 'color-filters'); }
	$Rating_Label = get_option("EWD_UWCF_Rating_Label");
	if($Rating_Label == ''){ $Rating_Label = __('Rating', 'color-filters'); }

	// Get the attributes passed by the shortcode, and store them in new variables for processing
	extract( shortcode_atts( array(
			'search_string' => ""),
			$atts
		)
	);

	$ReturnString = "<div class='ewd-uwcf-filters'>";
	$ReturnString .= EWD_UWCF_Add_Modified_Styles();
	$ReturnString .= ($Custom_CSS != '' ? "<style>" . $Custom_CSS . "</style>" : '');
	$ReturnString .= "<form id='ewd-uwcf-filtering-form' data-shopurl='" . get_permalink(wc_get_page_id('shop')) . "' data-min_price='" . htmlspecialchars($_GET['min_price']) . "' data-max_price='" . htmlspecialchars($_GET['max_price']) . "'>";

	if ($Reset_All_Button == "Yes") {
		$ReturnString .= "<div class='ewd-uwcf-reset-all'>";
		$ReturnString .= __("Reset All Filters", 'color-filters');
		$ReturnString .= "</div>";
	}

	if ($Enable_Text_Search == "Yes") {
		$ReturnString .= "<div class='ewd-uwcf-text-search'>";
		$ReturnString .= "<input type='text' class='ewd-uwcf-text-search-input " . ($Enable_Autocomplete == "Yes" ? 'ewd-uwcf-text-search-autocomplete' : '') . "' name='ewd-uwcf-text-search-input' placeholder='" . __("Search Products...", 'color-filters') . "' />";
		if ($Enable_Autocomplete != "Yes") {$ReturnString .= "<div class='ewd-uwcf-text-search-submit'>" . __("Search", 'color-filters') . "</div>";}
		$ReturnString .= "</div>";
	}

	if ($Enable_Ratings_Filtering == "Yes") {
		$ReturnString .= "<div class='ewd-uwcf-ratings-slider-title'>" . $Rating_Label . "</div>";
		$ReturnString .= "<div id='ewd-uwcf-ratings-slider' data-min_rating='" . (isset($_GET['min_rating']) ? htmlspecialchars($_GET['min_rating']) : 1) . "' data-max_rating='" . (isset($_GET['max_rating']) ? htmlspecialchars($_GET['max_rating']) : 5) . "'></div>";
		$ReturnString .= "<div class='ewd-uwcf-ratings-slider-min'>" . (isset($_GET['min_rating']) ? htmlspecialchars($_GET['min_rating']) : 1) . "</div>";
		$ReturnString .= "<div class='ewd-uwcf-ratings-slider-max'>" . (isset($_GET['max_rating']) ? htmlspecialchars($_GET['max_rating']) : 5) . "</div>";
	}

	if ($Enable_InStock_Filtering == "Yes") {
		$ReturnString .= "<div class='ewd-uwcf-instock-filtering'>";
		$ReturnString .= "<input type='checkbox' class='ewd-uwcf-instock-checkbox' name='ewd-uwcf-instock-checkbox' " . (isset($_GET['instock']) ? 'checked' : '') ." />";
		$ReturnString .= "<span class='ewd-uwcf-instock-text'>" . __("In-Stock Items Only", 'color-filters') . "</span>";
		$ReturnString .= "</div>";
	}

	if ($Enable_OnSale_Filtering == "Yes") {
		$ReturnString .= "<div class='ewd-uwcf-onsale-filtering'>";
		$ReturnString .= "<input type='checkbox' class='ewd-uwcf-onsale-checkbox' name='ewd-uwcf-onsale-checkbox' " . (isset($_GET['onsale']) ? 'checked' : '') ." />";
		$ReturnString .= "<span class='ewd-uwcf-onsale-text'>" . __("Sale Items Only", 'color-filters') . "</span>";
		$ReturnString .= "</div>";
	}

	$hide_empty = ($Color_Filters_Hide_Empty == "No" ? false : true);
		
	$get_terms = get_terms( 
		apply_filters( 'elm_cf_get_terms_args', array( 
			'hide_empty' => $hide_empty,
			'taxonomy' => 'product_color',
			'orderby' => 'meta_value_num',
			'meta_key' => 'EWD_UWCF_Term_Order'
		)) 
	);
		
	if ($Enable_Colors == "Yes" and $get_terms) {
		if (isset($_GET['product_color'])) {$Selected_Colors = explode(",", $_GET['product_color']);}
		else {$Selected_Colors = array();}

		$ReturnString .= "<div class='ewd-uwcf-color-filters-wrap ewd-uwcf-style-" . $Color_Filters_Display . "'>";

		if ($Color_Filters_Display == "Dropdown") {
			$CheckboxString = "";
			$ReturnString .= "<select class='ewd-uwcf-color-dropdown'>";
			$ReturnString .= "<option value='-1'>" . __("All Colors", 'color-filters') . "</option>";
		}

		foreach( $get_terms as $term ) { 
			$Color = get_term_meta($term->term_id, 'EWD_UWCF_Color', true);
			if (strpos($Color, 'http') === false) {$Style = ($Color != '' ? apply_filters( 'elm_cf_color_style_attribute', 'style="background: ' . $Color . ';"' ) : '');}
			else {$Style = 'style="background:url(\'' . $Color . '\'); background-size: cover;"';}

			if ($Color_Filters_Display != "Dropdown") {
				$ReturnString .= "<div class='ewd-uwcf-color-item text-" . $Color_Filters_Show_Text . "'>";
				$ReturnString .= "<input type='checkbox' class='ewd-uwcf-color ewd-uwcf-filtering-checkbox " . ($Color_Filters_Display == "Checklist" ? 'ewd-uwcf-checklist' : '') . "' value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Colors) ? 'checked' : '') . " />";

				if ($Color_Filters_Show_Color == "Yes") {
					$ReturnString .= "<div class='ewd-uwcf-color-wrap'>";
					$ReturnString .= "<div class='ewd-uwcf-color-preview " . (in_array($term->slug, $Selected_Colors) ? 'ewd-uwcf-selected' : '') . " " . ($Color_Filters_Color_Shape == "Circle" ? 'ewd-uwcf-rcorners' : '' ) . "' " . $Style . "></div>";
					$ReturnString .= "</div>";
				}

				if ($Color_Filters_Show_Text == "Yes") {
					$ReturnString .= "<div class='ewd-uwcf-color-link " . (in_array($term->slug, $Selected_Colors) ? 'ewd-uwcf-selected' : '') . "'>";
					$ReturnString .= "<span class='ewd-uwcf-color-name'>" . $term->name . "</span> ";
					$ReturnString .= ($Color_Filters_Show_Product_Count == "Yes" ? "(<span class='ewd-uwcf-product-count'>" . $term->count . "</span>)" : "");	
					$ReturnString .= "</div>";
				}

				$ReturnString .= "</div>";

			}
			else {
				$CheckboxString .= "<input type='checkbox' class='ewd-uwcf-color ewd-uwcf-filtering-checkbox' value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Colors) ? 'checked' : '') . " />";
				$ReturnString .= "<option value='" . $term->slug . "' " . $Style . " " . (in_array($term->slug, $Selected_Colors) ? 'selected' : '') . ">" . $term->name . ($Color_Filters_Show_Product_Count == "Yes" ? " (" . $term->count . ")" : "") . "</option>";
			}
		}

		if ($Color_Filters_Display == "Dropdown") {
			$ReturnString .= "</select>";
			$ReturnString .= $CheckboxString;
		}

		$ReturnString .= "</div>";

		if ($Color_Filters_Display != "Dropdown") {$ReturnString .= "<div class='ewd-uwcf-color-item ewd-uwcf-all-colors'>" . $Show_All_Colors_Label . "</div>";}
	}

	$hide_empty = ($Size_Filters_Hide_Empty == "No" ? false : true); $hide_empty = false;

	$get_terms = get_terms( 
		apply_filters( 'elm_cf_get_terms_args', array( 
			'hide_empty' => $hide_empty,
			'taxonomy' => 'product_size',
			'orderby' => 'meta_value_num',
			'meta_key' => 'EWD_UWCF_Term_Order'
		)) 
	);
	
	if ($Enable_Sizes == "Yes" and $get_terms) {
		if (isset($_GET['product_size'])) {$Selected_Sizes = explode(",", $_GET['product_size']);}
		else {$Selected_Sizes = array();}

		$ReturnString .= "<div class='ewd-uwcf-size-filters-wrap ewd-uwcf-style-" . $Size_Filters_Display . "'>";

		if ($Size_Filters_Display == "Dropdown") {
			$CheckboxString = "";
			$ReturnString .= "<select class='ewd-uwcf-size-dropdown'>";
			$ReturnString .= "<option value='-1'>" . __("All Sizes", 'color-filters') . "</option>";
		}

		foreach( $get_terms as $term ) { 
			if ($Size_Filters_Display != "Dropdown") {
				$ReturnString .= "<div class='ewd-uwcf-size-item'>";
	
				$ReturnString .= "<input type='checkbox' class='ewd-uwcf-size ewd-uwcf-filtering-checkbox " . ($Size_Filters_Display == "Checklist" ? 'ewd-uwcf-checklist' : '') . "' value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Sizes) ? 'checked' : '') . " />";
	
				$ReturnString .= "<div class='ewd-uwcf-size-link " . (in_array($term->slug, $Selected_Sizes) ? 'ewd-uwcf-selected' : '') . "'>";
				$ReturnString .= "<span class='ewd-uwcf-size-name'>" . $term->name . "</span> ";
				$ReturnString .= ($Size_Filters_Show_Product_Count == "Yes" ? "(<span class='ewd-uwcf-product-count'>" . $term->count . "</span>)" : "");	
				$ReturnString .= "</div>";
	
				$ReturnString .= "</div>";
			}
			else {
				$CheckboxString .= "<input type='checkbox' class='ewd-uwcf-size ewd-uwcf-filtering-checkbox' value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Sizes) ? 'checked' : '') . " />";
				$ReturnString .= "<option value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Sizes) ? 'selected' : '') . ">" . $term->name . ($Size_Filters_Show_Product_Count == "Yes" ? " (" . $term->count . ")" : "") . "</option>";
			}
		}

		if ($Size_Filters_Display == "Dropdown") {
			$ReturnString .= "</select>";
			$ReturnString .= $CheckboxString;
		}

		$ReturnString .= "</div>";

		if ($Size_Filters_Display != "Dropdown") {$ReturnString .= "<div class='ewd-uwcf-size-item ewd-uwcf-all-sizes'>" . $Show_All_Sizes_Label . "</div>";}
	}

	$hide_empty = ($Category_Filters_Hide_Empty == "No" ? false : true); $hide_empty = false;

	$get_terms = get_terms( 
		apply_filters( 'elm_cf_get_terms_args', array( 
			'hide_empty' => $hide_empty,
			'taxonomy' => 'product_cat'
		)) 
	);
	
	if ($Enable_Categories == "Yes" and $get_terms) {
		if (isset($_GET['product_cat'])) {$Selected_Categories = explode(",", $_GET['product_cat']);}
		else {$Selected_Categories = array();}

		$ReturnString .= "<div class='ewd-uwcf-category-filters-wrap ewd-uwcf-style-" . $Category_Filters_Display . "'>";

		if ($Category_Filters_Display == "Dropdown") {
			$CheckboxString = "";
			$ReturnString .= "<select class='ewd-uwcf-category-dropdown'>";
			$ReturnString .= "<option value='-1'>" . __("All Categories", 'color-filters') . "</option>";
		}

		foreach( $get_terms as $term ) {
			if ($Category_Filters_Display != "Dropdown") {
				$ReturnString .= "<div class='ewd-uwcf-category-item'>";
	
				$ReturnString .= "<input type='checkbox' class='ewd-uwcf-category ewd-uwcf-filtering-checkbox " . ($Category_Filters_Display == "Checklist" ? 'ewd-uwcf-checklist' : '') . "' value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Categories) ? 'checked' : '') . " />";
	
				$ReturnString .= "<div class='ewd-uwcf-category-link " . (in_array($term->slug, $Selected_Categories) ? 'ewd-uwcf-selected' : '') . "'>";
				$ReturnString .= "<span class='ewd-uwcf-category-name'>" . $term->name . "</span> ";
				$ReturnString .= ($Category_Filters_Show_Product_Count == "Yes" ? "(<span class='ewd-uwcf-product-count'>" . $term->count . "</span>)" : "");	
				$ReturnString .= "</div>";
	
				$ReturnString .= "</div>";
			}
			else {
				$CheckboxString .= "<input type='checkbox' class='ewd-uwcf-category ewd-uwcf-filtering-checkbox' value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Categories) ? 'checked' : '') . " />";
				$ReturnString .= "<option value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Categories) ? 'selected' : '') . ">" . $term->name . ($Category_Filters_Show_Product_Count == "Yes" ? " (" . $term->count . ")" : "") . "</option>";
			}
		}

		if ($Category_Filters_Display == "Dropdown") {
			$ReturnString .= "</select>";
			$ReturnString .= $CheckboxString;
		}

		$ReturnString .= "</div>";

		if ($Category_Filters_Display != "Dropdown") {$ReturnString .= "<div class='ewd-uwcf-category-item ewd-uwcf-all-categories'>" . $Show_All_Categories_Label . "</div>";}
	}

	$hide_empty = ($Tag_Filters_Hide_Empty == "No" ? false : true); $hide_empty = false;

	$get_terms = get_terms( 
		apply_filters( 'elm_cf_get_terms_args', array( 
			'hide_empty' => $hide_empty,
			'taxonomy' => 'product_tag'
		)) 
	);
	
	if ($Enable_Tags == "Yes" and $get_terms) {
		if (isset($_GET['product_tag'])) {$Selected_Tags = explode(",", $_GET['product_tag']);}
		else {$Selected_Tags = array();}

		$ReturnString .= "<div class='ewd-uwcf-tag-filters-wrap ewd-uwcf-style-" . $Tags_Filters_Display . "'>";

		if ($Tag_Filters_Display == "Dropdown") {
			$CheckboxString = "";
			$ReturnString .= "<select class='ewd-uwcf-tag-dropdown'>";
			$ReturnString .= "<option value='-1'>" . __("All Tags", 'color-filters') . "</option>";
		}

		foreach( $get_terms as $term ) {
			if ($Tag_Filters_Display != "Dropdown") {
				$ReturnString .= "<div class='ewd-uwcf-tag-item'>";
	
				$ReturnString .= "<input type='checkbox' class='ewd-uwcf-tag ewd-uwcf-filtering-checkbox " . ($Tags_Filters_Display == "Checklist" ? 'ewd-uwcf-checklist' : '') . "' value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Tags) ? 'checked' : '') . " />";
	
				$ReturnString .= "<div class='ewd-uwcf-tag-link " . (in_array($term->slug, $Selected_Tags) ? 'ewd-uwcf-selected' : '') . "'>";
				$ReturnString .= "<span class='ewd-uwcf-tag-name'>" . $term->name . "</span> ";
				$ReturnString .= ($Tags_Filters_Show_Product_Count == "Yes" ? "(<span class='ewd-uwcf-product-count'>" . $term->count . "</span>)" : "");	
				$ReturnString .= "</div>";
	
				$ReturnString .= "</div>";
			}
			else {
				$CheckboxString .= "<input type='checkbox' class='ewd-uwcf-tag ewd-uwcf-filtering-checkbox' value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Tags) ? 'checked' : '') . " />";
				$ReturnString .= "<option value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Tags) ? 'selected' : '') . ">" . $term->name . ($Tag_Filters_Show_Product_Count == "Yes" ? " (" . $term->count . ")" : "") . "</option>";
			}
		}

		if ($Tag_Filters_Display == "Dropdown") {
			$ReturnString .= "</select>";
			$ReturnString .= $CheckboxString;
		}

		$ReturnString .= "</div>";

		if ($Tag_Filters_Display != "Dropdown") {$ReturnString .= "<div class='ewd-uwcf-tag-item ewd-uwcf-all-tags'>" . $Show_All_Tags_Label . "</div>";}
	}

	$wc_attribute_table_name = $wpdb->prefix . "woocommerce_attribute_taxonomies";
	$attribute_taxonomies = $wpdb->get_results( "SELECT * FROM $wc_attribute_table_name order by attribute_name ASC;" );
	foreach ($attribute_taxonomies as $attribute_taxonomy) {
		if ($attribute_taxonomy->attribute_name == "ewd_uwcf_colors" or $attribute_taxonomy->attribute_name == "ewd_uwcf_sizes") {continue;}

		$Enabled = get_option("EWD_UWCF_" . $attribute_taxonomy->attribute_name . "_Enabled");

		if ($Enabled != "Yes") {continue;}

		$Display = get_option("EWD_UWCF_" . $attribute_taxonomy->attribute_name . "_Display");
    	$Show_Text = get_option("EWD_UWCF_" . $attribute_taxonomy->attribute_name . "_Show_Text");
    	$Hide_Empty = get_option("EWD_UWCF_" . $attribute_taxonomy->attribute_name . "_Hide_Empty");
    	$Product_Count = get_option("EWD_UWCF_" . $attribute_taxonomy->attribute_name . "_Product_Count");
    	$Thumbnail_Tags = get_option("EWD_UWCF_" . $attribute_taxonomy->attribute_name . "_Thumbnail_Tags");

		$hide_empty = ($Hide_Empty == "No" ? false : true);
		$get_terms = get_terms( 
			apply_filters( 'elm_cf_get_terms_args', array( 
				'hide_empty' => $hide_empty,
				'taxonomy' => 'pa_' . $attribute_taxonomy->attribute_name
			)) 
		); 

		if ($get_terms) {
			if (isset($_GET['pa_' . $attribute_taxonomy->attribute_name])) {$Selected_Attributes = explode(",", $_GET['pa_' . $attribute_taxonomy->attribute_name]);}
			else {$Selected_Attributes = array();}
	
			$ReturnString .= "<div class='ewd-uwcf-attribute-filters-wrap ewd-uwcf-style-" . $Display . "' data-attribute_name='pa_" . $attribute_taxonomy->attribute_name . "'>";
	
			if ($Display == "Dropdown") {
				$CheckboxString = "";
				$ReturnString .= "<select class='ewd-uwcf-attribute-dropdown'>";
				$ReturnString .= "<option value='-1'>" . __("All ", 'color-filters') . $attribute_taxonomy->attribute_label . "</option>";
			}
	
			foreach( $get_terms as $term ) {
				if ($Display != "Dropdown") {
					$ReturnString .= "<div class='ewd-uwcf-attribute-item'>";
		
					$ReturnString .= "<input type='checkbox' class='ewd-uwcf-attribute ewd-uwcf-filtering-checkbox " . ($Display == "Checklist" ? 'ewd-uwcf-checklist' : '') . "' value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Attributes) ? 'checked' : '') . " />";
		
					$ReturnString .= "<div class='ewd-uwcf-attribute-link " . (in_array($term->slug, $Selected_Attributes) ? 'ewd-uwcf-selected' : '') . "'>";
					$ReturnString .= "<span class='ewd-uwcf-attribute-name'>" . $term->name . "</span> ";
					$ReturnString .= ($Product_Count == "Yes" ? "(<span class='ewd-uwcf-product-count'>" . $term->count . "</span>)" : "");	
					$ReturnString .= "</div>";
		
					$ReturnString .= "</div>";
				}
				else {
					$CheckboxString .= "<input type='checkbox' class='ewd-uwcf-attribute ewd-uwcf-filtering-checkbox' value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Attributes) ? 'checked' : '') . " />";
					$ReturnString .= "<option value='" . $term->slug . "' " . (in_array($term->slug, $Selected_Attributes) ? 'selected' : '') . ">" . $term->name . ($Product_Count == "Yes" ? " (" . $term->count . ")" : "") . "</option>";
				}
			}
	
			if ($Display == "Dropdown") {
				$ReturnString .= "</select>";
				$ReturnString .= $CheckboxString;
			}
	
			$ReturnString .= "</div>";
	
			if ($Display != "Dropdown") {$ReturnString .= "<div class='ewd-uwcf-attribute-item ewd-uwcf-all-attributes'>" . $Show_All_Attributes_Label . " " . $attribute_taxonomy->attribute_label . "</div>";}
		}
	}


	$ReturnString .= "</form>";
	$ReturnString .= "</div>";

	return $ReturnString;
}


add_filter( 'woocommerce_attribute_label', 'ewd_uwcf_custom_attribute_label', 10, 3 );
function ewd_uwcf_custom_attribute_label( $label, $name, $product ) {
	$Product_Page_Colors_Label = get_option("EWD_UWCF_Product_Page_Colors_Label");
	if($Product_Page_Colors_Label == ''){ $Product_Page_Colors_Label = __('Colors', 'color-filters'); }
	$Product_Page_Sizes_Label = get_option("EWD_UWCF_Product_Page_Sizes_Label");
	if($Product_Page_Sizes_Label == ''){ $Product_Page_Sizes_Label = __('Sizes', 'color-filters'); }
	if($label == 'UWCF Colors'){
		$label = $Product_Page_Colors_Label;
	}
	if($label == 'UWCF Sizes'){
		$label = $Product_Page_Sizes_Label;
	}
	return $label;
}

