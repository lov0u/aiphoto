<?php elseif ( 'generate' === $page ) : ?>



<section class="gen-page" id="gen-page">

    <div class="container" style="padding-top: 20px;">

        <div class="gen-layout">

            <div class="gen-panel gen-panel--editor">

                <div class="gen-panel-header">

                    <h1>生成图片</h1>

                    <p class="gen-panel-desc">

                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" style="vertical-align: -2px;"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>

                        请勿生成侵犯他人隐私的照片，生成的图片将保存至服务器

                    </p>

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

                        <textarea id="generatorPrompt" class="gen-textarea" rows="2"

                                  placeholder="描述你想生成的图片，例如：夕阳下的宁静湖面..."

                                  required maxlength="500"></textarea>

                    </div>



                    <!-- 快捷选项 -->

                    <div class="gen-template-row" style="margin-bottom:8px;">

                        <label class="gen-label">快捷选项</label>

                        <div class="gen-template-tags" id="templateTags" style="display:flex;flex-wrap:wrap;gap:6px;margin-top:4px;">

                            <span class="gen-template-tag" data-template="beauty" style="cursor:pointer;padding:4px 10px;border-radius:12px;background:#f0f0f0;font-size:12px;transition:all .2s;">美颜</span>

                            <span class="gen-template-tag" data-template="soft_light" style="cursor:pointer;padding:4px 10px;border-radius:12px;background:#f0f0f0;font-size:12px;transition:all .2s;">柔光</span>

                            <span class="gen-template-tag" data-template="hd" style="cursor:pointer;padding:4px 10px;border-radius:12px;background:#f0f0f0;font-size:12px;transition:all .2s;">高清</span>

                            <span class="gen-template-tag" data-template="film" style="cursor:pointer;padding:4px 10px;border-radius:12px;background:#f0f0f0;font-size:12px;transition:all .2s;">胶片感</span>

                            <span class="gen-template-tag" data-template="magazine" style="cursor:pointer;padding:4px 10px;border-radius:12px;background:#f0f0f0;font-size:12px;transition:all .2s;">杂志风</span>

                            <span class="gen-template-tag" data-template="dreamy" style="cursor:pointer;padding:4px 10px;border-radius:12px;background:#f0f0f0;font-size:12px;transition:all .2s;">梦幻</span>

                        </div>

                    </div>



                    <div class="gen-btn-row">

                        <button type="button" class="gen-mode-btn gen-mode-btn--active" id="txt2imgBtn">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M4 7V4h16v3"/><path d="M9 20h6"/><path d="M12 4v16"/></svg>

                            文生图

                        </button>

                        <button type="button" class="gen-mode-btn" id="img2imgToggle">

                            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>

                            图生图

                        </button>

                    </div>



                    <div class="gen-img2img-area" id="img2imgArea" style="display:none;">

                        <div class="gen-img2img-content">

                            <div id="img2imgPreview" class="gen-img2img-preview"></div>

                            <input type="file" id="img2imgInput" accept="image/*" multiple style="display:none;">

                            <button type="button" class="gen-select-file-btn" id="img2imgUploadBtn">

                                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="16" height="16"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>

                                选择参考图片（可选，最多 4 张）

                            </button>

                        </div>

                    </div>



                    <div class="gen-options-row">

                        <div class="gen-option">

                            <label for="generatorEffect" class="gen-label">视觉特效</label>

                            <select id="generatorEffect" class="gen-select">

                                <option value="cinematic" selected>电影级</option>

                                <option value="pixel-art">像素风</option>

                                <option value="cartoon">卡通</option>

                                <option value="3d-render">3D 渲染</option>

                                <option value="watercolor">水彩</option>

                                <option value="oil-painting">油画</option>

                                <option value="anime">动漫</option>

                                <option value="photorealistic">照片级写实</option>

                                <option value="cyberpunk">赛博朋克</option>

                                <option value="fantasy">奇幻</option>

                            </select>

                        </div>

                        <div class="gen-option">

                            <label for="generatorLens" class="gen-label">镜头角度</label>

                            <select id="generatorLens" class="gen-select">

                                <option value="wide-angle">广角镜头</option>

                                <option value="macro">微距镜头</option>

                                <option value="birdseye">鸟瞰视角</option>

                                <option value="eye-level">平视视角</option>

                                <option value="low-angle">仰视视角</option>

                                <option value="close-up" selected>特写</option>

                                <option value="portrait">人像镜头</option>

                                <option value="panoramic">全景</option>

                            </select>

                        </div>

                        <div class="gen-option">

                            <label for="generatorSize" class="gen-label">清晰度</label>

                            <select id="generatorSize" class="gen-select">

                                <option value="1K">1K (1024×1024)</option>

                                <option value="2K" selected>2K (2048×2048)</option>

                                <option value="3K">3K (3072×3072)</option>

                                <option value="4K">4K (4096×4096)</option>

                            </select>

                        </div>

                        <div class="gen-option">

                            <label for="generatorRatio" class="gen-label">比例</label>

                            <select id="generatorRatio" class="gen-select">

                                <option value="16:9" selected>16:9 宽屏</option>

                                <option value="1:1">1:1 正方形</option>

                                <option value="9:16">9:16 竖屏</option>

                                <option value="4:3">4:3 横版</option>

                                <option value="3:4">3:4 竖版</option>

                                <option value="3:2">3:2</option>

                                <option value="2:3">2:3</option>

                                <option value="21:9">21:9 超宽</option>

                            </select>

                        </div>

                    </div>



                </form>



                    <!-- 进度框 + 开始/停止按钮（始终显示，在表单外面） -->

                    <div id="genProgressWrap" style="margin-top:8px;display:flex;align-items:stretch;gap:8px;">

                        <div id="genProgressBox" style="flex:1;border:1px solid #e0e0e0;border-radius:8px;padding:8px 12px;background:#fafafa;height:66px;overflow-y:auto;display:flex;align-items:center;justify-content:center;">

                            <div id="genWelcomeMsg" style="font-size:14px;font-weight:600;color:#8b5cf6;">输入描述，点击开始生成图片</div>

                        </div>

                        <button type="button" id="genStartBtn" style="flex-shrink:0;padding:0 24px;background:#ef4444;color:#fff;border:none;border-radius:8px;font-size:14px;font-weight:600;cursor:pointer;white-space:nowrap;">开始</button>

                    </div>



                <div class="gen-result" id="generatorResult">

                    <div class="gen-result-header"><h3>生成结果</h3></div>

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



            <div class="gen-panel gen-panel--recent">

                <div class="gen-panel-header">

                    <h2>最近生成</h2>

                    <a href="https://aiphoto.ra0.cn/gallery" class="gen-view-all">查看全部</a>

                </div>

                <div class="gen-recent-grid" id="recentGrid">

                    <?php

                    $rg = new WP_Query( array(

                        'post_type'      => 'attachment',

                        'post_status'    => 'inherit',

                        'post_mime_type' => 'image',

                        'posts_per_page' => 6,

                        'orderby'        => 'date',

                        'order'          => 'DESC',

                        'fields'         => 'ids',

                    ) );

                    if ( $rg->have_posts() ) :

                        foreach ( $rg->posts as $aid ) :

                            $tu = wp_get_attachment_image_src( $aid, 'medium' );

                            $fu = wp_get_attachment_image_src( $aid, 'full' );

                            $tt = get_the_title( $aid );

                            ?>

                            <?php $user_prompt = get_post_meta( $aid, '_aiphoto_user_prompt', true ) ?: get_post_meta( $aid, '_aiphoto_prompt', true ); ?>

                            <div class="gen-recent-item" data-full="<?php echo esc_url( $fu[0] ); ?>" data-title="<?php echo esc_attr( $tt ); ?>" data-original-prompt="<?php echo esc_attr( $user_prompt ); ?>">

                                <?php if ( $tu ) : ?>

                                    <img src="<?php echo esc_url( $tu[0] ); ?>" alt="<?php echo esc_attr( $tt ); ?>" loading="lazy">

                                <?php endif; ?>

                            </div>

                        <?php endforeach;

                    else : ?>

                        <div class="gen-empty-recent"><p>还没有生成图片</p></div>

                    <?php endif; wp_reset_postdata(); ?>

                </div>

            </div>

        </div>

    </div>

</section>



<!-- ============================================

     GALLERY - 画廊页面

     ============================================ -->

<?php elseif ( 'gallery' === $page ) : ?>
