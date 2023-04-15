<?php 


// $tag = 'woocommerce_single_product_summary';
// $function_to_remove = 'woocommerce_template_single_title';
// $priority = 5;
// remove_action( $tag, $function_to_remove, $priority );  //--> elimina el titulo en la pagina de productos

remove_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 10);

add_action('woocommerce_single_product_summary', 'woocommerce_template_single_price', 1);

//Productos por pagina
add_filter('loop_shop_per_page', 'productos_por_pagina', 20);
function productos_por_pagina($columnas) {
    $columnas = 30;
    return $columnas;
}

//Columnas por pagina
add_filter('loop_shop_columns', 'wendyspa_columnas', 20);
function wendyspa_columnas($columnas){
    return 4;
}

// Modifica los creditos del footer
function wendyspa_creditos(){
    remove_action('storefront_footer', 'storefront_credit', 20);
    add_action('storefront_after_footer', 'wendyspa_nuevo_footer', 20);
}
add_action('init', 'wendyspa_creditos');

function wendyspa_nuevo_footer(){
    echo "<div class='reservados'>";
    echo 'Derechos Reservados &copy; ' . get_bloginfo('name') . " " . get_the_date('Y');
    echo "</div>";
}

// Agrega imagen al Home

function wendyspa_descuento() {
    $imagen = '<div class="destacada">';
    $imagen .= '<img src="' . get_stylesheet_directory_uri() . '/img/cupon.jpg">';
    $imagen .= '</div>';
    echo $imagen;
}

add_action('homepage', 'wendyspa_descuento', 5);

// Mostrar 4 categorias en el Hompage
function wendyspa_categorias($args) {
    $args['limit'] = 4;
    $args['columns'] = 4;
    return $args;
}
add_filter('storefront_product_categories_args', 'wendyspa_categorias', 100);

// Cambiar texto a filtro

add_filter('woocommerce_catalog_orderby', 'wendyspa_cambiar_sort', 40);
function wendyspa_cambiar_sort($filtro) {
    $filtro['date'] = __('Nuevos productos primero', 'woocommerce');
    return $filtro;
}

// Remover Tabs
// add_filter('woocommerce_product_tabs', 'wendyspa_remover_tabs', 11, 1);

// function wendyspa_remover_tabs($tabs){
//     unset($tabs['description']);
//     return $tabs;
// }

// Mostrar descuento en cantidad

// add_filter('woocommerce_get_price_html', 'wendyspa_cantidad_ahorrada', 10, 2);

// function wendyspa_cantidad_ahorrada($precio, $producto) {
//     if($producto->sale_price){
//         $ahorro = wc_price($producto->regular_price - $producto->sale_price);
//         return $precio . sprintf( __('<span class="ahorro"> Ahorro %s </span>', 'woocommerce'), $ahorro);
//     }
//     return $precio;
// }


// Mostrar descuento en cantidad en porcentaje
add_filter('woocommerce_get_price_html', 'wendyspa_cantidad_ahorrada_porcentaje', 10, 2);

function wendyspa_cantidad_ahorrada_porcentaje($precio, $producto) {
    if($producto->sale_price){
        $porcentaje = round((($producto->regular_price - $producto->sale_price) / $producto->regular_price) * 100);
        return $precio . sprintf( __('<span class="ahorro"> Ahorro %s &#37;</span>', 'woocommerce'), $porcentaje);
    }
    return $precio;
}

// Cambiar Tab description por el titulo del producto

add_filter('woocommerce_product_tabs', 'wendyspa_titulo_tab_description', 10, 1);

function wendyspa_titulo_tab_description($tabs) {
    global $post;
    if(isset($tabs['description']['title'])) {
        $tabs['description']['title'] = $post->post_title;
    }
    return $tabs;
}
add_filter('woocommerce_product_description_heading', 'wendyspa_titulo_contenido_tab', 10, 1);

function wendyspa_titulo_contenido_tab($titulo) {
    global $post;
    return $post->post_title;
}

// Imprimir  Subtitulo con Advance Custom Fields

add_action('woocommerce_single_product_summary', 'wendyspa_imprimir_subtitulo', 6);
function wendyspa_imprimir_subtitulo(){
    global $post;
    echo "<p class='subtitulo'>" . get_field('Subtitulo', $post->ID) . "</p>";
}


// Agregado TAB para video d=con ACF
add_filter('woocommerce_product_tabs', 'wendyspa_agregar_tab_video', 11, 1);
function wendyspa_agregar_tab_video($tabs) {
    $tabs['video'] = array(
        'title' => 'Video',
        'priority' => 15,
        'callback' => 'video_en_producto'
    );
    return $tabs;
}
function video_en_producto() {
    global $post;
    $video = get_field('video', $post->ID);
    if($video) {
        echo "<video controls autoplay loop>";
        echo "<source src='" . $video . "'>";
        echo "</video>";
    }else{
        echo "No hay disponible";
    }
};
    

// Boton para vaciar el carrito
add_action('woocommerce_cart_actions', 'wendyspa_limpiar_carrito');
function wendyspa_limpiar_carrito() {
    echo '<a class="button" href="?vaciar-carrito=true">' . __('Vaciar Carrito', 'woocommerce') . '</a>';
};

// Vaciar el carrito
add_action('init', 'wendyspa_vaciar_carrito');

function wendyspa_vaciar_carrito() {
    if(isset($_GET['vaciar-carrito'])) {
        global $woocommerce;
        $woocommerce->cart->empty_cart();
    }
};

// Imprimir banner realizado en ACF  en la agina de carrito

add_action('woocommerce_check_cart_items', 'wendyspa_imprimir_banner_carrito', 10);
function wendyspa_imprimir_banner_carrito() {
    global $post;
    $imagen = get_field('Imagen', $post->ID);
    if($imagen) {
        echo "<div class='cupon-carrito'";
        echo "<img src='" . $imagen . "'>";
        echo "</div>";
    }
};

// Eliminar un campo del chekout

add_filter('woocommerce_checkout_fields', 'wendyspa_remover_telefono_checkout', 20, 1);

function wendyspa_remover_telefono_checkout($campos) {
    unset($campos['billing']['billing_phone']);
    return $campos;
};

// Agreagar campos en el ChekOut

add_filter('woocommerce_checkout_fields', 'wendyspa_rfc', 40);

function wendyspa_rfc($campos) {
    $campos['billing']['factura'] = array(
        'css' => array('form-row-wide'),
        'label' => 'Requiere factura?',
        'type' => 'checkbox',
        'id' => 'factura'
    );

    $campos['billing']['rfc'] = array(
        'css' => array('form-row-wide'),
        'label' => 'RFC'
    );

$campos['order']['escuchaste_nosotros'] = array(
    'type' => 'select',
    'css' => array('form-row-wide'),
    'label' => 'Como te enteraste de nosotros?',
    'options' => array(
        'default' => 'Seleccione...',
        'tv' =>'TV',
        'radio' => 'Radio',
        'periodico' => 'Periodico',
        'internet' => 'Internet',
        'facebook' => 'Facebook'
    )
    );
    return $campos;
}

/** Ocultar / Mostrar RFC en el checkout */
function wendyspa_mostrar_RFC() {
    if( is_checkout() ) { ?> 
    <script>
        $(document).ready(function() {
            $('input[type="checkbox"]#factura').on('change', function() {
                $('#rfc_field').slideToggle();
            });
        })
        </script>
   <?php }

}

add_action('wp_footer', 'wendyspa_mostrar_RFC');

//Insertar campos personalizados en checkout
add_action('woocommerce_checkout_update_order_meta', 'wendyspa_insertar_campos_personalizados');

function wendyspa_insertar_campos_personalizados($orden_id){
    if(! empty($_POST['rfc'])){
        update_post_meta( $orden_id, 'RFC', sanitize_text_field($_POST['rfc']));
    }
    if(! empty($_POST['factura'])){
        update_post_meta( $orden_id, 'factura', sanitize_text_field($_POST['factura']));
    }
    if(! empty($_POST['escuchaste_nosotros'])){
        update_post_meta( $orden_id, 'escuchaste_nosotros', sanitize_text_field($_POST['escuchaste_nosotros']));
    }
}

/**Agregar columnas personalizadas a la orden */
add_filter('manage_edit-shop_order_columns', 'wendyspa_columnas_ordenes');
function wendyspa_columnas_ordenes($columnas) {
    $columnas['factura'] = __('Factura', 'woocommerce');
    $columnas['rfc'] = __('Rfc', 'woocommerce');
    $columnas['escuchaste_nosotros'] = __('Escuchaste de nosotros', 'woocommerce');
    return $columnas;
}

/**Mostrar contenido dentro de las columnas */
// add_action('manage_shop_order_posts_custom_column', 'wendyspa_columnas_informacion', 2);
// function wendyspa_columnas_informacion( $columnas ) {
//     global $post, $woocommerce, $order;

//     //obtiene los valores de la orden (se pasa el ID de la orden)
//     if( empty($order) || $order->id != $post->ID){
//         $order = new WC_Order($post->ID);
// }
    

//     if($columnas === 'factura') {
//         $factura = get_post_meta($post->ID, 'factura', true);
//         if($factura){
//             echo 'Si';
//         }
//     }
//     if ($columnas === 'rfc'){
//         echo get_post_meta($post->ID, 'RFC', true);
//     }
//     if ($columnas === 'escuchaste_nosotros'){
//         echo get_post_meta($post->ID, 'escuchaste_nosotros', true);
// }
// }

// Mostrando los campos personalizados en pedidos
add_action('woocommerce_admin_order_data_after_billing_address', 'wendyspa_mostrar_informacion_ordenes');
function wendyspa_mostrar_informacion_ordenes($pedido){
$factura = get_post_meta($pedido->ID, 'factura', true);
if($factura){
    echo '<p><strong>' . __('Factura', 'woocommerce') . ':</strong> Si </p>';
    echo '<p><strong>' . __('RFC', 'woocommerce') . ':</strong>' . get_post_meta($pedido->id, 'RFC', true) . '</p>';
}
echo '<p><strong>' . __('Escuchaste de Nosotros en', 'woocommerce') . ': </strong>' . get_post_meta($pedido->id, 'escuchaste_nosotros', true) . '</p>';
}

// Mostrar imagen cuando no haya imagen destacada

function wendyspa_no_imagen_destacada($imagen_url) {
    $imagen_url = get_stylesheet_directory_uri() . '/img/no-imagen.png';
    return $imagen_url;
}

add_filter('woocommerce_placeholder_img_src', 'wendyspa_no_imagen_destacada');

