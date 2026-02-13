<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php
/**
 * Multiverse风格照片集-自定义模板（本地运行修改版）1.0（本地修改版）
 * @author    Ziyege
 * @link          https://Ziyege.com
  * 本模板基于 HTML5 UP 的 Lens 主题二次开发，用于展示照片集。
 * 数据格式：文章内容每行一条记录，格式为：标题,描述,图片URL         描述可以空置
 */
?>
<!DOCTYPE HTML>
<html>
<head>
    <title><?php $this->title(); ?> - <?php $this->options->title(); ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />
    <!-- 主题核心样式（必须） -->
    <link rel="stylesheet" href="<?php $this->options->themeUrl(); ?>/Multiverse/css/main.css" />
    <noscript>
        <link rel="stylesheet" href="<?php $this->options->themeUrl(); ?>/Multiverse/css/noscript.css" />
    </noscript>
    <!-- ✨ 极简占位灰底：图片加载前显示浅灰色，避免白屏抖动 -->
    <style>
        .thumb .image img {
            background-color: #f0f0f0;  /* 浅灰占位 */
            display: block;
            width: 100%;
            height: auto;
            aspect-ratio: 4 / 3;       /* 固定比例，布局稳定 */
            object-fit: cover;
        }
    </style>
</head>
<body class="is-preload">

<?php
// ========== 1. 解析图片数据 ==========
$raw = $this->row['text'];
$lines = preg_split('/\r\n|\r|\n/', $raw);
$images = [];
foreach ($lines as $line) {
    $line = trim($line);
    if ($line === '') continue;
    $parts = explode(',', $line);
    if (count($parts) >= 3) {
        $images[] = [
            'title' => trim($parts[0]),
            'desc'  => trim($parts[1]),
            'url'   => trim($parts[2])
        ];
    }
}

// ========== 2. CDN缩略图后缀 ==========
$thumbSuffix = '';
if (!empty($this->fields->CDN)) {
    switch (strtoupper($this->fields->CDN)) {
        case 'UPYUN': $thumbSuffix = '!/fw/640/quality/85'; break;
        case 'OSS':   $thumbSuffix = '?x-oss-process=image/resize,w_640/quality,q_85'; break;
        case 'KODO':
        case 'COS':   $thumbSuffix = '?imageView2/2/w/640/q/85'; break;
    }
}
?>

<!-- Wrapper -->
<div id="wrapper">
    <header id="header">
        <h1><a href="<?php $this->permalink(); ?>"><strong><?php $this->title(); ?></strong> Powered by ZDSR</a></h1>
        <nav><ul><li><a href="#footer" class="icon solid fa-info-circle">关于</a></li></ul></nav>
    </header>

    <!-- Main 照片展示区 -->
    <div id="main"></div>

    <!-- Footer 页脚（完整保留） -->
    <footer id="footer" class="panel">
        <!-- ... 与之前完全相同的 footer 代码，此处省略节省篇幅，实际使用时请完整保留 ... -->
        <div class="inner split">
            <div>
                <section>
                    <h2>控制台</h2>
                    <p>本系统共有<span id="count_CN"></span>张图片，前端样式Multiverse由HTML5UP设计。</p>
                    <h2>Console</h2>
                    <p>The system has a total of <span id="count_EN"></span> pictures, style Multiverse is designed by HTML5UP.</p>
                </section>
                <section>
                    <ul class="icons">
                        <?php if ($this->fields->Twitter): ?>
                        <li><a href="<?php echo htmlspecialchars($this->fields->Twitter, ENT_QUOTES, 'UTF-8'); ?>" class="icon brands fa-twitter" target="_blank" rel="noopener noreferrer"><span class="label">Twitter</span></a></li>
                        <?php endif; ?>
                        <?php if ($this->fields->Facebook): ?>
                        <li><a href="<?php echo htmlspecialchars($this->fields->Facebook, ENT_QUOTES, 'UTF-8'); ?>" class="icon brands fa-facebook-f" target="_blank" rel="noopener noreferrer"><span class="label">Facebook</span></a></li>
                        <?php endif; ?>
                        <?php if ($this->fields->Instagram): ?>
                        <li><a href="<?php echo htmlspecialchars($this->fields->Instagram, ENT_QUOTES, 'UTF-8'); ?>" class="icon brands fa-instagram" target="_blank" rel="noopener noreferrer"><span class="label">Instagram</span></a></li>
                        <?php endif; ?>
                        <?php if ($this->fields->GitHub): ?>
                        <li><a href="<?php echo htmlspecialchars($this->fields->GitHub, ENT_QUOTES, 'UTF-8'); ?>" class="icon brands fa-github" target="_blank" rel="noopener noreferrer"><span class="label">GitHub</span></a></li>
                        <?php endif; ?>
                    </ul>
                </section>
                <p class="copyright">
                    &copy; <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>
                    Powered by <a href="https://github.com/zzd/photo-page-for-typecho">ZDSR</a> Based on HTML5UP.
                </p>
            </div>
            <div>
                <section>
                    <h2>关于 <?php $this->title(); ?></h2>
                    <?php if ($this->fields->about): ?>
                    <p><?php echo htmlspecialchars($this->fields->about, ENT_QUOTES, 'UTF-8'); ?></p>
                    <?php else: ?>
                    <p>在自定义字段内添加 about 字段，即可编辑此内容。</p>
                    <?php endif; ?>
                </section>
            </div>
        </div>
    </footer>
</div>

<!-- ========== 3. 极简渲染：直接设置 src + 原生 loading="lazy" ========== -->
<script>
// 图片数据
var imageData = <?php echo json_encode($images, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;
var thumbSuffix = <?php echo json_encode($thumbSuffix, JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT); ?>;

/**
 * 渲染相册 - 直接显示图片，绝无不显示问题
 */
function renderGallery(images) {
    var container = document.getElementById('main');
    if (!container) return;

    // 清空容器（确保干净）
    container.innerHTML = '';

    images.forEach(function(item) {
        var article = document.createElement('article');
        article.className = 'thumb';

        var a = document.createElement('a');
        a.className = 'image';
        a.href = item.url; // 原图链接（poptrox使用）

        var img = document.createElement('img');
        // ✅ 直接设置 src，图片立即开始加载（一定会显示）
        img.src = item.url + thumbSuffix;
        // ✅ 添加原生懒加载属性，现代浏览器会自动延迟屏幕外图片
        img.loading = 'lazy';

        a.appendChild(img);
        article.appendChild(a);

        var h2 = document.createElement('h2');
        h2.textContent = item.title;
        article.appendChild(h2);

        var p = document.createElement('p');
        p.textContent = item.desc;
        article.appendChild(p);

        container.appendChild(article);
    });

    // 更新图片计数
    var countCN = document.getElementById('count_CN');
    var countEN = document.getElementById('count_EN');
    if (countCN) countCN.textContent = images.length;
    if (countEN) countEN.textContent = images.length;
}

// 立即执行
renderGallery(imageData);
</script>

<!-- 主题脚本（必须放在最后，不影响图片显示） -->
<script src="<?php $this->options->themeUrl(); ?>/Multiverse/js/jquery.min.js"></script>
<script src="<?php $this->options->themeUrl(); ?>/Multiverse/js/jquery.poptrox.min.js"></script>
<script src="<?php $this->options->themeUrl(); ?>/Multiverse/js/browser.min.js"></script>
<script src="<?php $this->options->themeUrl(); ?>/Multiverse/js/breakpoints.min.js"></script>
<script src="<?php $this->options->themeUrl(); ?>/Multiverse/js/util.js"></script>
<script src="<?php $this->options->themeUrl(); ?>/Multiverse/js/main.js"></script>
</body>
</html>