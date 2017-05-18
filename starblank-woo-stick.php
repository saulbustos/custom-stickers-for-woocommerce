<?php
/**
 * @package Starblank_Woo_Stick
 * @version 1.0
 */
/*
Plugin Name: Custom Product Stickers for Woocommerce
Plugin URI: https://starblank.com
Description: This plugin easily adds stickers to products
Author: starblank.com
Version: 1.0
Author URI: https://starblank.com
Text Domain: stick-woo-star
*/


//Stuff
defined( 'ABSPATH' ) or die( 'aborting!' );
require "include/stick-admin.php";
$dir = plugin_dir_path( __FILE__ );


//Hooks
add_action( 'woocommerce_before_shop_loop_item_title', 'lanza_botones', 10 );
add_action( 'woocommerce_before_single_product_summary', 'lanza_botones_single', 30 );



//for productloop
function lanza_botones(){
	lanza(true);	
}

//sor single product page
function lanza_botones_single(){
	lanza(false);
}



//Select what type of ribbons we will get
function lanza($type){
	global $product;
	
	//For new products
	if (get_option('stick-woo-activo-nuevo') && is_new($product)) incluir('nuevo',$type);
	
	//for products in offer
        if (get_option('stick-woo-activo-oferta') && $product->is_on_sale()) {
		add_filter('woocommerce_sale_flash', 'remove_sale_logo');	
		incluir('oferta',$type); 
	}
        
	//for products out of stock
        if (get_option('stick-woo-activo-agotado') && !$product->is_in_stock()) incluir('agotado',$type);
}

//remove default sale logo
function remove_sale_logo(){
	return false;
}


//Create ribbons
function incluir($option,$single){

        global $product;
		
		//Get options from admin page
                $img=wp_get_attachment_image_src(get_option('image_attachment_id-'.$option));
                $img=$img[0];
                $width=get_option('stick-woo-width-'.$option);
                $height=get_option('stick-woo-height-'.$option);
                $transdiv=get_option('stick-woo-transdiv-'.$option);
                $transimg=get_option('stick-woo-transimg-'.$option);
                $back_color=get_option('stick-woo-back-'.$option);
                $centrado=get_option('stick-woo-center-'.$option);
                if ($centrado) {
			$estilo_centrado="position: absolute; margin: auto!important; top: 0; left: 0; right: 0; bottom: 0;";
		}
		
		//this converts HEX to RGB
                list($r, $g, $b) = sscanf($back_color, "#%02x%02x%02x");
                if (get_option('stick-woo-ribbon-'.$option)) {
                                echo get_ribbon($option);
                        }
                if (get_option('stick-woo-imagen-activo-'.$option)){
			//Different code for product loop or single product view
			if ($single){
				echo "<style type='text/css'>
					.stick-woo-image {
					".$estilo_centrado."
					opacity:".$transimg."; 
					width:".$width."; 
					height:".$height.";
							
					}
					
					.stick-woo-image-div {
                                                z-index:1000;
                                                position:absolute;
                                                background-color:rgba($r , $g , $b , $transdiv);
                                                width:33%;
                                                height:33%;
					}

					@media (min-width: 769px) {
						.stick-woo-image-div {
						".($centrado ? 'top:0; left:8%;' : '')."
						}
					}

					@media (max-width: 479px){
	                                        .stick-woo-image-div {
        	                                ".($centrado ? 'top:-7%; left:35%;' : '')."
						}
					}

					@media (min-width: 480px) and (max-width: 768px) {
                                                .stick-woo-image-div {
                                                ".($centrado ? 'top:0; left:35%;' : '')."
						}
					}



				      </style>";
					
                                echo "<div class='stick-woo-image-div'>";
                                echo "<img class='stick-woo-image' src='".$img."' />";
				echo "</div>";
			}else{
				//product loop view
                                echo "<div style='z-index:1000; position:absolute; background-color:rgba($r , $g , $b , $transdiv); width:100%; height:100%;'>
                                <img style='".$estilo_centrado." opacity:".$transimg."; width:".$width."px; height:".$height."px;' src='".$img."' />";
                                echo "</div>";
			}
             }

}



//This function determines if product is new or not
//based on days specified in admin options page
function is_new($product){
	$fecha=get_option('stick-woo-nuevo-dias');
	if (!$fecha) $fecha='10';
	$fecha_producto=$product->get_post_data();
	$fecha_producto=strtotime($fecha_producto->post_date);
	$fecha_hoy=strtotime(date("Y-m-d H:i:s"));
	$dif=floor(($fecha_hoy-$fecha_producto)/3600/24);
	if ($dif<$fecha) return true; else return false;
}




//this function return the ribbons CSS based on admin options
function get_ribbon($option){
$align=get_option('stick-woo-ribbon-align-'.$option);
$color1=get_option('stick-woo-ribbon-color1-'.$option);
$color2=get_option('stick-woo-ribbon-color2-'.$option);
$text=get_option('stick-woo-ribbon-text-'.$option);
$css_ribbon=get_option('stick-woo-ribbon-css-ribbon-'.$option);
$css_ribbon_span=get_option('stick-woo-ribbon-css-ribbon-span-'.$option);
$css_ribbon_span_before=get_option('stick-woo-ribbon-css-ribbon-span-before-'.$option);
$css_ribbon_span_after=get_option('stick-woo-ribbon-css-ribbon-span-after-'.$option);

$html="<div class='box-".$option."'>
   <div class='ribbon-".$option."'><span>".$text."</span></div>
</div>";
$css="<style type='text/css'> .box {
  width: 200px; height: 300px;
  position: absolute;
  z-index:1000;
}
.ribbon-".$option." {
  position: absolute;
  ".($align=='izquierda' ? 'left: -5px; top: -5px;':'right: -5px; top: -5px;')."
  z-index: 5;
  overflow: hidden;
  width: 75px; height: 75px;
  text-align: right;
".$css_ribbon."
}
.ribbon-".$option." span {
  font-size: 10px;
  font-weight: bold;
  color: ".$color1.";
  /*text-transform: uppercase;*/
  text-align: center;
  line-height: 20px;
  transform: rotate(".($align=='izquierda' ? '-' : '')."45deg);
  -webkit-transform: rotate(".($align=='izquierda' ? '-' : '')."45deg);
  width: 100px;
  display: block;
  background: #79A70A;
  background: linear-gradient($color2 0%, $color2 100%);
  box-shadow: 0 3px 10px -5px rgba(0, 0, 0, 1);
  position: absolute;
  top: 19px; ".($align=='izquierda' ? 'left: -' : 'right: -')."21px;
".$css_ribbon_span."
}
.ribbon-".$option." span::before {
  content: '';
  position: absolute; left: 0px; top: 100%;
  z-index: -1;
  border-left: 3px solid $color2;
  border-right: 3px solid transparent;
  border-bottom: 3px solid transparent;
  border-top: 3px solid $color2;
".$css_ribbon_span_before."
}
.ribbon-".$option." span::after {
  content: '';
  position: absolute; right: 0px; top: 100%;
  z-index: -1;
  border-left: 3px solid transparent;
  border-right: 3px solid $color2;
  border-bottom: 3px solid transparent;
  border-top: 3px solid $color2;
".$css_ribbon_span_after."
}
".($align=='derecha' ? '@media (min-width: 768px) {.ribbon-nuevo {right:52%;}}' : '')."
</style>";

return $css.$html;
}
