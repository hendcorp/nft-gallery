<?php
/**
* NFT Gallery - shortcodes.php
*
* In this file,
* you will find all functions related to the shortcodes that are available on the plugin.
*
* @author   Hendra Setiawan
* @version  1.0.0
*/

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function nftgallery_function( $atts ){ 
        wp_enqueue_style( 'flexbox' );
        wp_enqueue_style( 'nftgallery' );
        wp_enqueue_script( 'nftgallery' );

        $args = array(
            'headers'     => array(
                'X-API-KEY' => get_option('nftgallery-api'),
            ),
        );

        $type = get_option('nftgallery-type');
        $limit = get_option('nftgallery-limit');
        $id = get_option('nftgallery-id');

        $request = wp_remote_get( 'https://api.opensea.io/api/v1/assets?format=json&limit='.$limit.'&offset=0&order_direction=desc&'.$type.'='.$id,$args );

        ob_start();
        $nfts = '';

        if( is_wp_error( $request ) || wp_remote_retrieve_response_code( $request ) != 200 ) {
            $nfts .= '<pre>No NFTs detected! Please check the wallet address or the collection slug.</pre>';
        } else {
            $body = wp_remote_retrieve_body( $request );

            $data = json_decode( $body );

            if( ! empty( $data ) ) {
                $nfts .= '<div class="row nftgallery">';
                foreach( $data->assets as $asset ) {
                    if($asset->name) { $title = $asset->name; } else { $title = '#'.$asset->token_id; }
                    
                    $nfts .= '<div class="col-xs-6 col-sm-6 col-md-6 col-lg-4 nftgallery-wrapper">';
                        $nfts .= '<div class="nft" data-url="'.$asset->permalink.'">';
                        $nfts .= '<div class="image" style="background-image: url('.$asset->image_preview_url.');"></div>';
                        $nfts .= '<div class="desc">
                                    <div class="collection">'.$asset->collection->name.'</div>
                                    <h2>'.$title.'</h2>
                                  </div>';
                        $nfts .= '</div>';
                    $nfts .= '</div>';
                
                }
                if($type == 'collection'):

                    $nfts .= '<div class="col-xs-12 col-sm-12 col-md-12 col-lg-12 os-button-wrapper"><a href="https://opensea.io/collection/'.$id.'" class="view-opensea" target="_blank">View '.$asset->collection->name.' on OpenSea</a></div>';
                
                endif;
                $nfts .= '</div>';
            }
        }
        
        echo wp_kses_post($nfts);
        return ob_get_clean(); 
}
add_shortcode('nftgallery', 'nftgallery_function');