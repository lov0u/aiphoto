<?php
/**
 * AIPhoto 主题函数
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

// 增加内存限制和执行时间
@ini_set( 'memory_limit', '256M' );
@ini_set( 'max_execution_time', '300' ); // 5分钟超时

/**
 * 加载 AI 提示词配置
 */
function aiphoto_get_prompts() {
    static $prompts = null;
    if ( $prompts === null ) {
        $file = get_template_directory() . '/ai-prompts.php';
        if ( file_exists( $file ) ) {
            $prompts = require $file;
        } else {
            $prompts = array( 'chat' => array( 'system_prompt' => '' ), 'skills' => array() );
        }
    }
    return $prompts;
}

/**
 * 获取聊天系统提示词（含自定义技能）
 */
function aiphoto_get_chat_prompt() {
    $prompts = aiphoto_get_prompts();
    $base = $prompts['chat']['system_prompt'] ?? '';
    $skills = $prompts['skills'] ?? array();
    if ( ! empty( $skills ) ) {
        $base .= "\n\n自定义规则：\n" . implode( "\n", array_map( function( $i, $s ) { return ( $i + 1 ) . '. ' . $s; }, array_keys( $skills ), $skills ) );
    }
    return $base;
}

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
        'api_key'         => 'wk-mGhyMZKCVgzlgixVmvWdxrK7JJUmUymI6oAgFt7Qug0m',
        'api_base_url'    => 'https://apihub.agnes-ai.com',
        'api_model'       => 'agnes-image-2.1-flash',
        'api_image_size'  => '1K',
        'api_ratio'       => '1:1',
        'gallery_source'  => 'media',
        'gallery_per_page'=> 48,
    );
    return wp_parse_args( get_option( 'aiphoto_settings', array() ), $defaults );
}

// ============================================================
// 生图规则增强系统（Flux + Portrait Framework + GPT Image 2）
// ============================================================

/**
 * 中文关键词映射（用户输入中文时自动转英文）
 */
function aiphoto_translate_prompt( $prompt ) {
    $zh_en_map = array(
        // 自然
        '美女' => 'beautiful woman', '帅哥' => 'handsome man', '风景' => 'beautiful landscape',
        '海边' => 'seaside beach', '日落' => 'golden sunset', '日出' => 'sunrise',
        '森林' => 'lush forest', '星空' => 'starry night sky', '夜景' => 'night cityscape',
        '猫' => 'cute cat', '狗' => 'loyal dog', '花' => 'flowers', '山' => 'mountain',
        '湖' => 'serene lake', '雪' => 'snow', '雨' => 'rain', '月亮' => 'moon',
        '大海' => 'ocean', '城市' => 'cityscape', '沙漠' => 'desert landscape',
        '瀑布' => 'waterfall', '樱花' => 'cherry blossom', '竹林' => 'bamboo forest',
        '草地' => 'green meadow', '河流' => 'river',
        // 风格
        '古风' => 'traditional Chinese style', '水墨' => 'Chinese ink painting',
        '二次元' => 'anime style', '赛博朋克' => 'cyberpunk style', '写实' => 'photorealistic',
        '唯美' => 'beautiful aesthetic', '浪漫' => 'romantic atmosphere', '梦幻' => 'dreamlike ethereal',
        '复古' => 'vintage retro style', '清新' => 'fresh clean aesthetic', '暗黑' => 'dark moody',
        '阳光' => 'warm sunshine', '黄昏' => 'dusk twilight', '黎明' => 'dawn light',
        // 场景
        '古镇' => 'ancient town', '城堡' => 'castle', '飞船' => 'spaceship', '机器人' => 'robot',
        '精灵' => 'elf', '龙' => 'dragon', '人物' => 'person', '小孩' => 'child',
        '老人' => 'elderly person', '情侣' => 'couple', '家庭' => 'family',
        // 负面词替代
        '不要模糊' => 'sharp focus, crisp details', '不要噪点' => 'clean image, smooth gradients',
        '不要水印' => 'pristine image, clean composition', '不要文字' => 'clean surfaces, unmarked',
        '不要人群' => 'empty scene, solitary subject',
    );
    if ( preg_match( '/[\x{4e00}-\x{9fff}]/u', $prompt ) ) {
        foreach ( $zh_en_map as $zh => $en ) {
            $prompt = str_replace( $zh, $en, $prompt );
        }
    }
    return $prompt;
}

/**
 * 时代/朝代自动推断
 */
function aiphoto_era_enhance( $prompt ) {
    $era_map = array(
        '古代' => 'ancient China, traditional architecture, period costume',
        '唐朝' => 'Tang Dynasty, ornate golden details, luxurious silk robes',
        '宋朝' => 'Song Dynasty, elegant minimalist aesthetic, ink painting style',
        '明朝' => 'Ming Dynasty, red and gold color scheme, imperial court style',
        '民国' => 'Republic of China era, qipao, old Shanghai aesthetic',
        '现代' => 'modern contemporary, urban setting',
        '未来' => 'futuristic, sci-fi, high-tech environment',
        '中世纪' => 'medieval European, gothic architecture, knights',
        '维多利亚' => 'Victorian era, ornate details, gaslight atmosphere',
    );
    foreach ( $era_map as $zh => $en ) {
        if ( mb_strpos( $prompt, $zh ) !== false && mb_stripos( $prompt, $en ) === false ) {
            $prompt .= ', ' . $en;
        }
    }
    return $prompt;
}

/**
 * 依赖自动推断（古装→中式服装等）
 */
function aiphoto_dependency_infer( $prompt ) {
    // 古装自动推导
    if ( preg_match( '/(古风|ancient|traditional chinese|古代|汉服|唐朝|宋朝|明朝)/i', $prompt ) ) {
        if ( ! preg_match( '/(clothing|服装|汉服|旗袍|古装|hanfu|qipao)/i', $prompt ) ) {
            $prompt .= ', traditional Chinese hanfu clothing';
        }
        if ( ! preg_match( '/(hair|头发|发型|发髻|updo)/i', $prompt ) ) {
            $prompt .= ', classical Chinese updo hairstyle';
        }
    }
    // 东亚人种默认发色
    if ( preg_match( '/(东亚|East Asian|中国人|Japanese|Korean|Chinese|中国|日本|韩国)/i', $prompt ) ) {
        if ( ! preg_match( '/(hair color|发色|blonde|brown|red|black hair)/i', $prompt ) ) {
            $prompt .= ', black hair';
        }
    }
    return $prompt;
}

/**
 * 人像结构化增强（面部/皮肤/姿势）
 * 注意：只在镜头为 portrait 时才增强，避免场景描述被强制变成特写
 */
function aiphoto_portrait_enhance( $prompt, $lens = '' ) {
    // 只有镜头选"人像镜头"时才增强面部细节
    if ( $lens !== 'portrait' ) return $prompt;

    // 只有纯人像描述（没有场景动作词）才增强
    $scene_words = array( '摘', '摘桃', '采', '做', '走', '跑', '跳', '游泳', '做饭', '画画', '弹琴', 'reading', 'cooking', 'running', 'jumping', 'swimming', 'picking' );
    foreach ( $scene_words as $w ) {
        if ( mb_stripos( $prompt, $w ) !== false ) return $prompt;
    }

    $portrait_suffix = '';
    if ( ! preg_match( '/(eye|眼睛|眼|eyes)/i', $prompt ) ) {
        $portrait_suffix .= ', detailed expressive eyes';
    }
    if ( ! preg_match( '/(skin|皮肤|肤质|skin texture)/i', $prompt ) ) {
        $portrait_suffix .= ', natural skin texture';
    }
    return $prompt . $portrait_suffix;
}

/**
 * 色温增强
 */
function aiphoto_color_enhance( $prompt ) {
    if ( preg_match( '/#[0-9A-Fa-f]{6}/', $prompt ) ) {
        $prompt .= ', brand color accuracy, precise color matching';
    }
    $color_temp = array(
        '红色' => 'warm red tones', '蓝色' => 'cool blue tones',
        '绿色' => 'natural green tones', '金色' => 'golden warm tones',
        '紫色' => 'mystical purple tones', '粉色' => 'soft pink tones',
        '白色' => 'pure white tones', '黑色' => 'deep black tones',
    );
    foreach ( $color_temp as $zh => $en ) {
        if ( mb_strpos( $prompt, $zh ) !== false ) {
            $prompt .= ', ' . $en;
        }
    }
    return $prompt;
}

/**
 * 构图规则自动补充
 */
function aiphoto_composition_enhance( $prompt, $effect ) {
    $has_composition = preg_match( '/(composition|rule of thirds|leading lines|symmetry|framing|构图|三分法|对称)/i', $prompt );
    if ( ! $has_composition ) {
        $type_composition = array(
            'portrait' => ', rule of thirds composition, subject off-center',
            'landscape' => ', rule of thirds, leading lines to horizon',
            'product' => ', centered composition, product hero shot',
            'poster' => ', balanced composition, clear visual hierarchy',
        );
        foreach ( $type_composition as $type => $comp ) {
            if ( mb_stripos( $prompt, $type ) !== false ) {
                $prompt .= $comp;
                break;
            }
        }
    }
    return $prompt;
}

/**
 * 提示词增强引擎（光照+质量词）
 */
function aiphoto_enhance_prompt( $user_prompt, $effect, $lens ) {
    $enhanced = $user_prompt;

    // 检测是否缺少光照，自动补充
    $has_lighting = preg_match( '/(light|sunlight|lighting|光线|光照|阳光|月光|灯光)/i', $user_prompt );
    if ( ! $has_lighting ) {
        $effect_lighting = array(
            'cinematic'      => ', dramatic golden hour lighting with rim light and volumetric rays',
            'photorealistic' => ', natural soft diffused lighting, gentle shadows',
            'fantasy'        => ', ethereal mystical lighting with god rays and ambient glow',
            'cyberpunk'      => ', neon glow lighting with volumetric haze and light reflections',
            'anime'          => ', bright vibrant lighting, clean cel shading, soft gradients',
            'watercolor'     => ', soft natural daylight, gentle washes of light',
            'oil-painting'   => ', dramatic chiaroscuro lighting, strong contrast',
            '3d-render'      => ', studio three-point lighting, soft shadows, global illumination',
            'pixel-art'      => ', flat pixel lighting, limited shading',
            'cartoon'        => ', bright flat cartoon lighting, soft shadows',
        );
        if ( isset( $effect_lighting[ $effect ] ) ) {
            $enhanced .= $effect_lighting[ $effect ];
        }
    }

    // 始终追加质量关键词
    $enhanced .= ', high resolution, sharp focus, detailed, professional quality';
    return $enhanced;
}

/**
 * 提示词长度验证（Flux 最优 30-80 词）
 */
function aiphoto_validate_prompt_length( $prompt ) {
    $word_count = str_word_count( $prompt );
    if ( $word_count < 10 ) {
        $prompt .= ', high resolution, detailed, professional quality, sharp focus';
    }
    return $prompt;
}

/**
 * 负面提示词替代（"不要xxx" → 正面描述）
 */
function aiphoto_positive_alternatives( $prompt ) {
    $replacements = array(
        'no blur' => 'sharp focus, crisp details, tack-sharp',
        'no noise' => 'clean image, smooth gradients, low ISO',
        'no watermark' => 'pristine image, clean composition',
        'no text' => 'clean surfaces, unmarked, text-free',
        'no people' => 'empty scene, solitary subject, deserted',
    );
    foreach ( $replacements as $old => $new ) {
        $prompt = str_ireplace( $old, $new, $prompt );
    }
    return $prompt;
}

/**
 * 内容审核规则（5层防护 - 第2层：后端PHP过滤）
 */
function aiphoto_content_check( $prompt ) {
    $prompt_lower = mb_strtolower( $prompt );

    // 完全阻止的词
    $blocked_words = array(
        'nude', 'naked', '裸体', '裸露', '露点', '色情', 'porn', 'sexual',
        'topless', 'bottomless', 'uncensored', 'nsfw',
        'blood', 'gore', 'violence', 'kill', 'murder', '血腥', '暴力',
        'drug', 'weapon', 'gun', 'bomb', '毒品', '武器', '枪',
    );
    foreach ( $blocked_words as $word ) {
        if ( mb_strpos( $prompt_lower, $word ) !== false ) {
            return array( 'pass' => false, 'reason' => 'blocked_word', 'message' => '提示词包含不当内容，请修改' );
        }
    }

    // 需要审核的词（标记但不直接拒绝）
    $review_words = array(
        'bikini', '泳装', '比基尼', '内衣', 'underwear', 'lingerie',
        'sensual', 'sexy', '性感', '诱惑', 'seductive', 'boudoir',
    );
    foreach ( $review_words as $word ) {
        if ( mb_strpos( $prompt_lower, $word ) !== false ) {
            return array( 'pass' => false, 'reason' => 'need_review', 'message' => '该内容需要审核，暂不支持生成' );
        }
    }

    return array( 'pass' => true );
}

// ============================================================
// AI 提示词增强（用 agnes-2.0-flash 分析用户输入）
// ============================================================

/**
 * 用 AI 模型将用户简短描述转换为高质量英文提示词
 */
function aiphoto_ai_enhance_prompt( $user_prompt, $effect = '', $lens = '', $template = '' ) {
    $settings = aiphoto_get_settings();
    if ( empty( $settings['api_key'] ) ) return $user_prompt;

    $api_url = rtrim( $settings['api_base_url'], '/' ) . '/v1/chat/completions';

    // ========== 从配置文件读取提示词和映射表 ==========
    $prompts = aiphoto_get_prompts();
    $img_cfg = $prompts['image_enhance'] ?? array();
    $system_prompt = $img_cfg['system_prompt'] ?? '';
    $effect_map = $img_cfg['effect_map'] ?? array();
    $lens_map = $img_cfg['lens_map'] ?? array();
    $template_map = $img_cfg['template_map'] ?? array();

    if ( ! empty( $effect ) && isset( $effect_map[ $effect ] ) ) {
        $system_prompt .= $effect_map[ $effect ] . '\n';
    }
    if ( ! empty( $lens ) && isset( $lens_map[ $lens ] ) ) {
        $system_prompt .= $lens_map[ $lens ] . '\n';
    }
    if ( ! empty( $template ) && isset( $template_map[ $template ] ) ) {
        $system_prompt .= $template_map[ $template ] . '\n';
    }

    $system_prompt .= '\n请根据以上分析，选择最相关的 SKILL 规则，生成高质量英文提示词。只输出最终提示词，不要解释。';

    $messages = array(
        array( 'role' => 'system', 'content' => $system_prompt ),
        array( 'role' => 'user', 'content' => $user_prompt ),
    );

    $body = array(
        'model'       => $img_cfg['model'] ?? 'agnes-2.0-flash',
        'messages'    => $messages,
        'max_tokens'  => $img_cfg['max_tokens'] ?? 200,
        'temperature' => $img_cfg['temperature'] ?? 0.7,
        'stream'      => false,
    );

    $response = wp_remote_post( $api_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $settings['api_key'],
            'Content-Type'  => 'application/json',
        ),
        'body'    => wp_json_encode( $body ),
        'timeout' => 45,
    ) );

    if ( is_wp_error( $response ) ) {
        error_log( 'AIPhoto: [AI_ENHANCE] API error: ' . $response->get_error_message() );
        return $user_prompt;
    }

    $resp_body = wp_remote_retrieve_body( $response );
    $data = json_decode( $resp_body, true );

    if ( isset( $data['choices'][0]['message']['content'] ) ) {
        $enhanced = trim( $data['choices'][0]['message']['content'] );
        error_log( 'AIPhoto: [AI_ENHANCE] Original: ' . $user_prompt );
        error_log( 'AIPhoto: [AI_ENHANCE] Enhanced: ' . $enhanced );
        return $enhanced;
    }

    // AI 返回异常，记录完整响应
    error_log( 'AIPhoto: [AI_ENHANCE] Unexpected response: ' . mb_substr( $resp_body, 0, 500 ) );
    return $user_prompt;
}

// ============================================================
// 预设提示词模板（来自 GPT Image 2 Skill 的 17 大类）
// ============================================================
function aiphoto_get_predefined_templates() {
    return array(
        // 1. UI样机
        'ui-mockup' => array(
            'name' => 'UI界面样机',
            'name_en' => 'UI Mockup',
            'prompt' => 'UI mockup of {app_name}, {screen_type} screen, {design_style} design, clean interface, professional UI/UX, high resolution, realistic device frame, screen interaction elements',
            'defaults' => array( 'effect' => '3d-render', 'lens' => 'eye-level' ),
        ),
        // 2. 产品视觉
        'product' => array(
            'name' => '产品摄影',
            'name_en' => 'Product Visuals',
            'prompt' => 'Professional product photography of {product}, clean white studio background, soft even lighting, commercial quality, sharp focus throughout, reflection on glossy surface, 8K resolution, marketing-ready',
            'defaults' => array( 'effect' => 'photorealistic', 'lens' => 'close-up' ),
        ),
        // 3. 地图类
        'map' => array(
            'name' => '地图/路线图',
            'name_en' => 'Maps',
            'prompt' => 'Illustrated map of {location}, {map_style} style, marked points of interest, decorative landmarks, legend/key, vibrant colors, clear typography, tourist-friendly design',
            'defaults' => array( 'effect' => 'cartoon', 'lens' => 'birdseye' ),
        ),
        // 4. 幻灯片
        'slide' => array(
            'name' => '幻灯片/演示文稿',
            'name_en' => 'Slides & Visual Docs',
            'prompt' => 'Professional presentation slide about {topic}, {layout} layout, clean typography, data visualization, charts and graphs, corporate design style, high resolution, print quality',
            'defaults' => array( 'effect' => '3d-render', 'lens' => 'eye-level' ),
        ),
        // 5. 海报/活动
        'poster' => array(
            'name' => '品牌海报',
            'name_en' => 'Poster & Campaigns',
            'prompt' => 'Professional brand poster featuring {subject}, {style} style, {lighting}, bold headline typography, {composition}, high resolution, print quality, marketing visual',
            'defaults' => array( 'effect' => 'cinematic', 'lens' => 'portrait' ),
        ),
        // 6. 人像/角色
        'portrait' => array(
            'name' => '人像摄影',
            'name_en' => 'Portraits & Characters',
            'prompt' => 'Professional portrait photography of {subject}, {expression}, natural skin texture, soft studio lighting with key light and fill light, shallow depth of field, shot on 85mm f/1.4 lens, bokeh background, 8K resolution, professional color science',
            'defaults' => array( 'effect' => 'photorealistic', 'lens' => 'portrait' ),
        ),
        // 7. 场景/插画
        'scene' => array(
            'name' => '场景/插画',
            'name_en' => 'Scenes & Illustrations',
            'prompt' => 'Atmospheric illustration of {scene}, {mood} mood, {time_of_day} lighting, detailed environment, storytelling composition, art direction, high resolution, concept art quality',
            'defaults' => array( 'effect' => 'fantasy', 'lens' => 'wide-angle' ),
        ),
        // 8. 编辑工作流
        'editing' => array(
            'name' => '图片编辑',
            'name_en' => 'Editing Workflows',
            'prompt' => 'Professional photo editing: {edit_instruction}, maintaining original quality, seamless blending, natural result, high resolution output',
            'defaults' => array( 'effect' => 'photorealistic', 'lens' => 'eye-level' ),
        ),
        // 9. 头像/个人资料
        'avatar' => array(
            'name' => '风格化头像',
            'name_en' => 'Avatars & Profile',
            'prompt' => 'Stylized avatar of {subject}, {avatar_style} style, clean background, centered composition, detailed features, vibrant colors, social media ready, high resolution',
            'defaults' => array( 'effect' => 'cartoon', 'lens' => 'close-up' ),
        ),
        // 10. 分镜/序列
        'storyboard' => array(
            'name' => '分镜/漫画',
            'name_en' => 'Storyboards & Sequences',
            'prompt' => 'Sequential art storyboard of {story}, {panel_count} panels, {art_style} style, narrative flow, dialogue bubbles, cinematic composition, professional quality',
            'defaults' => array( 'effect' => 'anime', 'lens' => 'eye-level' ),
        ),
        // 11. 网格/拼贴
        'grid' => array(
            'name' => '网格/拼贴',
            'name_en' => 'Grids & Collages',
            'prompt' => 'Grid collage of {subject}, {grid_size} layout, unified design theme, consistent color palette, each panel distinct yet cohesive, high resolution, print ready',
            'defaults' => array( 'effect' => 'photorealistic', 'lens' => 'eye-level' ),
        ),
        // 12. 品牌/包装
        'branding' => array(
            'name' => '品牌/包装设计',
            'name_en' => 'Branding & Packaging',
            'prompt' => 'Brand identity design for {brand_name}, {package_type}, {design_style} style, logo placement, color scheme, material texture, professional product photography, high resolution',
            'defaults' => array( 'effect' => 'photorealistic', 'lens' => 'close-up' ),
        ),
        // 13. 文字排版
        'typography' => array(
            'name' => '文字排版海报',
            'name_en' => 'Typography & Text Layout',
            'prompt' => 'Typography poster with "{text}" in {font_style} font, {text_layout} layout, {color_scheme} colors, graphic design, print quality, high resolution, editorial design',
            'defaults' => array( 'effect' => '3d-render', 'lens' => 'eye-level' ),
        ),
        // 14. 素材/道具
        'asset' => array(
            'name' => '图标/素材',
            'name_en' => 'Assets & Props',
            'prompt' => '{asset_type} icon set, {icon_style} style, consistent design language, clean edges, isolated on transparent/white background, high resolution, UI ready',
            'defaults' => array( 'effect' => '3d-render', 'lens' => 'eye-level' ),
        ),
        // 15. 学术图表
        'academic' => array(
            'name' => '学术图表',
            'name_en' => 'Academic Figures',
            'prompt' => 'Academic figure: {figure_type} about {topic}, white background, clean geometric shapes, publication-ready, professional typography, low saturation engineering colors, precise layout',
            'defaults' => array( 'effect' => 'photorealistic', 'lens' => 'eye-level' ),
        ),
        // 16. 信息图
        'infographic' => array(
            'name' => '信息图',
            'name_en' => 'Infographics',
            'prompt' => 'Professional infographic about {topic}, {infographic_style} style, data visualization, icons and illustrations, clear hierarchy, {color_scheme}, high information density, print quality',
            'defaults' => array( 'effect' => '3d-render', 'lens' => 'eye-level' ),
        ),
        // 17. 技术图表
        'technical' => array(
            'name' => '技术架构图',
            'name_en' => 'Technical Diagrams',
            'prompt' => 'Technical diagram: {diagram_type}, dark grid background, monospace font, role-based color coding, arrows and connectors, clean layout, engineering diagram quality, high resolution',
            'defaults' => array( 'effect' => '3d-render', 'lens' => 'eye-level' ),
        ),
        // 额外风格类模板
        'anime' => array(
            'name' => '动漫风格',
            'name_en' => 'Anime Style',
            'prompt' => 'Anime style illustration of {subject}, cel shading, vibrant colors, detailed expressive eyes, clean linework, dynamic composition, soft gradients, studio quality, 4K resolution',
            'defaults' => array( 'effect' => 'anime', 'lens' => 'portrait' ),
        ),
        'fantasy' => array(
            'name' => '奇幻场景',
            'name_en' => 'Fantasy Scene',
            'prompt' => 'Epic fantasy scene of {subject}, ethereal magical lighting, mystical atmosphere, enchanted environment, volumetric fog, god rays, dreamlike quality, concept art, 8K resolution',
            'defaults' => array( 'effect' => 'fantasy', 'lens' => 'wide-angle' ),
        ),
        'chinese_ink' => array(
            'name' => '中国水墨',
            'name_en' => 'Chinese Ink Painting',
            'prompt' => 'Traditional Chinese ink painting of {subject}, flowing brush strokes with varying ink density, minimalist composition emphasizing negative space, monochromatic black ink with subtle grey washes, poetic atmosphere, masterful brushwork technique',
            'defaults' => array( 'effect' => 'watercolor', 'lens' => 'wide-angle' ),
        ),
        'cyberpunk' => array(
            'name' => '赛博朋克',
            'name_en' => 'Cyberpunk',
            'prompt' => 'Cyberpunk cityscape of {scene}, neon glow lighting with volumetric haze, futuristic architecture, rain-slicked streets reflecting lights, high contrast, dark atmosphere, holographic displays, 8K resolution',
            'defaults' => array( 'effect' => 'cyberpunk', 'lens' => 'wide-angle' ),
        ),
        'cinematic' => array(
            'name' => '电影感',
            'name_en' => 'Cinematic',
            'prompt' => 'Cinematic scene of {subject}, dramatic golden hour lighting with rim light, anamorphic lens flare, film grain, shallow depth of field, movie scene quality, professional color grading, 8K resolution',
            'defaults' => array( 'effect' => 'cinematic', 'lens' => 'portrait' ),
        ),
        'landscape' => array(
            'name' => '风景摄影',
            'name_en' => 'Landscape Photography',
            'prompt' => 'Stunning landscape photography of {scene}, golden hour lighting, dramatic sky with clouds, leading lines, rule of thirds composition, shot on wide angle 16mm lens, deep depth of field, vivid colors, 8K resolution',
            'defaults' => array( 'effect' => 'photorealistic', 'lens' => 'wide-angle' ),
        ),
    );
}

/**
 * AJAX 获取预设模板列表
 */
function aiphoto_get_templates() {
    $templates = aiphoto_get_predefined_templates();
    // 只返回 name 和 name_en，不返回完整 prompt
    $list = array();
    foreach ( $templates as $key => $tpl ) {
        $list[] = array(
            'key'     => $key,
            'name'    => $tpl['name'],
            'name_en' => $tpl['name_en'],
        );
    }
    wp_send_json_success( $list );
}
add_action( 'wp_ajax_aiphoto_get_templates', 'aiphoto_get_templates' );
add_action( 'wp_ajax_nopriv_aiphoto_get_templates', 'aiphoto_get_templates' );

/**
 * AJAX 根据模板key获取完整模板
 */
function aiphoto_get_template_detail() {
    $key = sanitize_text_field( $_GET['key'] ?? '' );
    $templates = aiphoto_get_predefined_templates();
    if ( isset( $templates[ $key ] ) ) {
        wp_send_json_success( $templates[ $key ] );
    } else {
        wp_send_json_error( array( 'message' => '模板不存在' ) );
    }
}
add_action( 'wp_ajax_aiphoto_get_template_detail', 'aiphoto_get_template_detail' );
add_action( 'wp_ajax_nopriv_aiphoto_get_template_detail', 'aiphoto_get_template_detail' );

// ============================================================
// AJAX AI 提示词增强（独立接口，供前端第一步调用）
// ============================================================
function aiphoto_ai_enhance_ajax() {
    if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'aiphoto_nonce' ) ) {
        wp_send_json_error( array( 'message' => '安全验证失败' ) );
    }

    $prompt   = sanitize_text_field( $_GET['prompt'] ?? '' );
    $effect   = sanitize_text_field( $_GET['effect'] ?? '' );
    $lens     = sanitize_text_field( $_GET['lens'] ?? '' );
    $template = sanitize_text_field( $_GET['template'] ?? '' );

    if ( empty( $prompt ) ) {
        wp_send_json_error( array( 'message' => '提示词为空' ) );
    }

    $enhanced = aiphoto_ai_enhance_prompt( $prompt, $effect, $lens, $template );
    wp_send_json_success( array( 'enhanced' => $enhanced ) );
}
add_action( 'wp_ajax_aiphoto_ai_enhance', 'aiphoto_ai_enhance_ajax' );
add_action( 'wp_ajax_nopriv_aiphoto_ai_enhance', 'aiphoto_ai_enhance_ajax' );

// ============================================================
// AJAX 图片生成
// ============================================================
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
    $effect   = sanitize_text_field( $_POST['effect'] ?? '' );
    $lens     = sanitize_text_field( $_POST['lens'] ?? '' );
    $template = sanitize_text_field( $_POST['template'] ?? '' );

    // 特效和镜头翻译（增强版 - 参考 Flux Best Practices）
    $effect_map = array(
        'cinematic'      => 'cinematic lighting, dramatic atmosphere, movie scene quality, professional color grading, film grain, volumetric lighting, anamorphic lens flare, shallow depth of field, warm color temperature',
        'pixel-art'      => 'pixel art style, retro 8-bit game aesthetic, clear pixel details, nostalgic retro atmosphere, limited color palette, crisp edges, dithering',
        'cartoon'        => 'cartoon style, vibrant colors, bold outlines, playful and fun, animated movie quality, clean shapes, expressive features, cel shading',
        '3d-render'      => '3D rendered, octane render, ray tracing, ultra detailed, studio lighting, physically based rendering, subsurface scattering, global illumination',
        'watercolor'     => 'watercolor painting style, soft translucent colors, paper texture, artistic brush strokes, delicate washes, wet-on-wet blending, pigment granulation',
        'oil-painting'   => 'oil painting style, rich textures, impasto technique, classical art, canvas texture, dramatic chiaroscuro, visible brushwork, color mixing',
        'anime'          => 'anime style, Japanese animation aesthetic, cel shading, vibrant colors, detailed eyes, manga quality, clean linework, dynamic composition, studio quality, soft gradients',
        'photorealistic' => 'photorealistic, ultra HD photography, natural lighting, shot on professional DSLR, hyper detailed, sharp focus, RAW photo, 8K resolution, professional color science, accurate skin tones',
        'cyberpunk'      => 'cyberpunk style, neon lights, futuristic cityscape, high tech low life, glowing elements, dark atmosphere, rain-slicked streets, volumetric haze, holographic displays',
        'fantasy'        => 'fantasy art style, magical atmosphere, ethereal lighting, mystical elements, dreamlike quality, volumetric fog, god rays, concept art, rich color palette, epic scale',
    );

    $lens_map = array(
        'wide-angle' => 'wide angle lens 24mm, expansive perspective, dramatic depth, leading lines, foreground interest, deep depth of field, architectural distortion',
        'macro'      => 'macro photography, extreme close-up, shallow depth of field, bokeh background, 100mm macro lens, razor sharp detail, magnified textures',
        'birdseye'   => 'bird\'s eye view, aerial perspective, top-down shot, panoramic overview, drone photography, map-like composition, vast scale',
        'eye-level'  => 'eye level shot, natural perspective, documentary style, neutral angle, immersive viewing, human-scale composition',
        'low-angle'  => 'low angle shot, dramatic perspective, towering subject, looking up, powerful imposing, worm\'s eye view, strong vertical lines',
        'close-up'   => 'close-up shot, intimate framing, detailed focus, shallow depth of field, macro lens 100mm, creamy bokeh, emotional connection',
        'portrait'   => 'portrait photography, professional headshot, studio lighting, blurred background, bokeh, 85mm f/1.4 lens, shallow depth of field, subject separation, catchlight in eyes',
        'panoramic'  => 'panoramic view, wide sweeping landscape, ultra wide angle, cinematic aspect ratio, stitched panorama, vast horizon, epic scale',
    );

    // ========== 增强链：AI 已在前端完成，这里做最终检查 ==========
    // 前端已调用 AI 增强，这里只需做内容审核和补充

    // 0. 过滤不支持的参数（如 --ar, --chaos, --style 等 Midjourney 风格参数）
    $prompt = preg_replace( '/\s*--\w+\s+\S+/', '', $prompt );
    $prompt = trim( $prompt );

    // 1. 内容审核
    $check_result = aiphoto_content_check( $prompt );
    if ( ! $check_result['pass'] ) {
        wp_send_json_error( array( 'message' => $check_result['message'] ) );
        exit;
    }

    // 2. 确保画质关键词
    if ( stripos( $prompt, '8K' ) === false && stripos( $prompt, 'resolution' ) === false ) {
        $prompt .= ', 8K resolution, sharp focus, detailed';
    }

    error_log( 'AIPhoto: [ENHANCE] Final prompt: ' . $prompt );

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
        error_log( 'AIPhoto: [GENERATE] API error: ' . $response->get_error_message() );
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
 * 语音识别 - 调用本地 Vosk API
 */
function aiphoto_recognize_speech() {
    $audio = isset( $_POST['audio'] ) ? sanitize_text_field( $_POST['audio'] ) : '';
    if ( empty( $audio ) ) {
        wp_send_json_error( array( 'message' => '没有音频数据' ) );
    }

    $response = wp_remote_post( 'http://127.0.0.1:5000/api/recognize', array(
        'headers' => array( 'Content-Type' => 'application/json' ),
        'body' => wp_json_encode( array( 'audio' => $audio ) ),
        'timeout' => 30,
    ) );

    if ( is_wp_error( $response ) ) {
        wp_send_json_error( array( 'message' => '语音识别服务连接失败' ) );
    }

    $body = wp_remote_retrieve_body( $response );
    $result = json_decode( $body, true );

    if ( isset( $result['text'] ) && $result['text'] !== '' ) {
        wp_send_json_success( array( 'text' => $result['text'] ) );
    } else {
        wp_send_json_error( array( 'message' => '未识别到语音内容' ) );
    }
}
add_action( 'wp_ajax_aiphoto_recognize_speech', 'aiphoto_recognize_speech' );
add_action( 'wp_ajax_nopriv_aiphoto_recognize_speech', 'aiphoto_recognize_speech' );

/**
 * AI 助手 - 流式输出接口
 */
function aiphoto_chat_stream() {
    if ( ! isset( $_GET['nonce'] ) || ! wp_verify_nonce( $_GET['nonce'], 'aiphoto_nonce' ) ) {
        http_response_code(403);
        echo "data: " . wp_json_encode(array('error' => 'nonce验证失败')) . "\n\n";
        echo "data: [DONE]\n\n";
        exit;
    }

    $message = sanitize_textarea_field( $_GET['message'] ?? '' );
    // 注意：不能对JSON字符串用sanitize_text_field，会破坏内容
    $history_raw = wp_unslash( $_GET['history'] ?? '[]' );
    $history = json_decode( $history_raw, true );
    if ( ! is_array( $history ) ) $history = array();

    if ( empty( $message ) ) {
        http_response_code(400);
        echo "data: " . wp_json_encode(array('error' => '消息为空')) . "\n\n";
        echo "data: [DONE]\n\n";
        exit;
    }

    $settings = aiphoto_get_settings();
    if ( empty( $settings['api_key'] ) ) {
        http_response_code(500);
        echo "data: " . wp_json_encode(array('error' => 'API密钥未配置')) . "\n\n";
        echo "data: [DONE]\n\n";
        exit;
    }

    $api_url = rtrim( $settings['api_base_url'], '/' ) . '/v1/chat/completions';

    $messages = array(
        array( 'role' => 'system', 'content' => aiphoto_get_chat_prompt() ),
    );

    if ( ! empty( $history ) && is_array( $history ) ) {
        foreach ( array_slice( $history, -100 ) as $msg ) {
            if ( isset( $msg['role'] ) && isset( $msg['content'] ) ) {
                $messages[] = array( 'role' => in_array( $msg['role'], array('user','assistant','system') ) ? $msg['role'] : 'user', 'content' => trim( $msg['content'] ) );
            }
        }
    }

    $messages[] = array( 'role' => 'user', 'content' => $message );

    // 调试日志：记录历史消息数量
    error_log( 'AIPhoto: [CHAT] history_count=' . count( $history ) . ', messages_count=' . count( $messages ) );

    $prompts = aiphoto_get_prompts();
    $chat_config = $prompts['chat'] ?? array();

    $body = array(
        'model'      => $chat_config['model'] ?? 'agnes-2.0-flash',
        'messages'   => $messages,
        'max_tokens' => $chat_config['max_tokens'] ?? 4096,
        'temperature'=> $chat_config['temperature'] ?? 0.7,
        'stream'     => true,
    );

    @ini_set('output_buffering', 'Off');
    @ini_set('zlib.output_compression', 'False');
    while (ob_get_level() > 0) { @ob_end_clean(); }

    header('Content-Type: text/event-stream');
    header('Cache-Control: no-cache');
    header('Connection: keep-alive');
    header('X-Accel-Buffering: no');

    $ch = curl_init($api_url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, wp_json_encode($body));
    curl_setopt($ch, CURLOPT_HTTPHEADER, array(
        'Authorization: Bearer ' . $settings['api_key'],
        'Content-Type: application/json',
        'Accept: text/event-stream',
    ));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, false);
    curl_setopt($ch, CURLOPT_TIMEOUT, 120);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

    $fullText = '';

    curl_setopt($ch, CURLOPT_WRITEFUNCTION, function($ch, $chunk) use (&$fullText) {
        $lines = explode("\n", $chunk);
        foreach ($lines as $line) {
            $line = trim($line);
            if (strpos($line, 'data: ') === 0) {
                $jsonStr = substr($line, 6);
                if ($jsonStr === '[DONE]') { continue; }
                $json = json_decode($jsonStr, true);
                if (isset($json['choices'][0]['delta']['content'])) {
                    $text = $json['choices'][0]['delta']['content'];
                    $fullText .= $text;
                    echo "data: " . wp_json_encode(array('text' => $text)) . "\n\n";
                    @flush();
                }
            }
        }
        return strlen($chunk);
    });

    curl_exec($ch);
    $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curlError = curl_error($ch);
    curl_close($ch);

    if ($httpCode !== 200 && empty($fullText)) {
        echo "data: " . wp_json_encode(array('error' => 'API请求失败(' . $httpCode . ')' . ($curlError ? ': '.$curlError : ''))) . "\n\n";
    }

    echo "data: [DONE]\n\n";
    @flush();
    exit;
}
add_action('wp_ajax_aiphoto_chat_stream', 'aiphoto_chat_stream');
add_action('wp_ajax_nopriv_aiphoto_chat_stream', 'aiphoto_chat_stream');

/**
 * AI 生成对话标题
 */
function aiphoto_generate_title() {
    if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( $_POST['nonce'], 'aiphoto_nonce' ) ) {
        wp_send_json_error( array( 'message' => '安全验证失败' ) );
    }

    $history = json_decode( sanitize_text_field( $_POST['history'] ?? '[]' ), true );
    if ( empty( $history ) ) {
        wp_send_json_success( array( 'title' => '新对话' ) );
        return;
    }

    $prompts = aiphoto_get_prompts();
    $title_cfg = $prompts['title'] ?? array();
    $settings = aiphoto_get_settings();

    if ( empty( $settings['api_key'] ) ) {
        wp_send_json_success( array( 'title' => '新对话' ) );
        return;
    }

    $api_url = rtrim( $settings['api_base_url'], '/' ) . '/v1/chat/completions';

    // 取前4轮对话作为上下文
    $context = '';
    foreach ( array_slice( $history, 0, 8 ) as $msg ) {
        $role = $msg['role'] === 'user' ? '用户' : 'AI';
        $context .= $role . ': ' . $msg['content'] . "\n";
    }

    $messages = array(
        array( 'role' => 'system', 'content' => $title_cfg['system_prompt'] ?? '根据对话内容生成简短标题' ),
        array( 'role' => 'user', 'content' => $context ),
    );

    $body = array(
        'model'       => $title_cfg['model'] ?? $settings['api_model'],
        'messages'    => $messages,
        'max_tokens'  => $title_cfg['max_tokens'] ?? 50,
        'temperature' => $title_cfg['temperature'] ?? 0.3,
        'stream'      => false,
    );

    $response = wp_remote_post( $api_url, array(
        'headers' => array(
            'Authorization' => 'Bearer ' . $settings['api_key'],
            'Content-Type'  => 'application/json',
        ),
        'body'    => wp_json_encode( $body ),
        'timeout' => 30,
    ) );

    if ( is_wp_error( $response ) ) {
        wp_send_json_success( array( 'title' => '新对话' ) );
        return;
    }

    $data = json_decode( wp_remote_retrieve_body( $response ), true );
    $title = trim( $data['choices'][0]['message']['content'] ?? '新对话' );
    $title = preg_replace( '/[""\']/', '', $title ); // 去除引号

    wp_send_json_success( array( 'title' => $title ) );
}
add_action('wp_ajax_aiphoto_generate_title', 'aiphoto_generate_title');
add_action('wp_ajax_nopriv_aiphoto_generate_title', 'aiphoto_generate_title');

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
