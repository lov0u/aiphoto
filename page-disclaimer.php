<?php
/**
 * Template Name: 免责协议
 * Description: AIPhoto 免责声明与使用条款
 */

get_header(); ?>

<style>
.disclaimer-page {
    max-width: 800px;
    margin: 0 auto;
    padding: 100px 20px 80px;
}
.disclaimer-page h1 {
    font-size: 2rem;
    font-weight: 700;
    margin-bottom: 8px;
    color: #0f172a;
    text-align: center;
}
.disclaimer-page .disclaimer-update {
    color: #64748b;
    font-size: 0.875rem;
    margin-bottom: 32px;
}
.disclaimer-page h2 {
    font-size: 1.25rem;
    font-weight: 600;
    color: #0f172a;
    margin: 32px 0 12px;
    padding-bottom: 8px;
    border-bottom: 1px solid #e2e8f0;
}
.disclaimer-page p,
.disclaimer-page li {
    color: #475569;
    font-size: 0.9375rem;
    line-height: 1.8;
}
.disclaimer-page p {
    margin-bottom: 12px;
}
.disclaimer-page ul {
    margin: 8px 0 16px 20px;
}
.disclaimer-page li {
    margin-bottom: 6px;
}
.disclaimer-page .disclaimer-box {
    background: #fef3c7;
    border: 1px solid #fde68a;
    border-radius: 8px;
    padding: 16px 20px;
    margin: 20px 0;
}
.disclaimer-page .disclaimer-box p {
    color: #92400e;
    margin: 0;
    font-size: 0.875rem;
}
.disclaimer-page a {
    color: #7c3aed;
    text-decoration: none;
}
.disclaimer-page a:hover {
    text-decoration: underline;
}
</style>

<section class="disclaimer-page">
    <h1>免责协议与使用条款</h1>
    <p class="disclaimer-update">最后更新：<?php echo date( 'Y 年 m 月 d 日' ); ?></p>

    <div class="disclaimer-box">
        <p><strong>重要提示：</strong>请在使用本平台前仔细阅读以下条款。使用本平台即表示您同意遵守本协议的所有条款。</p>
    </div>

    <h2>一、服务说明</h2>
    <p>Aiphoto（以下简称"本平台"）是一个基于人工智能技术的图片生成与展示平台。用户可以通过输入文字描述（提示词）或上传参考图片，由 AI 模型生成相应的图片内容。</p>
    <p>本平台提供的 AI 图片生成功能基于第三方 AI 模型（如 DALL-E、Stable Diffusion 等），生成结果由 AI 算法自动产出，本平台无法完全控制生成内容。</p>

    <h2>二、用户行为规范</h2>
    <p>用户在使用本平台时，应遵守以下规范：</p>
    <ul>
        <li><strong>禁止生成侵犯他人隐私的内容</strong>：不得使用真实人物的姓名、肖像或个人信息作为提示词生成图片</li>
        <li><strong>禁止生成违法内容</strong>：不得生成违反国家法律法规、危害国家安全、传播暴力、色情、歧视等违法或不良信息</li>
        <li><strong>禁止侵权行为</strong>：不得利用本平台生成侵犯他人著作权、商标权、肖像权等知识产权的内容</li>
        <li><strong>禁止恶意使用</strong>：不得利用本平台进行批量生成、爬取数据、干扰服务等恶意行为</li>
    </ul>

    <h2>三、内容责任</h2>
    <p><strong>AI 生成内容的不确定性：</strong></p>
    <ul>
        <li>AI 模型生成的图片可能包含错误、不准确或不恰当的内容</li>
        <li>生成的图片可能与用户的预期存在差异</li>
        <li>用户应对 AI 生成内容进行自行判断和审核</li>
    </ul>

    <div class="disclaimer-box">
        <p><strong>免责声明：</strong>本平台不对 AI 生成内容的准确性、合法性、安全性作出任何保证。用户使用 AI 生成内容所产生的一切法律责任和后果，由用户自行承担。本平台不承担因用户使用生成内容而导致的任何直接或间接损失。</p>
    </div>

    <h2>四、知识产权</h2>
    <ul>
        <li>本平台的界面设计、代码、logo 等知识产权归本平台所有</li>
        <li>AI 生成的图片版权归属遵循相关 AI 模型的服务条款</li>
        <li>用户上传的参考图片版权归原作者所有</li>
        <li>用户不得将 AI 生成的图片用于商业用途，除非获得相关权利人的明确授权</li>
    </ul>

    <h2>五、数据与隐私</h2>
    <ul>
        <li>用户输入的提示词和上传的参考图片将被记录用于生成图片</li>
        <li>AI 生成的图片将保存在服务器媒体库中</li>
        <li>本平台不会主动向第三方披露用户的个人信息</li>
        <li>用户应自行备份重要数据，本平台不对数据丢失承担责任</li>
    </ul>

    <h2>六、服务变更与终止</h2>
    <p>本平台保留随时修改、暂停或终止服务的权利，恕不另行通知。因服务变更或终止导致的任何损失，本平台不承担责任。</p>

    <h2>七、年龄限制</h2>
    <p>本平台面向 18 周岁以上的用户。未满 18 周岁的用户请在监护人指导下使用本平台。</p>

    <h2>八、协议修改</h2>
    <p>本平台有权根据需要修改本协议条款，修改后的协议将在本页面发布。继续使用本平台即表示您接受修改后的协议条款。</p>

    <h2>九、联系方式</h2>
    <p>如对本协议有任何疑问，请通过以下方式联系我们：</p>
    <p>邮箱：<?php echo esc_html( get_option( 'admin_email' ) ); ?></p>
</section>

<?php get_footer(); ?>
