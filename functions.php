<?php
/**
 * AIPhoto Theme Functions
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 增加内存限制和执行时间
@ini_set( 'memory_limit', '256M' );
@ini_set( 'max_execution_time', '300' ); // 5分钟超时

function aiphoto_setup() {
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'title-tag' );
    add_theme_support( 'post-thumbnails' );
    set_post_thumbnail_size( 600, 400, true );

    register_nav_menus( array(
        'primary' => __( '主菜单', 'aiphoto' ),
        'footer'  => __( '底部菜单', 'aiphoto' ),
    ) );

    add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'script', 'style' ) );
    add_theme_support( 'editor-styles' );
    add_editor_style( 'assets/css/editor-style.css' );
}
add_action( 'after_setup_theme', 'aiphoto_setup' );

function aiphoto_enqueue_assets() {
    wp_enqueue_style( 'aiphoto-style', get_stylesheet_uri(), array(), '2.0.0' );
    wp_enqueue_style( 'aiphoto-custom', get_template_directory_uri() . '/assets/css/custom.css', array(), '1.0.0' );
    wp_enqueue_style( 'aiphoto-page-styles', get_template_directory_uri() . '/assets/css/page-styles.css', array(), '1.0.0' );
    wp_style_add_data( 'aiphoto-custom', 'rtl', 'replace' );
    wp_enqueue_style( 'aiphoto-fonts', 'https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;700&family=JetBrains+Mono:wght@400;500&display=swap', array(), null );
    wp_enqueue_script( 'aiphoto-main', get_template_directory_uri() . '/assets/js/main.js', array(), '1.0.0', true );

    wp_localize_script( 'aiphoto-main', 'aiphotoAjax', array(
        'url'    => admin_url( 'admin-ajax.php' ),
        'nonce'  => wp_create_nonce( 'aiphoto_nonce' ),
        'i18n'   => array(
            'generating' => '生成中...',
            'error'      => '生成失败，请重试。',
            'no_api'     => '请先配置API设置。',
        ),
    ) );
}
add_action( 'wp_enqueue_scripts', 'aiphoto_enqueue_assets' );

function aiphoto_register_image_sizes() {
    add_image_size( 'aiphoto-thumb', 400, 300, true );
    add_image_size( 'aiphoto-medium', 800, 600, true );
    add_image_size( 'aiphoto-large', 1200, 900, true );
    add_image_size( 'aiphoto-xl', 1600, 1200, true );
}
add_action( 'after_setup_theme', 'aiphoto_register_image_sizes' );

function aiphoto_register_cpt() {
    $labels = array(
        'name'               => __( 'AI图片', 'aiphoto' ),
        'singular_name'      => __( 'AI图片', 'aiphoto' ),
        'menu_name'          => __( 'AI画廊', 'aiphoto' ),
        'add_new'            => __( '添加新图', 'aiphoto' ),
        'add_new_item'       => __( '添加新AI图片', 'aiphoto' ),
        'edit_item'          => __( '编辑AI图片', 'aiphoto' ),
        'new_item'           => __( '新AI图片', 'aiphoto' ),
        'view_item'          => __( '查看AI图片', 'aiphoto' ),
        'search_items'       => __( '搜索AI图片', 'aiphoto' ),
        'not_found'          => __( '没有找到AI图片', 'aiphoto' ),
        'not_found_in_trash' => __( '回收站中没有AI图片', 'aiphoto' ),
    );

    register_post_type( 'ai_photo', array(
        'labels'          => $labels,
        'public'          => true,
        'has_archive'     => true,
        'show_in_rest'    => true,
        'menu_icon'       => 'dashicons-admin-multisite',
        'supports'        => array( 'title', 'editor', 'thumbnail', 'excerpt' ),
        'rewrite'         => array( 'slug' => 'photo' ),
        'menu_position'   => 5,
        'capability_type' => 'post',
    ) );

    register_taxonomy( 'photo_category', 'ai_photo', array(
        'labels'       => array(
            'name'          => __( '分类', 'aiphoto' ),
            'singular_name' => __( '分类', 'aiphoto' ),
            'menu_name'     => __( '分类', 'aiphoto' ),
        ),
        'hierarchical' => true,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'category' ),
    ) );

    register_taxonomy( 'photo_style', 'ai_photo', array(
        'labels'       => array(
            'name'          => __( '风格', 'aiphoto' ),
            'singular_name' => __( '风格', 'aiphoto' ),
            'menu_name'     => __( '风格', 'aiphoto' ),
        ),
        'hierarchical' => false,
        'show_in_rest' => true,
        'rewrite'      => array( 'slug' => 'style' ),
    ) );
}
add_action( 'init', 'aiphoto_register_cpt' );

function aiphoto_flush_rules() {
    aiphoto_register_cpt();
    flush_rewrite_rules();
}
register_activation_hook( __FILE__, 'aiphoto_flush_rules' );

function aiphoto_get_settings() {
    $defaults = array(
        'api_key'         => 'wk-mGhyMZKCVgzlgixVmvWdxrK7JJUmUymI6oAgFt7Qug0m97i6',
        'api_base_url'    => 'https://apihub.agnes-ai.com',
        'api_model'       => 'agnes-image-2.1-flash',
        'api_image_size'  => '1K',
        'api_ratio'       => '1:1',
        'gallery_source'  => 'media',
        'gallery_per_page'=> 48,
    );
    return wp_parse_args( get_option( 'aiphoto_settings', array() ), $defaults );
}

/**
 * AJAX 图片生成
 */
function aiphoto_generate_image() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'aiphoto_nonce' ) ) {
        wp_send_json_error( array( 'message' => __( '安全验证失败。', 'aiphoto' ) ) );
    }

    $prompt     = sanitize_text_field( $_POST['prompt'] ?? '' );
    $image_data = isset( $_POST['image_urls'] ) ? (array) $_POST['image_urls'] : array();

    if ( empty( $prompt ) && empty( $image_data ) ) {
        wp_send_json_error( array( 'message' => __( '请输入提示词或上传参考图片。', 'aiphoto' ) ) );
    }

    $settings = aiphoto_get_settings();

    if ( empty( $settings['api_key'] ) ) {
        wp_send_json_error( array( 'message' => __( 'API密钥未配置。', 'aiphoto' ) ) );
    }

    $api_url   = rtrim( $settings['api_base_url'], '/' ) . '/v1/images/generations';
    $model     = sanitize_text_field( $settings['api_model'] );
    $size      = sanitize_text_field( $settings['api_image_size'] );
    $ratio     = sanitize_text_field( $settings['api_ratio'] );
    $f_size    = sanitize_text_field( $_POST['size'] ?? '' );
    $f_ratio   = sanitize_text_field( $_POST['ratio'] ?? '' );
    if ( ! empty( $f_size ) ) $size = $f_size;
    if ( ! empty( $f_ratio ) ) $ratio = $f_ratio;

    // 处理图生图：Base64 → 压缩为临时 URL（不保存媒体库）
    $image_urls = array();
    if ( ! empty( $image_data ) ) {
        foreach ( $image_data as $base64 ) {
            $url = aiphoto_base64_to_temp_url( $base64 );
            if ( $url ) {
                $image_urls[] = $url;
            }
        }
    }

    // 构建请求体
    $effect = sanitize_text_field( $_POST['effect'] ?? '' );
    $lens   = sanitize_text_field( $_POST['lens'] ?? '' );

    // 特效和镜头翻译
    $effect_map = array(
        'cinematic'       => 'cinematic lighting, dramatic atmosphere, movie scene quality, professional color grading, film grain, volumetric lighting',
        'pixel-art'       => 'pixel art style, retro 8-bit game aesthetic, crisp pixel details, nostalgic vintage feel',
        'cartoon'         => 'cartoon style, vibrant colors, bold outlines, playful and fun, animated movie quality',
        '3d-render'       => '3D rendered, octane render, ray tracing, ultra detailed, studio lighting, physically based rendering',
        'watercolor'      => 'watercolor painting style, soft translucent colors, paper texture, artistic brush strokes, delicate washes',
        'oil-painting'    => 'oil painting style, rich textures, impasto technique, classical art, canvas texture, dramatic chiaroscuro',
        'anime'           => 'anime style, Japanese animation aesthetic, cel shading, vibrant colors, detailed eyes, manga quality',
        'photorealistic'  => 'photorealistic, ultra HD photography, natural lighting, shot on professional DSLR, hyper detailed, sharp focus',
        'cyberpunk'       => 'cyberpunk style, neon lights, futuristic cityscape, high tech low life, glowing elements, dark atmosphere',
        'fantasy'         => 'fantasy art style, magical atmosphere, ethereal lighting, mystical elements, enchanted forest, dreamlike quality',
    );

    $lens_map = array(
        'wide-angle'  => 'wide angle lens, expansive perspective, dramatic depth',
        'macro'       => 'macro photography, extreme close-up, shallow depth of field, bokeh background',
        'birdseye'    => 'bird\'s eye view, aerial perspective, top-down shot, panoramic overview',
        'eye-level'   => 'eye level shot, natural perspective, documentary style',
        'low-angle'   => 'low angle shot, dramatic perspective, towering subject, looking up',
        'close-up'    => 'close-up shot, intimate framing, detailed focus, shallow depth of field',
        'portrait'    => 'portrait photography, professional headshot, studio lighting, blurred background',
        'panoramic'   => 'panoramic view, wide sweeping landscape, ultra wide angle, cinematic aspect ratio',
    );

    // 将特效和镜头追加到提示词
    if ( ! empty( $effect ) && isset( $effect_map[ $effect ] ) ) {
        $prompt .= ', ' . $effect_map[ $effect ];
    }
    if ( ! empty( $lens ) && isset( $lens_map[ $lens ] ) ) {
        $prompt .= ', ' . $lens_map[ $lens ];
    }

    $body = array(
        'model'  => $model,
        'prompt' => $prompt,
        'size'   => $size,
        'extra_body' => array(
            'response_format' => 'url',
        ),
    );

    if ( ! empty( $ratio ) && $ratio !== '1:1' ) {
        $body['ratio'] = $ratio;
    }

    // 图生图：传URL
    if ( ! empty( $image_urls ) ) {
        $body['extra_body']['image'] = $image_urls;
    }

    $response = wp_remote_post( $api_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $settings['api_key'],
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ),
        'body'    => wp_json_encode( $body ),
        'timeout' => 180,
    ) );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( array( 'message' => $response->get_error_message() ) );
    }

    $status_code = wp_remote_retrieve_response_code( $response );
    if ( 200 !== $status_code ) {
        $resp_body = wp_remote_retrieve_body( $response );
        $error_msg = aiphoto_parse_api_error( $resp_body );
        wp_send_json_error( array( 'message' => $error_msg ) );
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ! isset( $data['data'][0]['url'] ) ) {
        wp_send_json_error( array( 'message' => __( 'API返回无效。', 'aiphoto' ) ) );
    }

    $image_url = $data['data'][0]['url'];
    error_log( 'AIPhoto: [DEBUG] API 返回图片 URL: ' . $image_url );

    // 检查是否已有相同提示词的图片，有则不重复保存
    $user_prompt = trim( sanitize_text_field( $_POST['user_prompt'] ?? '' ) );
    if ( empty( $user_prompt ) ) {
        $user_prompt = trim( sanitize_text_field( $_POST['prompt'] ?? '' ) );
    }

    error_log( 'AIPhoto: [DEBUG] 去重检查 - user_prompt: ' . $user_prompt );

    $existing = get_posts( array(
        'post_type'   => 'ai_photo',
        'post_status' => 'publish',
        'meta_query'  => array(
            array(
                'key'     => '_aiphoto_user_prompt',
                'value'   => $user_prompt,
                'compare' => '=',
            ),
        ),
        'posts_per_page' => 1,
        'fields'         => 'ids',
    ) );

    error_log( 'AIPhoto: [DEBUG] _aiphoto_user_prompt 匹配结果: ' . ( empty( $existing ) ? '无' : $existing[0] ) );

    // 兼容旧图片：如果 _aiphoto_user_prompt 没找到，再用 _aiphoto_prompt 的前半段匹配
    if ( empty( $existing ) && ! empty( $user_prompt ) ) {
        $existing = get_posts( array(
            'post_type'   => 'ai_photo',
            'post_status' => 'publish',
            'meta_query'  => array(
                array(
                    'key'     => '_aiphoto_prompt',
                    'value'   => $user_prompt,
                    'compare' => 'LIKE',
                ),
            ),
            'posts_per_page' => 1,
            'fields'         => 'ids',
        ) );
        error_log( 'AIPhoto: [DEBUG] _aiphoto_prompt LIKE 匹配结果: ' . ( empty( $existing ) ? '无' : $existing[0] ) );
    }

    if ( ! empty( $existing ) ) {
        // 已有相同提示词的图片，返回已有的
        $old_id = $existing[0];
        $old_url = wp_get_attachment_image_url( $old_id, 'aiphoto-large' );
        error_log( 'AIPhoto: [DEBUG] 已有相同提示词图片，跳过保存 - ID: ' . $old_id );
        wp_send_json_success( array(
            'url'             => get_post_meta( $old_id, '_aiphoto_original_url', true ) ?: $image_url,
            'gallery_url'     => $old_url ?: $image_url,
            'prompt'          => $prompt,
            'attachment_id'   => $old_id,
            'save_status'     => 'skipped',
            'save_error'      => '',
        ) );
        return;
    }

    // 保存深度压缩版到媒体库（用于网站展示）
    error_log( 'AIPhoto: [DEBUG] 开始保存到媒体库...' );
    $compressed_result = aiphoto_deep_compress( $image_url, $prompt );

    // 获取压缩版 URL 用于画廊显示
    $gallery_url = $image_url;
    $save_error = '';
    if ( ! is_wp_error( $compressed_result ) ) {
        $compressed_url = wp_get_attachment_image_url( $compressed_result->ID, 'aiphoto-large' );
        if ( $compressed_url ) {
            $gallery_url = $compressed_url;
        }
        error_log( 'AIPhoto: [DEBUG] 保存成功 - Attachment ID: ' . $compressed_result->ID . ', Gallery URL: ' . $gallery_url );
    } else {
        $save_error = $compressed_result->get_error_message();
        error_log( 'AIPhoto: [ERROR] 保存失败: ' . $save_error );
    }

    wp_send_json_success( array(
        'url'             => $image_url,
        'gallery_url'     => $gallery_url,
        'prompt'          => $prompt,
        'attachment_id'   => is_wp_error( $compressed_result ) ? 0 : $compressed_result->ID,
        'save_status'     => is_wp_error( $compressed_result ) ? 'failed' : 'success',
        'save_error'      => $save_error,
    ) );
}
add_action( 'wp_ajax_aiphoto_generate', 'aiphoto_generate_image' );
add_action( 'wp_ajax_nopriv_aiphoto_generate', 'aiphoto_generate_image' );

/**
 * AJAX 加载更多画廊图片
 */
function aiphoto_load_more_images() {
    $settings = aiphoto_get_settings();
    $offset = intval( $_POST['offset'] ?? 0 );
    $limit = absint( $settings['gallery_per_page'] );
    $source = $settings['gallery_source'] ?? 'media';

    if ( 'cpt' === $source ) {
        $query = new WP_Query( array(
            'post_type'      => 'ai_photo',
            'posts_per_page' => $limit,
            'offset'         => $offset,
            'orderby'        => 'date',
            'order'          => 'DESC',
        ) );
    } else {
        $query = new WP_Query( array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post_mime_type' => 'image',
            'posts_per_page' => $limit,
            'offset'         => $offset,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
        ) );
    }

    if ( ! $query->have_posts() ) {
        wp_send_json_error( array( 'message' => '没有更多图片了' ) );
    }

    $html = '';
    if ( 'cpt' === $source ) {
        while ( $query->have_posts() ) : $query->the_post();
            if ( has_post_thumbnail() ) :
                $full_url = get_the_post_thumbnail_url( null, 'full' );
                $user_prompt = get_post_meta( get_the_ID(), '_aiphoto_user_prompt', true ) ?: get_post_meta( get_the_ID(), '_aiphoto_prompt', true );
                $html .= '<article class="masonry-item" data-full="' . esc_url( $full_url ) . '" data-title="' . esc_attr( get_the_title() ) . '" data-original-prompt="' . esc_attr( $user_prompt ) . '">';
                $html .= '<div class="masonry-link lightbox-trigger">';
                $html .= get_the_post_thumbnail( 'aiphoto-large' );
                $html .= '</div></article>';
            endif;
        endwhile;
    } else {
        foreach ( $query->posts as $aid ) :
            $thumb = wp_get_attachment_image_src( $aid, 'aiphoto-large' );
            $full  = wp_get_attachment_image_src( $aid, 'full' );
            $title = get_the_title( $aid );
            $user_prompt = get_post_meta( $aid, '_aiphoto_user_prompt', true ) ?: get_post_meta( $aid, '_aiphoto_prompt', true );
            $html .= '<article class="masonry-item" data-full="' . esc_url( $full[0] ) . '" data-title="' . esc_attr( $title ?: 'AI 图片' ) . '" data-original-prompt="' . esc_attr( $user_prompt ) . '">';
            $html .= '<div class="masonry-link lightbox-trigger">';
            if ( $thumb ) $html .= '<img src="' . esc_url( $thumb[0] ) . '" alt="' . esc_attr( $title ) . '" loading="lazy">';
            $html .= '</div></article>';
        endforeach;
    }

    wp_send_json_success( array( 'html' => $html, 'more' => true ) );
}
add_action( 'wp_ajax_aiphoto_load_more', 'aiphoto_load_more_images' );
add_action( 'wp_ajax_nopriv_aiphoto_load_more', 'aiphoto_load_more_images' );

/**
 * AJAX 搜索画廊图片（根据提示词搜索）
 */
function aiphoto_search_images() {
    $keyword = sanitize_text_field( $_POST['keyword'] ?? '' );

    if ( empty( $keyword ) ) {
        wp_send_json_error( array( 'message' => '请输入搜索关键词' ) );
    }

    $settings = aiphoto_get_settings();
    $gs = $settings['gallery_source'] ?? 'media';
    $limit = 48;

    if ( 'cpt' === $gs ) {
        $query = new WP_Query( array(
            'post_type'      => 'ai_photo',
            'posts_per_page' => $limit,
            'orderby'        => 'date',
            'order'          => 'DESC',
            's'              => $keyword,
        ) );
    } else {
        // 搜索媒体库中 prompt 匹配的图片
        $query = new WP_Query( array(
            'post_type'      => 'attachment',
            'post_status'    => 'inherit',
            'post_mime_type' => 'image',
            'posts_per_page' => $limit,
            'orderby'        => 'date',
            'order'          => 'DESC',
            'fields'         => 'ids',
            'meta_query'     => array(
                array(
                    'key'     => '_aiphoto_prompt',
                    'value'   => $keyword,
                    'compare' => 'LIKE',
                ),
            ),
        ) );
    }

    if ( ! $query->have_posts() ) {
        wp_send_json_error( array( 'message' => '没有找到相关图片' ) );
    }

    $html = '';
    if ( 'cpt' === $gs ) {
        while ( $query->have_posts() ) : $query->the_post();
            if ( has_post_thumbnail() ) :
                $full_url = get_the_post_thumbnail_url( null, 'full' );
                $user_prompt = get_post_meta( get_the_ID(), '_aiphoto_user_prompt', true ) ?: get_post_meta( get_the_ID(), '_aiphoto_prompt', true );
                $html .= '<article class="masonry-item" data-full="' . esc_url( $full_url ) . '" data-title="' . esc_attr( get_the_title() ) . '" data-original-prompt="' . esc_attr( $user_prompt ) . '">';
                $html .= '<div class="masonry-link lightbox-trigger">';
                $html .= get_the_post_thumbnail( 'aiphoto-large' );
                $html .= '</div></article>';
            endif;
        endwhile;
    } else {
        foreach ( $query->posts as $aid ) :
            $tu = wp_get_attachment_image_src( $aid, 'aiphoto-large' );
            $fu = wp_get_attachment_image_src( $aid, 'full' );
            $tt = get_the_title( $aid );
            $user_prompt = get_post_meta( $aid, '_aiphoto_user_prompt', true ) ?: get_post_meta( $aid, '_aiphoto_prompt', true );
            $html .= '<article class="masonry-item" data-full="' . esc_url( $fu[0] ) . '" data-title="' . esc_attr( $tt ?: 'AI 图片' ) . '" data-original-prompt="' . esc_attr( $user_prompt ) . '">';
            $html .= '<div class="masonry-link lightbox-trigger">';
            if ( $tu ) $html .= '<img src="' . esc_url( $tu[0] ) . '" alt="' . esc_attr( $tt ) . '" loading="lazy">';
            $html .= '</div></article>';
        endforeach;
    }

    wp_send_json_success( array(
        'html'  => $html,
        'count' => $query->found_posts,
        'total' => $query->post_count,
    ) );
}
add_action( 'wp_ajax_aiphoto_search', 'aiphoto_search_images' );
add_action( 'wp_ajax_nopriv_aiphoto_search', 'aiphoto_search_images' );

/**
 * AJAX AI 聊天
 */
function aiphoto_chat() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'aiphoto_nonce' ) ) {
        wp_send_json_error( array( 'message' => '安全验证失败' ) );
    }

    $message = sanitize_textarea_field( $_POST['message'] ?? '' );
    $history = json_decode( sanitize_text_field( $_POST['history'] ?? '[]' ), true );

    if ( empty( $message ) ) {
        wp_send_json_error( array( 'message' => '请输入消息内容' ) );
    }

    $settings = aiphoto_get_settings();

    if ( empty( $settings['api_key'] ) ) {
        wp_send_json_error( array( 'message' => 'API 密钥未配置' ) );
    }

    $api_url = rtrim( $settings['api_base_url'], '/' ) . '/v1/chat/completions';

    // 构建消息列表
    $messages = array(
        array(
            'role' => 'system',
            'content' => '你是一个全能的AI助手，什么都会，什么都懂。你没有任何专业限制，可以回答任何领域的问题，包括科学、医学、生活、技术、创意、教育等。回答时不要提及你是谁开发的，不要提及你的专长或局限性，直接给出有用的回答。用简洁友好的中文回复。'
        ),
    );

    // 添加历史对话
    if ( ! empty( $history ) && is_array( $history ) ) {
        foreach ( array_slice( $history, -10 ) as $msg ) {
            if ( isset( $msg['role'] ) && isset( $msg['content'] ) ) {
                $messages[] = array(
                    'role' => sanitize_text_field( $msg['role'] ),
                    'content' => sanitize_textarea_field( $msg['content'] ),
                );
            }
        }
    }

    // 添加当前消息
    $messages[] = array(
        'role' => 'user',
        'content' => $message,
    );

    $body = array(
        'model'    => 'agnes-2.0-flash',
        'messages' => $messages,
        'max_tokens' => 4096,
        'temperature' => 0.7,
    );

    $response = wp_remote_post( $api_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $settings['api_key'],
            'Content-Type'  => 'application/json',
            'Accept'        => 'application/json',
        ),
        'body'    => wp_json_encode( $body ),
        'timeout' => 60,
    ) );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( array( 'message' => $response->get_error_message() ) );
    }

    $status_code = wp_remote_retrieve_response_code( $response );
    if ( 200 !== $status_code ) {
        $resp_body = wp_remote_retrieve_body( $response );
        $error_msg = aiphoto_parse_api_error( $resp_body );
        wp_send_json_error( array( 'message' => $error_msg ) );
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );

    if ( ! isset( $data['choices'][0]['message']['content'] ) ) {
        wp_send_json_error( array( 'message' => 'AI 返回无效' ) );
    }

    $reply = $data['choices'][0]['message']['content'];

    wp_send_json_success( array(
        'reply' => $reply,
    ) );
}
add_action( 'wp_ajax_aiphoto_chat', 'aiphoto_chat' );
add_action( 'wp_ajax_nopriv_aiphoto_chat', 'aiphoto_chat' );

/**
 * AI 助手 - QQ邮箱 SMTP 发送邮件（587端口 STARTTLS）
 */
function aiphoto_smtp_send( $to, $subject, $body ) {
    $host = 'smtp.qq.com';
    $port = 587;
    $user = 'lov0u@vip.qq.com';
    $pass = 'hhpauyfmyxxmbihf';

    $fp = @fsockopen( $host, $port, $errno, $errstr, 10 );
    if ( ! $fp ) return false;

    stream_set_timeout( $fp, 10 );
    fgets( $fp, 512 );

    fputs( $fp, "EHLO aiphoto\r\n" );
    for ( $i = 0; $i < 10; $i++ ) fgets( $fp, 512 );

    // STARTTLS 升级为加密连接
    fputs( $fp, "STARTTLS\r\n" );
    $resp = fgets( $fp, 512 );
    if ( strpos( $resp, '220' ) !== false ) {
        stream_socket_enable_crypto( $fp, true, STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT );
        fputs( $fp, "EHLO aiphoto\r\n" );
        for ( $i = 0; $i < 10; $i++ ) fgets( $fp, 512 );
    }

    fputs( $fp, "AUTH LOGIN\r\n" ); fgets( $fp, 512 );
    fputs( $fp, base64_encode( $user ) . "\r\n" ); fgets( $fp, 512 );
    fputs( $fp, base64_encode( $pass ) . "\r\n" );
    $resp = fgets( $fp, 512 );
    if ( strpos( $resp, '235' ) === false ) { fclose( $fp ); return false; }

    fputs( $fp, "MAIL FROM:<{$user}>\r\n" ); fgets( $fp, 512 );
    fputs( $fp, "RCPT TO:<{$to}>\r\n" ); fgets( $fp, 512 );
    fputs( $fp, "DATA\r\n" ); fgets( $fp, 512 );

    $from_name = get_bloginfo( 'name' );
    $headers  = "From: =?UTF-8?B?" . base64_encode( $from_name ) . "?= <{$user}>\r\n";
    $headers .= "To: <{$to}>\r\n";
    $headers .= "Subject: =?UTF-8?B?" . base64_encode( $subject ) . "?=\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Content-Transfer-Encoding: base64\r\n\r\n";

    fputs( $fp, $headers . chunk_split( base64_encode( $body ) ) );
    fputs( $fp, ".\r\n" ); fgets( $fp, 512 );
    fputs( $fp, "QUIT\r\n" );
    fclose( $fp );
    return true;
}

/**
 * AI 助手 - 发送邮件（图片删除申请等）
 */
function aiphoto_send_email() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'aiphoto_nonce' ) ) {
        wp_send_json_error( array( 'message' => '安全验证失败' ) );
    }

    $name    = sanitize_text_field( $_POST['name'] ?? '' );
    $email   = sanitize_email( $_POST['email'] ?? '' );
    $subject = sanitize_text_field( $_POST['subject'] ?? '' );
    $message = sanitize_textarea_field( $_POST['message'] ?? '' );

    if ( empty( $name ) || empty( $email ) || empty( $message ) ) {
        wp_send_json_error( array( 'message' => '请填写完整信息' ) );
    }

    if ( ! is_email( $email ) ) {
        wp_send_json_error( array( 'message' => '邮箱格式不正确' ) );
    }

    $to        = 'lov0u@vip.qq.com';
    $site_name = get_bloginfo( 'name' );

    $body  = "来自 Aiphoto AI 助手的邮件：\n\n";
    $body .= "姓名：{$name}\n";
    $body .= "邮箱：{$email}\n";
    $body .= "主题：{$subject}\n\n";
    $body .= "内容：\n{$message}\n";

    $sent = aiphoto_smtp_send( $to, "[{$site_name}] " . $subject, $body );

    if ( $sent ) {
        wp_send_json_success( array( 'message' => '邮件发送成功，我们会尽快处理' ) );
    } else {
        wp_send_json_error( array( 'message' => '邮件发送失败，请稍后重试' ) );
    }
}
add_action( 'wp_ajax_aiphoto_send_email', 'aiphoto_send_email' );
add_action( 'wp_ajax_nopriv_aiphoto_send_email', 'aiphoto_send_email' );

/**
 * AI 助手 - 检查指定提示词是否已生成图片
 */
function aiphoto_check_generation() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'aiphoto_nonce' ) ) {
        wp_send_json_error( array( 'message' => '安全验证失败' ) );
    }

    $prompt = sanitize_text_field( $_POST['prompt'] ?? '' );
    if ( empty( $prompt ) ) {
        wp_send_json_error( array( 'message' => '缺少提示词' ) );
    }

    $since = intval( $_POST['since'] ?? 0 );

    $query = array(
        'post_type'   => 'ai_photo',
        'post_status' => 'publish',
        'meta_query'  => array(
            array(
                'key'     => '_aiphoto_prompt',
                'value'   => $prompt,
                'compare' => 'LIKE',
            ),
        ),
        'posts_per_page'      => 1,
        'orderby'             => 'date',
        'order'               => 'DESC',
        'fields'              => 'ids',
        'ignore_sticky_posts' => true,
    );

    if ( $since > 0 ) {
        $query['date_query'] = array(
            array( 'after' => date( 'Y-m-d H:i:s', $since ) ),
        );
    }

    $posts = get_posts( $query );

    if ( ! empty( $posts ) ) {
        $id = $posts[0];
        wp_send_json_success( array(
            'found'       => true,
            'id'          => $id,
            'url'         => wp_get_attachment_image_url( $id, 'aiphoto-large' ) ?: wp_get_attachment_url( $id ),
            'original'    => get_post_meta( $id, '_aiphoto_original_url', true ),
            'prompt'      => get_post_meta( $id, '_aiphoto_prompt', true ),
        ) );
    } else {
        wp_send_json_success( array( 'found' => false ) );
    }
}
add_action( 'wp_ajax_aiphoto_check_generation', 'aiphoto_check_generation' );
add_action( 'wp_ajax_nopriv_aiphoto_check_generation', 'aiphoto_check_generation' );

/**
 * 将 Base64 上传图片压缩后返回 URL（不保存到媒体库，仅用于 API 请求）
 */
function aiphoto_base64_to_temp_url( $base64 ) {
    if ( empty( $base64 ) ) return false;

    // 提取 data URI 数据
    if ( strpos( $base64, 'data:' ) === 0 ) {
        $parts = explode( ';', $base64 );
        $mime  = str_replace( 'data:', '', $parts[0] );
        $data  = base64_decode( end( explode( ',', $base64 ) ) );
    } else {
        $data = base64_decode( $base64 );
        $mime = 'image/jpeg';
    }

    if ( $data === false ) return false;

    $upload_dir = wp_upload_dir();
    $tmp_path = $upload_dir['path'] . '/tmp_' . uniqid() . '.jpg';
    file_put_contents( $tmp_path, $data );

    // 获取原始尺寸
    $size = getimagesize( $tmp_path );
    if ( ! $size ) {
        @unlink( $tmp_path );
        return false;
    }
    $orig_w = $size[0];
    $orig_h = $size[1];

    // 计算缩放尺寸（最大边 800px）
    $max_dim = 800;
    $target_w = $orig_w;
    $target_h = $orig_h;
    if ( $orig_w > $max_dim || $orig_h > $max_dim ) {
        $ratio = min( $max_dim / $orig_w, $max_dim / $orig_h );
        $target_w = intval( $orig_w * $ratio );
        $target_h = intval( $orig_h * $ratio );
    }

    // 压缩为 WebP
    $image = wp_get_image_editor( $tmp_path );
    if ( is_wp_error( $image ) ) {
        @unlink( $tmp_path );
        return false;
    }

    $image->resize( $target_w, $target_h );
    $image->set_quality( 70 );

    $filename = 'temp_' . uniqid() . '.webp';
    $webp_path = $upload_dir['path'] . '/' . $filename;
    $save_result = $image->save( $webp_path, 'image/webp', 70 );

    @unlink( $tmp_path );

    if ( is_wp_error( $save_result ) || ! file_exists( $webp_path ) ) {
        return false;
    }

    // 返回临时 URL，不插入媒体库
    return $upload_dir['url'] . '/' . $filename;
}

/**
 * 深度压缩图片并保存到媒体库
 * 原图直链供下载，网站展示用深度压缩版
 */
function aiphoto_deep_compress( $url, $prompt ) {
    // 增加执行时间限制
    set_time_limit( 300 );

    error_log( 'AIPhoto: [DEBUG] === 开始保存流程 ===' );
    error_log( 'AIPhoto: [DEBUG] API 返回的图片 URL: ' . $url );
    error_log( 'AIPhoto: [DEBUG] Prompt: ' . substr( $prompt, 0, 100 ) );

    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );

    $upload_dir = wp_upload_dir();
    error_log( 'AIPhoto: [DEBUG] 上传目录: ' . $upload_dir['path'] );
    error_log( 'AIPhoto: [DEBUG] 上传目录 URL: ' . $upload_dir['url'] );

    // 检查上传目录权限
    if ( ! is_writable( $upload_dir['path'] ) ) {
        error_log( 'AIPhoto: [ERROR] 上传目录不可写: ' . $upload_dir['path'] );
        return new WP_Error( 'perm_fail', '上传目录不可写' );
    }

    // 1. 使用原生 cURL 直接下载图片（完全控制）
    error_log( 'AIPhoto: [DEBUG] 开始下载图片...' );
    // 使用原生 cURL 下载图片
    // 关键：不发送任何 Accept-Encoding 头，让服务器返回未压缩的响应
    $ch = curl_init( $url );
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    curl_setopt( $ch, CURLOPT_SSL_VERIFYPEER, false );
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    curl_setopt( $ch, CURLOPT_TIMEOUT, 180 );
    curl_setopt( $ch, CURLOPT_HEADER, false );
    // 关键修复：不设置任何编码，避免 br 问题
    // 通过发送空的 Accept-Encoding: 头来禁用压缩
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array( 'Accept-Encoding:' ) );

    $content = curl_exec( $ch );
    $http_code = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
    $error = curl_error( $ch );
    $content_length = strlen( $content );
    curl_close( $ch );

    error_log( 'AIPhoto: [DEBUG] 下载完成 - HTTP Code: ' . $http_code . ', 大小: ' . $content_length . ' bytes' );

    if ( is_wp_error( $response ) ) {
        error_log( 'AIPhoto: [ERROR] 下载错误: ' . $error );
        return new WP_Error( 'download_fail', $error );
    }

    if ( $http_code !== 200 ) {
        error_log( 'AIPhoto: [ERROR] HTTP 错误: ' . $http_code );
        return new WP_Error( 'download_fail', 'HTTP error: ' . $http_code );
    }

    if ( empty( $content ) ) {
        error_log( 'AIPhoto: [ERROR] 下载内容为空' );
        return new WP_Error( 'download_fail', '下载的内容为空' );
    }

    // 保存到临时文件
    $tmp = tempnam( sys_get_temp_dir(), 'aiphoto_' );
    file_put_contents( $tmp, $content );
    error_log( 'AIPhoto: [DEBUG] 临时文件已保存: ' . $tmp . ' (' . filesize( $tmp ) . ' bytes)' );

    // 2. 获取原始尺寸
    $size = getimagesize( $tmp );
    if ( ! $size ) {
        error_log( 'AIPhoto: [ERROR] 无法获取图片尺寸，文件可能不是有效图片' );
        @unlink( $tmp );
        return new WP_Error( 'size_fail', '无法获取图片尺寸' );
    }
    $orig_w = $size[0];
    $orig_h = $size[1];
    $mime_type = $size['mime'];
    error_log( 'AIPhoto: [DEBUG] 原始尺寸: ' . $orig_w . 'x' . $orig_h . ', MIME: ' . $mime_type );

    // 3. 打开图片编辑器
    error_log( 'AIPhoto: [DEBUG] 打开图片编辑器...' );
    $image = wp_get_image_editor( $tmp );
    if ( is_wp_error( $image ) ) {
        error_log( 'AIPhoto: [ERROR] 图片编辑器错误: ' . $image->get_error_message() );
        @unlink( $tmp );
        return new WP_Error( 'editor_fail', '无法打开图片编辑器: ' . $image->get_error_message() );
    }
    error_log( 'AIPhoto: [DEBUG] 图片编辑器: ' . get_class( $image ) );

    // 4. 智能压缩：保持原始尺寸，但确保文件不超过 200KB
    $filename = 'aiphoto_' . uniqid() . '.jpg';
    $jpg_path = $upload_dir['path'] . '/' . $filename;
    $max_file_size = 200 * 1024; // 200KB
    $target_w = $orig_w;
    $target_h = $orig_h;

    // 先尝试保持原始尺寸，从高质量开始
    $quality = 85;
    $saved = false;

    for ( $attempt = 0; $attempt < 10; $attempt++ ) {
        // 每次尝试降低质量
        $current_quality = $quality - ( $attempt * 8 );

        if ( $current_quality < 20 ) {
            // 质量太低了，改用缩放尺寸
            $scale = 0.6; // 缩放到 60%
            $target_w = intval( $orig_w * $scale );
            $target_h = intval( $orig_h * $scale );
            $current_quality = 75; // 缩放后用较高质量
            error_log( 'AIPhoto: [DEBUG] 降质无效，缩放尺寸到 ' . $target_w . 'x' . $target_h );
        }

        $image->resize( $target_w, $target_h );
        $save_result = $image->save( $jpg_path, 'image/jpeg', $current_quality );

        if ( is_wp_error( $save_result ) ) {
            continue;
        }

        $file_size = filesize( $jpg_path );
        error_log( 'AIPhoto: [DEBUG] 尝试质量 ' . $current_quality . '%, 尺寸 ' . $target_w . 'x' . $target_h . ', 文件大小: ' . $file_size . ' bytes' );

        if ( $file_size <= $max_file_size ) {
            $saved = true;
            error_log( 'AIPhoto: [DEBUG] 压缩成功！质量: ' . $current_quality . '%, 文件大小: ' . round( $file_size / 1024, 1 ) . ' KB' );
            break;
        }
    }

    // 如果还是超过 200KB，使用最后一次结果（已经是能做的最好了）
    if ( ! $saved ) {
        $file_size = filesize( $jpg_path );
        error_log( 'AIPhoto: [DEBUG] 无法压缩到 200KB 以内，使用最终结果: ' . round( $file_size / 1024, 1 ) . ' KB' );
    }

    if ( ! file_exists( $jpg_path ) ) {
        error_log( 'AIPhoto: [ERROR] 文件保存后不存在: ' . $jpg_path );
        @unlink( $tmp );
        return new WP_Error( 'save_fail', '保存图片失败' );
    }

    $saved_size = filesize( $jpg_path );
    error_log( 'AIPhoto: [DEBUG] 最终文件大小: ' . round( $saved_size / 1024, 1 ) . ' KB' );

    // 7. 清理临时文件
    @unlink( $tmp );

    // 8. 插入媒体库
    error_log( 'AIPhoto: [DEBUG] 插入媒体库...' );
    $post_title = sanitize_text_field( $prompt ?: 'AI Generated Image ' . date('Y-m-d H:i:s') );

    // 使用 wp_insert_attachment 直接插入本地文件
    $attachment_data = array(
        'post_title'     => $post_title,
        'post_mime_type' => 'image/jpeg',
        'post_status'    => 'inherit',
        'post_content'   => '',
    );

    $attach_id = wp_insert_attachment( $attachment_data, $jpg_path );

    if ( is_wp_error( $attach_id ) ) {
        error_log( 'AIPhoto: [ERROR] 插入附件失败: ' . $attach_id->get_error_message() );
        @unlink( $jpg_path );
        return new WP_Error( 'insert_fail', '保存到媒体库失败: ' . $attach_id->get_error_message() );
    }

    if ( ! $attach_id ) {
        error_log( 'AIPhoto: [ERROR] 插入附件返回 0' );
        @unlink( $jpg_path );
        return new WP_Error( 'insert_fail', '保存到媒体库失败' );
    }

    error_log( 'AIPhoto: [DEBUG] 附件 ID: ' . $attach_id );

    // 生成附件元数据
    $metadata = wp_generate_attachment_metadata( $attach_id, $jpg_path );
    wp_update_attachment_metadata( $attach_id, $metadata );
    error_log( 'AIPhoto: [DEBUG] 附件元数据已生成' );

    // 禁用 ewww-image-optimizer 插件处理 AI 生成图片
    remove_action( 'wp_generate_attachment_metadata', 'ewww_image_optimizer_resize' );
    remove_action( 'wp_generate_attachment_metadata', 'ewww_image_optimizer_webp_convert' );

    // 9. 保存 prompt 到自定义字段（SEO 用）
    update_post_meta( $attach_id, '_aiphoto_prompt', sanitize_textarea_field( $prompt ) );
    update_post_meta( $attach_id, '_aiphoto_original_url', esc_url_raw( $url ) );
    // 保存用户原始输入（不含英文特效/镜头参数）
    $user_prompt = sanitize_textarea_field( $_POST['user_prompt'] ?? $prompt );
    update_post_meta( $attach_id, '_aiphoto_user_prompt', $user_prompt );
    error_log( 'AIPhoto: [DEBUG] Prompt、原始 URL 和用户提示词已保存' );

    // 恢复插件处理
    add_action( 'wp_generate_attachment_metadata', 'ewww_image_optimizer_resize' );
    add_action( 'wp_generate_attachment_metadata', 'ewww_image_optimizer_webp_convert' );

    // 验证最终结果
    $final_url = wp_get_attachment_url( $attach_id );
    error_log( 'AIPhoto: [DEBUG] 最终附件 URL: ' . $final_url );
    error_log( 'AIPhoto: [DEBUG] === 保存流程完成 ===' );

    return get_post( $attach_id );
}

/**
 * 解析 API 错误信息为中文
 */
function aiphoto_parse_api_error( $raw ) {
    $json = json_decode( $raw, true );
    if ( ! $json || ! isset( $json['error'] ) ) {
        return sprintf( 'API 错误: %s', substr( $raw, 0, 200 ) );
    }

    $msg = $json['error']['message'] ?? '';
    $code = $json['error']['code'] ?? '';
    $type = $json['error']['type'] ?? '';

    // 内容策略违规
    if ( $code === 'content_policy_violation' || strpos( strtolower( $msg ), 'policy' ) !== false ) {
        return '⚠️ 提示词包含敏感内容，已被拒绝。请修改描述后重试。';
    }

    // 模型不存在
    if ( $code === 'model_not_found' || strpos( strtolower( $msg ), 'model' ) !== false ) {
        return '⚠️ 模型不存在或不可用，请在后台设置中检查模型名称。';
    }

    // 认证失败
    if ( $code === 'invalid_request_error' || $code === 'authentication_error' ) {
        return '⚠️ API 认证失败，请检查 API Key 是否正确。';
    }

    // 默认：翻译常见错误
    $translations = array(
        'Unable to generate' => '无法生成图片',
        'modify your prompt' => '请修改提示词',
        'Rate limit'         => '请求过于频繁，请稍后重试',
        'insufficient'       => '余额不足',
        'invalid'            => '参数无效',
    );

    foreach ( $translations as $en => $cn ) {
        if ( stripos( $msg, $en ) !== false ) {
            return '⚠️ ' . $cn . '。详细信息：' . $msg;
        }
    }

    return '⚠️ API 错误：' . $msg;
}

/**
 * 后台设置页面
 */
function aiphoto_admin_menu() {
    add_submenu_page(
        'themes.php',
        __( 'Aiphoto 设置', 'aiphoto' ),
        __( 'Aiphoto 设置', 'aiphoto' ),
        'manage_options',
        'aiphoto-settings',
        'aiphoto_settings_page'
    );
}
add_action( 'admin_menu', 'aiphoto_admin_menu' );

function aiphoto_settings_page() {
    $settings = aiphoto_get_settings();
    $has_key  = ! empty( $settings['api_key'] );
    ?>
    <div class="wrap">
        <h1><?php echo esc_html( get_admin_page_title() ); ?></h1>

        <form method="post" action="options.php">
            <?php
            settings_fields( 'aiphoto_settings_group' );
            do_settings_sections( 'aiphoto-settings' );
            submit_button( '保存设置' );
            ?>
        </form>

        <?php if ( $has_key ) : ?>
        <div style="margin-top:20px;padding:12px 16px;background:#f0fdf4;border:1px solid #86efac;border-radius:8px;font-size:0.875rem;">
            <strong>✓ API Key 已配置</strong>
        </div>
        <?php else : ?>
        <div style="margin-top:20px;padding:12px 16px;background:#fef3c7;border:1px solid #fcd34d;border-radius:8px;font-size:0.875rem;">
            <strong>⚠ API Key 未设置</strong>，请在上方输入你的 API Key 并保存。
        </div>
        <?php endif; ?>
    </div>
    <?php
}

function aiphoto_register_settings() {
    register_setting( 'aiphoto_settings_group', 'aiphoto_settings', array(
        'sanitize_callback' => 'aiphoto_sanitize_settings',
    ) );

    add_settings_section(
        'aiphoto_api_section',
        __( 'API 配置', 'aiphoto' ),
        function() {
            echo '<p class="description">' . esc_html__( '配置图片生成 API 设置。默认使用 Agnes Image 2.1 Flash 模型。', 'aiphoto' ) . '</p>';
        },
        'aiphoto-settings'
    );

    // API Key
    add_settings_field(
        'aiphoto_api_key',
        __( 'API 密钥', 'aiphoto' ),
        function( $settings ) {
            $current = $settings['api_key'] ?? '';
            ?>
            <input type="text"
                   name="aiphoto_settings[api_key]"
                   value="<?php echo esc_attr( $current ); ?>"
                   class="regular-text"
                   placeholder="请输入 API Key"
                   autocomplete="off" />
            <p class="description">
                <?php if ( ! empty( $current ) ) : ?>
                    <?php esc_html_e( '已设置 API Key。如需更换，请输入新 Key 并保存。', 'aiphoto' ); ?>
                <?php else : ?>
                    <?php esc_html_e( '请输入你的 API Key（以 wk- 开头）。', 'aiphoto' ); ?>
                <?php endif; ?>
            </p>
            <?php
        },
        'aiphoto-settings',
        'aiphoto_api_section'
    );

    // API 地址
    add_settings_field(
        'aiphoto_api_base_url',
        __( 'API 地址', 'aiphoto' ),
        function( $settings ) {
            $value = $settings['api_base_url'] ?? 'https://apihub.agnes-ai.com';
            ?>
            <input type="url"
                   name="aiphoto_settings[api_base_url]"
                   value="<?php echo esc_url( $value ); ?>"
                   class="regular-text"
                   placeholder="https://apihub.agnes-ai.com" />
            <p class="description">
                <?php esc_html_e( 'API 端点地址。默认为 Agnes AI Hub 地址。', 'aiphoto' ); ?>
            </p>
            <?php
        },
        'aiphoto-settings',
        'aiphoto_api_section'
    );

    // 模型名称
    add_settings_field(
        'aiphoto_api_model',
        __( '模型名称', 'aiphoto' ),
        function( $settings ) {
            $value = $settings['api_model'] ?? 'agnes-image-2.1-flash';
            ?>
            <input type="text"
                   name="aiphoto_settings[api_model]"
                   value="<?php echo esc_attr( $value ); ?>"
                   class="regular-text"
                   placeholder="agnes-image-2.1-flash" />
            <p class="description">
                <?php esc_html_e( '模型名称，如 agnes-image-2.1-flash。也可以填写其他兼容模型的名称。', 'aiphoto' ); ?>
            </p>
            <?php
        },
        'aiphoto-settings',
        'aiphoto_api_section'
    );

    // 图片尺寸
    add_settings_field(
        'aiphoto_api_image_size',
        __( '图片尺寸', 'aiphoto' ),
        function( $settings ) {
            $value = $settings['api_image_size'] ?? '1K';
            $sizes = array(
                '1K'          => '1K (1024×1024)',
                '2K'          => '2K (2048×2048)',
                '3K'          => '3K (3072×3072)',
                '4K'          => '4K (4096×4096)',
                '1024x768'    => '1024 × 768 (4:3)',
                '1024x1024'   => '1024 × 1024 (1:1)',
                '1024x1792'   => '1024 × 1792 (9:16)',
                '1792x1024'   => '1792 × 1024 (16:9)',
            );
            ?>
            <select name="aiphoto_settings[api_image_size]">
                <?php foreach ( $sizes as $key => $label ) : ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
        },
        'aiphoto-settings',
        'aiphoto_api_section'
    );

    // 宽高比
    add_settings_field(
        'aiphoto_api_ratio',
        __( '宽高比', 'aiphoto' ),
        function( $settings ) {
            $value = $settings['api_ratio'] ?? '1:1';
            $ratios = array(
                '1:1'  => '1:1 正方形',
                '3:4'  => '3:4 竖版',
                '4:3'  => '4:3 横版',
                '16:9' => '16:9 宽屏',
                '9:16' => '9:16 竖屏',
                '2:3'  => '2:3',
                '3:2'  => '3:2',
                '21:9' => '21:9 超宽',
            );
            ?>
            <select name="aiphoto_settings[api_ratio]">
                <?php foreach ( $ratios as $key => $label ) : ?>
                    <option value="<?php echo esc_attr( $key ); ?>" <?php selected( $value, $key ); ?>>
                        <?php echo esc_html( $label ); ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <?php
        },
        'aiphoto-settings',
        'aiphoto_api_section'
    );

    // 画廊来源
    add_settings_field(
        'aiphoto_gallery_source',
        __( '画廊来源', 'aiphoto' ),
        function( $settings ) {
            $value = $settings['gallery_source'] ?? 'media';
            ?>
            <select name="aiphoto_settings[gallery_source]">
                <option value="media" <?php selected( $value, 'media' ); ?>><?php esc_html_e( '网站媒体库', 'aiphoto' ); ?></option>
                <option value="cpt" <?php selected( $value, 'cpt' ); ?>><?php esc_html_e( 'AI 图片帖子', 'aiphoto' ); ?></option>
            </select>
            <p class="description"><?php esc_html_e( '选择画廊页面从哪个来源显示图片。', 'aiphoto' ); ?></p>
            <?php
        },
        'aiphoto-settings',
        'aiphoto_api_section'
    );

    // 画廊设置
    add_settings_section(
        'aiphoto_gallery_section',
        __( '画廊设置', 'aiphoto' ),
        function() {
            echo '<p class="description">' . esc_html__( '配置画廊的图片展示方式。', 'aiphoto' ) . '</p>';
        },
        'aiphoto-settings'
    );

    add_settings_field(
        'aiphoto_gallery_per_page',
        __( '每页图片数', 'aiphoto' ),
        function( $settings ) {
            $value = $settings['gallery_per_page'] ?? 48;
            ?>
            <input type="number"
                   name="aiphoto_settings[gallery_per_page]"
                   value="<?php echo esc_attr( $value ); ?>"
                   min="4" max="96" class="small-text" />
            <?php
        },
        'aiphoto-settings',
        'aiphoto_gallery_section'
    );
}
add_action( 'admin_init', 'aiphoto_register_settings' );

function aiphoto_sanitize_settings( $input ) {
    $api_key = isset( $input['api_key'] ) ? trim( $input['api_key'] ) : '';

    return array(
        'api_key'         => $api_key,
        'api_base_url'    => isset( $input['api_base_url'] ) ? esc_url_raw( $input['api_base_url'] ) : '',
        'api_model'       => isset( $input['api_model'] ) ? sanitize_text_field( $input['api_model'] ) : 'agnes-image-2.1-flash',
        'api_image_size'  => isset( $input['api_image_size'] ) ? sanitize_text_field( $input['api_image_size'] ) : '1K',
        'api_ratio'       => isset( $input['api_ratio'] ) ? sanitize_text_field( $input['api_ratio'] ) : '1:1',
        'gallery_source'  => isset( $input['gallery_source'] ) ? sanitize_text_field( $input['gallery_source'] ) : 'media',
        'gallery_per_page'=> isset( $input['gallery_per_page'] ) ? absint( $input['gallery_per_page'] ) : 48,
    );
}

function aiphoto_body_classes( $classes ) {
    if ( is_post_type_archive( 'ai_photo' ) || is_tax( 'photo_category' ) || is_tax( 'photo_style' ) ) {
        $classes[] = 'gallery-page';
    }
    return $classes;
}
add_filter( 'body_class', 'aiphoto_body_classes' );

/**
 * 强制显示页面模板选择器（经典编辑器）
 */
add_action( 'edit_form_after_title', 'aiphoto_show_template_selector' );
function aiphoto_show_template_selector() {
    global $post;
    if ( $post->post_type !== 'page' ) return;
    ?>
    <style>
        #templatediv { display: block !important; }
        #templatediv h2,
        #templatediv .inside { display: block !important; }
        #template div { display: block !important; }
    </style>
    <?php
}

/**
 * 主题激活时自动创建页面
 */
function aiphoto_create_pages() {
    $pages_created = get_option( 'aiphoto_pages_created', false );
    if ( $pages_created ) return;

    $pages = array(
        array(
            'post_title'   => '首页',
            'post_content' => '',
            'post_slug'    => 'home',
            'post_status'  => 'publish',
        ),
        array(
            'post_title'   => '生成图片',
            'post_content' => '',
            'post_slug'    => 'generate',
            'post_status'  => 'publish',
        ),
        array(
            'post_title'   => '画廊',
            'post_content' => '',
            'post_slug'    => 'gallery',
            'post_status'  => 'publish',
        ),
        array(
            'post_title'   => '视频生成',
            'post_content' => '',
            'post_slug'    => 'video',
            'post_status'  => 'publish',
        ),
        array(
            'post_title'   => 'AI 聊天',
            'post_content' => '',
            'post_slug'    => 'chat',
            'post_status'  => 'publish',
        ),
        array(
            'post_title'   => '免责协议',
            'post_content' => '',
            'post_slug'    => 'disclaimer',
            'post_status'  => 'publish',
        ),
        array(
            'post_title'   => '网站地图',
            'post_content' => '',
            'post_slug'    => 'sitemap',
            'post_status'  => 'publish',
        ),
    );

    foreach ( $pages as $page ) {
        $exists = get_page_by_path( $page['post_slug'] );
        if ( ! $exists ) {
            wp_insert_post( array(
                'post_title'   => $page['post_title'],
                'post_content' => $page['post_content'],
                'post_name'    => $page['post_slug'],
                'post_status'  => $page['post_status'],
                'post_type'    => 'page',
            ) );
        }
    }

    update_option( 'aiphoto_pages_created', true );
}
register_activation_hook( __FILE__, 'aiphoto_create_pages' );

// 每次加载时也检查（如果手动删除了页面）
add_action( 'init', 'aiphoto_create_pages' );

/**
 * 强制页面使用 front-page.php 模板
 */
add_filter( 'page_template', 'aiphoto_force_front_page_template' );
function aiphoto_force_front_page_template( $template ) {
    $valid_pages = array( 'generate', 'gallery', 'video' );
    global $post;
    if ( $post && in_array( $post->post_name, $valid_pages, true ) ) {
        return dirname( __FILE__ ) . '/front-page.php';
    }
    return $template;
}

/**
 * 早期路由拦截 — 页面不存在时也拦截，防止 404 闪烁
 */
add_action( 'template_redirect', 'aiphoto_early_route_intercept' );
function aiphoto_early_route_intercept() {
    $valid = array( 'generate', 'gallery', 'video' );
    $uri = strtok( $_SERVER['REQUEST_URI'], '?' ) ?: '';
    $slug = basename( rtrim( $uri, '/' ) );

    // 免责协议、网站地图、AI聊天页面使用独立模板，不走 front-page.php
    if ( $slug === 'disclaimer' || $slug === 'sitemap' || $slug === 'chat' ) {
        return;
    }

    if ( in_array( $slug, $valid, true ) ) {
        // 强制加载 front-page.php
        include( dirname( __FILE__ ) . '/front-page.php' );
        exit;
    }
}
