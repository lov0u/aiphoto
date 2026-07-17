<?php
/**
 * 模板名称: 图片生成
 * 描述: AI 图片生成页面
 */

get_header();

$settings = aiphoto_get_settings();
?>

<section class="gen-page" id="gen-page">
    <div class="container">
        <div class="gen-layout">
            <!-- 左侧：生成器 -->
            <div class="gen-panel gen-panel--editor">
                <div class="gen-panel-header">
                    <h1>生成图片</h1>
                    <p class="gen-panel-desc">输入描述文字，AI 即刻为你生成高质量图片</p>
                </div>

                <?php if ( empty( $settings['api_key'] ) ) : ?>
                <div class="api-warning" role="alert">
                    <?php
                    printf(
                        esc_html__( 'API 尚未配置。请%s开始使用。', 'aiphoto' ),
                        '<a href="' . esc_url( admin_url( 'admin.php?page=aiphoto-settings' ) ) . '">' . esc_html__( '前往设置', 'aiphoto' ) . '</a>'
                    );
                    ?>
                </div>
                <?php endif; ?>

                <form class="gen-form" id="generatorForm" novalidate>
                    <div class="gen-input-group">
                        <label for="generatorPrompt" class="gen-label">提示词</label>
                        <textarea id="generatorPrompt" class="gen-textarea" rows="3"
                                  placeholder="描述你想生成的图片，例如：夕阳下的宁静湖面，群山倒映在水中，金色阳光洒满水面..."
                                  required maxlength="500"></textarea>
                    </div>

                    <div class="gen-options">
                        <div class="gen-option">
                            <label for="generatorSize" class="gen-label">尺寸</label>
                            <select id="generatorSize" class="gen-select">
                                <option value="1K">1K (1024×1024)</option>
                                <option value="2K">2K (2048×2048)</option>
                                <option value="3K">3K (3072×3072)</option>
                                <option value="4K">4K (4096×4096)</option>
                                <option value="1024x768">1024 × 768 (4:3)</option>
                                <option value="1024x1792">1024 × 1792 (9:16)</option>
                                <option value="1792x1024">1792 × 1024 (16:9)</option>
                            </select>
                        </div>
                        <div class="gen-option">
                            <label for="generatorRatio" class="gen-label">比例</label>
                            <select id="generatorRatio" class="gen-select">
                                <option value="1:1">1:1 正方形</option>
                                <option value="3:4">3:4 竖版</option>
                                <option value="4:3">4:3 横版</option>
                                <option value="16:9">16:9 宽屏</option>
                                <option value="9:16">9:16 竖屏</option>
                                <option value="2:3">2:3</option>
                                <option value="3:2">3:2</option>
                                <option value="21:9">21:9 超宽</option>
                            </select>
                        </div>
                    </div>

                    <!-- 图生图 -->
                    <div class="gen-img2img-toggle">
                        <button type="button" class="gen-toggle-btn" id="img2imgToggle">
                            🖼 图生图
                        </button>
                        <div class="gen-img2img-area" id="img2imgArea" style="display:none;">
                            <p class="gen-help-text">上传参考图片（可选，最多 4 张）：</p>
                            <div id="img2imgPreview"></div>
                            <input type="file" id="img2imgInput" accept="image/*" multiple style="display:none;">
                            <button type="button" class="gen-select-file-btn" id="img2imgUploadBtn">选择图片</button>
                        </div>
                    </div>

                    <button type="submit" class="gen-submit-btn" id="generateBtn" disabled>
                        <span id="btnText">生成图片</span>
                        <span id="btnSpinner" class="spinner" style="display:none;"></span>
                    </button>
                </form>

                <!-- 结果 -->
                <div class="gen-result" id="generatorResult">
                    <div class="gen-result-header">
                        <h3>生成结果</h3>
                    </div>
                    <div class="gen-result-image">
                        <img id="resultImage" src="" alt="">
                    </div>
                    <div class="gen-result-actions">
                        <button class="gen-action-btn" id="downloadBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            下载原图
                        </button>
                        <button class="gen-action-btn" id="copyPromptBtn">
                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                            复制提示词
                        </button>
                    </div>
                    <div class="status-message error" id="errorMessage" style="display:none;" role="alert"></div>
                </div>
            </div>

            <!-- 右侧：最近生成 -->
            <div class="gen-panel gen-panel--recent">
                <div class="gen-panel-header">
                    <h2>最近生成</h2>
                    <a href="<?php echo esc_url( get_permalink( get_page_by_path( 'gallery' ) ) ?: home_url( '/gallery/' ) ); ?>" class="gen-view-all">查看全部</a>
                </div>
                <div class="gen-recent-grid" id="recentGrid">
                    <?php
                    $recent = new WP_Query( array(
                        'post_type'      => 'attachment',
                        'post_status'    => 'inherit',
                        'post_mime_type' => 'image',
                        'posts_per_page' => 6,
                        'orderby'        => 'date',
                        'order'          => 'DESC',
                        'fields'         => 'ids',
                    ) );
                    if ( $recent->have_posts() ) :
                        foreach ( $recent->posts as $attach_id ) :
                            $thumb = wp_get_attachment_image_src( $attach_id, 'medium' );
                            $full  = wp_get_attachment_image_src( $attach_id, 'full' );
                            $title = get_the_title( $attach_id );
                            ?>
                            <?php $user_prompt = get_post_meta( $attach_id, '_aiphoto_user_prompt', true ) ?: get_post_meta( $attach_id, '_aiphoto_prompt', true ); ?>
                            <div class="gen-recent-item" data-full="<?php echo esc_url( $full[0] ); ?>" data-title="<?php echo esc_attr( $title ); ?>" data-original-prompt="<?php echo esc_attr( $user_prompt ); ?>">
                                <?php if ( $thumb ) : ?>
                                    <img src="<?php echo esc_url( $thumb[0] ); ?>" alt="<?php echo esc_attr( $title ); ?>" loading="lazy">
                                <?php endif; ?>
                            </div>
                        <?php endforeach;
                    else : ?>
                        <div class="gen-empty-recent">
                            <p>还没有生成图片</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<?php get_footer(); ?>
