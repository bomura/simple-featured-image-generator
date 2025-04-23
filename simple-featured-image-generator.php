<?php
/*
Plugin Name:    Simple Featured Image Generator
Description:    アイキャッチジェネレータです。クラシックエディタの対投稿画面でアイキャッチを作成します。
Version:        0.1
Author:         Nando Koubo
Author URI:     https://blog.donguri3.net
License:        Apache 2.0 License
License URI:    https://www.apache.org/licenses/LICENSE-2.0
Text Domain:    simple-featured-image-generator
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// メタボックスを追加
add_action( 'add_meta_boxes', 'egf_add_meta_box' );
function egf_add_meta_box() {
    add_meta_box(
        'simple_featured_image_generator',
        'アイキャッチジェネレータ',
        'egf_render_meta_box',
        'post',
        'side',
        'default'
    );
}

// メタボックスの中身
function egf_render_meta_box( $post ) {
    $image_id  = get_post_thumbnail_id( $post->ID );
    $image_url = $image_id ? wp_get_attachment_image_src( $image_id, 'full' )[0] : '';
    ?>
    <canvas id="egf_generator_canvas" width="1280" height="720" style="width:100%"></canvas>
    <div><input type="color" id="egf_fontcolor" name="egf_fontcolor" value="#000000" /><label for="egf_fontcolor"> Font Color</label></div>
    <div><input type="color" id="egf_bgcolor" name="egf_bgcolor" value="#FFFFFF" /><label for="egf_bgcolor"> BackGround Color</label></div>
    <div><input type="color" id="egf_framecolor" name="egf_framecolor" value="#a2d7dd" /><label for="egf_framecolor"> Frame Color</label></div>
    <button type="button" id="egf_generate" class="button" style="margin-top:8px; width:100%;">Preview</button>
    <button type="button" id="egf_save" class="button button-primary" style="margin-top:4px; width:100%;">Save</button>
    <img id="egf_result_img" src="<?php echo esc_attr( $image_url ); ?>" alt="Generated" style="margin-top:8px; width:100%; display:<?php echo $image_url ? 'block' : 'none'; ?>;" />
    <?php
}

// スクリプト／スタイル読み込み
add_action( 'admin_enqueue_scripts', 'egf_enqueue_assets' );
function egf_enqueue_assets( $hook ) {
    if ( ! in_array( $hook, [ 'post.php', 'post-new.php' ], true ) ) {
        return;
    }
    global $post;
    wp_enqueue_script( 'egf_script', plugins_url( 'simple-featured-image-generator.js', __FILE__ ), [ 'jquery' ], '0.1', true );
    wp_enqueue_style( 'egf_style', plugins_url( 'simple-featured-image-generator.css', __FILE__ ) );
    wp_localize_script(
        'egf_script',
        'egfData',
        [
            'ajaxUrl'     => admin_url( 'admin-ajax.php' ),
            'postId'      => $post->ID,
            'initialImage'=> $image_url,
            'postTitle'   => get_the_title( $post->ID ),      // 投稿タイトル :contentReference[oaicite:0]{index=0}
            'authorAvatar'=> get_avatar_url( $post->post_author, ['size'=>96] ),
            'authorName'  => get_the_author_meta( 'display_name', $post->post_author ),
            'headerImage' => get_header_image(),
            'nonce'       => wp_create_nonce( 'egf_save' ),
        ]
    );
}

// AJAX: 画像保存＆アイキャッチ設定
add_action( 'wp_ajax_egf_save_image', 'egf_save_image' );
function egf_save_image() {
    check_ajax_referer( 'egf_save', 'nonce' );
    $post_id = intval( $_POST['postId'] );
    $data    = $_POST['imageData'] ?? '';

    if ( ! $data || strpos( $data, 'data:image/png;base64,' ) !== 0 ) {
        wp_send_json_error( [ 'message' => 'Invalid image data.' ] );
    }
    $encoded = substr( $data, strpos( $data, ',' ) + 1 );
    $decoded = base64_decode( $encoded );
    if ( ! $decoded ) {
        wp_send_json_error( [ 'message' => 'Decoding failed.' ] );
    }
    $upload = wp_upload_bits( 'egf_' . time() . '.png', null, $decoded );
    if ( $upload['error'] ) {
        wp_send_json_error( [ 'message' => $upload['error'] ] );
    }
    $attachment = [
        'post_mime_type' => 'image/png',
        'post_title'     => sanitize_file_name( basename( $upload['file'] ) ),
        'post_content'   => '',
        'post_status'    => 'inherit',
    ];
    $attach_id = wp_insert_attachment( $attachment, $upload['file'], $post_id );
    require_once ABSPATH . 'wp-admin/includes/image.php';
    $meta = wp_generate_attachment_metadata( $attach_id, $upload['file'] );
    wp_update_attachment_metadata( $attach_id, $meta );
    set_post_thumbnail( $post_id, $attach_id );
    wp_send_json_success( [ 'url' => $upload['url'] ] );
}
