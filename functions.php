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
}
    

?> 