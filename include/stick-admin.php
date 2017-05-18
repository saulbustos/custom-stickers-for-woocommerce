<?PHP

//number and order of tabs
$tabOptions=Array('nuevo','oferta','agotado');


//Admin menu hooks
  add_action( 'admin_menu', 'stick_woo_create_menu' );
  add_action( 'admin_init', 'stick_woo_settings' );

//function for creating admin menu
function stick_woo_create_menu() {
	add_menu_page('Product Stickers Options', 'Stickers', 'administrator', __FILE__, 'stick_product_options' , plugins_url('/img/stick-logo.png', __FILE__) );
}


//Register settings that will be used 
function stick_woo_settings() { 
 global $tabOptions;

  register_setting( 'stick-woo-option-group', 'stick-woo-nuevo-dias' );
  register_setting( 'stick-woo-option-group', 'stick-woo-custom-tipo');

 foreach($tabOptions as $option){ 
  register_setting( 'stick-woo-option-group', 'stick-woo-activo-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-width-'.$option );
  //register_setting( 'stick-woo-option-group', 'stick-woo-back-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-height-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-transdiv-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-transimg-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-center-'.$option );
  register_setting( 'stick-woo-option-group', 'image_attachment_id-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-imagen-activo-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-ribbon-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-ribbon-text-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-ribbon-color1-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-ribbon-color2-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-ribbon-align-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-ribbon-css-ribbon-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-ribbon-css-ribbon-span-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-ribbon-css-ribbon-span-before-'.$option );
  register_setting( 'stick-woo-option-group', 'stick-woo-ribbon-css-ribbon-span-after-'.$option );
  //default values
  if (!get_option('stick-woo-width-'.$option)) update_option('stick-woo-width-'.$option,'100px');
  if (!get_option('stick-woo-height-'.$option)) update_option('stick-woo-height-'.$option,'100px');
  //if (!get_option('stick-woo-back-'.$option)) update_option('stick-woo-back-'.$option,'#FF0000');
  if (!get_option('stick-woo-transdiv-'.$option)) update_option('stick-woo-transdiv-'.$option,0);
  if (!get_option('stick-woo-transimg-'.$option)) update_option('stick-woo-transimg-'.$option,1);
  if (!get_option('stick-woo-center-'.$option)) update_option('stick-woo-center-'.$option,true);
  if (!get_option('stick-woo-ribbon-'.$option)) update_option('stick-woo-ribbon-'.$option,true);
  if (!get_option('stick-woo-imagen-activo-'.$option)) update_option('stick-woo-imagen-activo-'.$option,false);
  if (!get_option('stick-woo-ribbon-color1-'.$option)) update_option('stick-woo-ribbon-color1-'.$option,'#000000');
  if (!get_option('stick-woo-ribbon-color2-'.$option)) update_option('stick-woo-ribbon-color2-'.$option,'#FF0000');
  if (!get_option('stick-woo-ribbon-align-'.$option)) update_option('stick-woo-ribbon-align-'.$option,'izquierda');
 }

//Special default options
if (!get_option('stick-woo-ribbon-text-nuevo')) update_option('stick-woo-ribbon-text-nuevo','NEW');
if (!get_option('stick-woo-ribbon-text-oferta')) update_option('stick-woo-ribbon-text-oferta','SALE');
if (!get_option('stick-woo-ribbon-text-agotado')) update_option('stick-woo-ribbon-text-agotado','UNAVAILABLE');
if (!get_option('stick-woo-nuevo-dias')) update_option('stick-woo-nuevo-dias',10);

}


//Select TAB
if (!isset($_GET['tab'])) $tab='nuevo'; else $tab=$_GET['tab'];



//Main function - admin menu
function stick_product_options() {
	global $tab,$tabOptions;
	
	//control access permissions
	if ( !current_user_can( 'manage_options' ) )  {
		wp_die( __( 'You do not have sufficient permissions to access this page.' ) );
	}

	//main form
	echo '<div class="wrap"><form method="post" action="options.php">'; 
	settings_fields( 'stick-woo-option-group' );

	//depuracion?
	depurar(false);

	//get image?
	$img_new=wp_get_attachment_image_src(get_option('image_attachment_id-'.$tab));
	
	wp_enqueue_media();
?>
<h1><?php echo esc_html__( 'STICKERS PARA PRODUCTO ','stick-woo').strtoupper($tab);?></h1>
<h2 class="nav-tab-wrapper">
<?php foreach($tabOptions as $option){
	echo '<a href="?page=starblank-stick-woo%2Finclude%2Fstick-admin.php&tab='.$option.'" class="nav-tab'.($tab==$option ? " nav-tab-active" : "").'">'.esc_html__( 'Sticker producto ','stick-woo-star').$option.'</a>';
}?>
</h2>
<br><input type='checkbox' name='stick-woo-activo-<?php echo $tab;?>' value='true' <?php if (get_option('stick-woo-activo-'.$tab)) echo 'checked';?> /><?php echo esc_html__( 'Activar para producto ','stick-woo-star').$tab;?>
<?php 
$es_nuevo=get_option('stick-woo-nuevo-dias');

//Theres  special options for NEW, CUSTOM
if ($tab=='nuevo') {
	echo "<br><br>".esc_html__( 'Dias para considerar un producto como nuevo:','stick-woo-star')."<input style='width:40px;' type='text' name='stick-woo-nuevo-dias' value='".($es_nuevo=='' ? '10' : $es_nuevo)."'/>";
}
if ($tab=='custom') {
        echo "<br><br>".esc_html__( 'Tipo de producto:','stick-woo-star')."<select name='stick-woo-custom-tipo'>
		<option value='tipo1' ".(get_option('stick-woo-custom-tipo')=='tipo1' ? 'selected="selected"' : '').">tipo1</option>
                <option value='tipo2' ".(get_option('stick-woo-custom-tipo')=='tipo2' ? 'selected="selected"' : '').">tipo2</option></select>";
}

?>
<br><br><h2><?php echo esc_html__( 'RIBBON','stick-woo-star');?></h2>
<hr>
<table class="form-table">
<tr valign="top">
<th scope="row">
<input type='checkbox' name='stick-woo-ribbon-<?php echo $tab;?>' value='true' <?php if (get_option('stick-woo-ribbon-'.$tab)) echo 'checked';?> /><?php echo esc_html__( 'Usar ribbon','stick-woo-star');?></th>
</tr>
<tr valign="top">
<th scope="row"><?php echo esc_html__( 'Texto del ribbon','stick-woo-star');?></th>
<td><input style="width:100px;" type="text" name="stick-woo-ribbon-text-<?php echo $tab;?>" value="<?php echo esc_attr( get_option('stick-woo-ribbon-text-'.$tab) ); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php echo esc_html__( 'Color del texto','stick-woo-star');?></th>
<td><input class="colorpicker" type="text" name="stick-woo-ribbon-color1-<?php echo $tab;?>" value="<?php echo esc_attr( get_option('stick-woo-ribbon-color1-'.$tab) ); ?>" /></td>
</tr>
<tr valign="top">
<th scope="row"><?php echo esc_html__( 'Color fondo','stick-woo-star');?></th>
<td><input class="colorpicker" type="text" name="stick-woo-ribbon-color2-<?php echo $tab;?>" value="<?php echo esc_attr( get_option('stick-woo-ribbon-color2-'.$tab) ); ?>" /></td>
<tr valign="top">
<th scope="row"><?php echo esc_html__( 'Alineacion','stick-woo-star');?></th>
<td><select name="stick-woo-ribbon-align-<?php echo $tab;?>" selected="<?php echo esc_attr( get_option('stick-woo-ribbon-align-'.$tab) ); ?>">
	<option value="izquierda" <?php if (get_option('stick-woo-ribbon-align-'.$tab)=='izquierda') echo 'selected="selected"'; ?>><?php echo esc_html__( 'Izquierda','stick-woo-star');?></option>
        <option value="derecha" <?php if (get_option('stick-woo-ribbon-align-'.$tab)=='derecha') echo 'selected="selected"'; ?>><?php echo esc_html__( 'Derecha','stick-woo-star');?></option>
</select></td>
</tr>	
<tr valign="top">
<th scope="row"><?php echo esc_html__( 'CSS Personalizado - Clase: ','stick-woo-star')."ribbon-".$tab;?></th>
<td><textarea style="width:50%; height:100px;" name="stick-woo-ribbon-css-ribbon-<?php echo $tab;?>"><?php echo esc_attr( get_option('stick-woo-ribbon-css-ribbon-'.$tab) ); ?></textarea></td>
</tr>
<tr valign="top">
<th scope="row"><?php echo esc_html__( 'CSS Personalizado - Clase: ','stick-woo-star')."ribbon-".$tab;?> span</th>
<td><textarea style="width:50%; height:100px;" name="stick-woo-ribbon-css-ribbon-span-<?php echo $tab;?>"><?php echo esc_attr( get_option('stick-woo-ribbon-css-ribbon-span-'.$tab) ); ?></textarea></td>
</tr>
<tr valign="top">
<th scope="row"><?php echo esc_html__( 'CSS Personalizado - Clase: ','stick-woo-star')."ribbon-".$tab;?> span::before</th>
<td><textarea style="width:50%; height:100px;" name="stick-woo-ribbon-css-ribbon-span-before-<?php echo $tab;?>"><?php echo esc_attr( get_option('stick-woo-ribbon-css-ribbon-span-before-'.$tab) ); ?></textarea></td>
</tr>
<tr valign="top">
<th scope="row"><?php echo esc_html__( 'CSS Personalizado - Clase: ','stick-woo-star')."ribbon-".$tab;?> span::after</th>
<td><textarea style="width:50%; height:100px;" name="stick-woo-ribbon-css-ribbon-span-after-<?php echo $tab;?>"><?php echo esc_attr( get_option('stick-woo-ribbon-css-ribbon-span-after-'.$tab) ); ?></textarea></td>
</tr>

</table>
<h2><?php echo esc_html__( 'IMAGEN','stick-woo-star');?></h2>
<hr>
<table class="form-table">
	<tr valign="top">
	<th scope=row"><input type='checkbox' name='stick-woo-imagen-activo-<?php echo $tab;?>' value='true' <?php if (get_option('stick-woo-imagen-activo-'.$tab)) echo 'checked';?> /><?php echo  esc_html__( 'Usar imagen','stick-woo-star');?></th>
	</tr>
        <tr valign="top">
        <th scope="row"><?php echo  esc_html__( 'Ancho (indica px o %)','stick-woo-star');?></th>
        <td><input style="width:60px;" type="text" name="stick-woo-width-<?php echo $tab;?>" value="<?php echo esc_attr( get_option('stick-woo-width-'.$tab) ); ?>" /></td>
        </tr>
        <tr valign="top">
        <th scope="row"><?php echo  esc_html__( 'Alto (indica pc o %)','stick-woo-star');?></th>
        <td><input style="width:60px;" type="text" name="stick-woo-height-<?php echo $tab;?>" value="<?php echo esc_attr( get_option('stick-woo-height-'.$tab) ); ?>" /></td>
        </tr>
        <tr valign="top">
        <!--<th scope="row">Color de fondo</th>
        <td><input class="colorpicker" type="text" name="stick-woo-back-<?php echo $tab;?>" value="<?php echo esc_attr( get_option('stick-woo-back-'.$tab) ); ?>" /></td>
        </tr>-->

   </table>
<div class='image-preview-wrapper'><strong><?php echo  esc_html__( 'Imagen para sticker','stick-woo-star');?></strong><br>
                <img id='image-preview' src='<?php echo $img_new[0];?>' width='100' height='100' style='max-height: 100px; width: 100px;'>
        </div>
        <input id="upload_image_button" type="button" class="button" value="<?php _e( 'Upload image' ); ?>" />
        <input type='hidden' name='image_attachment_id-<?php echo $tab;?>' id='image_attachment_id' value='<?php echo get_option('image_attachment_id-'.$tab)?>'>
	<br><br><input type='checkbox' name='stick-woo-center-<?php echo $tab;?>' value='true' <?php if (get_option('stick-woo-center-'.$tab)) echo 'checked';?> /><?php echo  esc_html__( 'Centrar imagen en producto','stick-woo-star');?> 

<br><br><strong><?php echo  esc_html__( 'Transparencia de la capa','stick-woo-star');?><br><input style="width:40px;" type="text"  name="outrange1" id="outrange1" value="<?php if (get_option('stick-woo-transdiv-'.$tab)) echo get_option('stick-woo-transdiv-'.$tab); else echo "0";?>"/></strong><?php echo  esc_html__( 'Mas transparente','stick-woo-star');?>
<input name="stick-woo-transdiv-<?php echo $tab;?>" id="inrange1" type="range" oninput="outrange1.value = inrange1.value" name="points" step="0.1" id="points" value="<?php echo get_option('stick-woo-transdiv-'.$tab)?>" min="0" max="1"><?php echo  esc_html__( 'Mas opaco','stick-woo-star');?>

<br><br><strong><?php echo  esc_html__( 'Transparencia de la imagen','stick-woo-star');?><br><input style="width:40px;" type="text"  name="outrange2" id="outrange2" value="<?php if (get_option('stick-woo-transimg-'.$tab)) echo get_option('stick-woo-transimg-'.$tab); else echo "0";?>"/></strong><?php echo  esc_html__( 'Mas transparente','stick-woo-star');?>
<input name="stick-woo-transimg-<?php echo $tab;?>" id="inrange2" type="range" oninput="outrange2.value = inrange2.value" name="points" step="0.1" id="points" value="<?php echo get_option('stick-woo-transimg-'.$tab)?>" min="0" max="1"><?php echo  esc_html__( 'Mas opaco','stick-woo-star');?>


<?php
	
	do_settings_sections( 'stick-woo-option-group' );
	submit_button();
	echo '</form>';
	echo '</div>';
}



//Color picker control function
function color_picker_assets($hook_suffix) {
    // $hook_suffix to apply a check for admin page.
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('functions.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}
add_action( 'admin_enqueue_scripts', 'color_picker_assets' );


//Image load control function
add_action( 'admin_footer', 'media_selector_print_scripts' );
function media_selector_print_scripts() {
	global $tab;
	$my_saved_attachment_post_id = get_option( 'image_attachment_id-'.$tab, 0 );
	if (!$my_saved_attachment_post_id) $my_saved_attachment_post_id=0;
	?><script type='text/javascript'>
		jQuery( document ).ready( function( $ ) {
			// Uploading files
			var file_frame;
			var wp_media_post_id = wp.media.model.settings.post.id; // Store the old id
			var set_to_post_id = <?php echo $my_saved_attachment_post_id; ?>; // Set this
			jQuery('#upload_image_button').on('click', function( event ){
				event.preventDefault();
				// If the media frame already exists, reopen it.
				if ( file_frame ) {
					// Set the post ID to what we want
					file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
					// Open frame
					file_frame.open();
					return;
				} else {
					// Set the wp.media post id so the uploader grabs the ID we want when initialised
					wp.media.model.settings.post.id = set_to_post_id;
				}
				// Create the media frame.
				file_frame = wp.media.frames.file_frame = wp.media({
					title: 'Select a image to upload',
					button: {
						text: 'Use this image',
					},
					multiple: false	// Set to true to allow multiple files to be selected
				});
				// When an image is selected, run a callback.
				file_frame.on( 'select', function() {
					// We set multiple to false so only get one image from the uploader
					attachment = file_frame.state().get('selection').first().toJSON();
					// Do something with attachment.id and/or attachment.url here
					$( '#image-preview' ).attr( 'src', attachment.url ).css( 'width', 'auto' );
					$( '#image_attachment_id' ).val( attachment.id );
					// Restore the main post ID
					wp.media.model.settings.post.id = wp_media_post_id;
				});
					// Finally, open the modal
					file_frame.open();
			});
			// Restore the main ID when the add media button is pressed
			jQuery( 'a.add_media' ).on( 'click', function() {
				wp.media.model.settings.post.id = wp_media_post_id;
			});
		});
	</script><?php
}


//DEbug - ignore
function depurar($estado){

	if (!$estado) return;
	echo "DEBUG<br>";
	echo "centrado:".get_option('stick-woo-center')."<br>";
	echo "opcion 1:".get_option('option1')."<br>";
        echo "Trans:".get_option('stick-woo-trans')."<br>";
        echo "Imagen:".var_dump(wp_get_attachment_image_src(get_option('image_attachment_id')))."<br>";

}


