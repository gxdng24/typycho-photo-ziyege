<?php
/**
 * Lens风格照片集 - 自定义模板（本地运行修改版）1.0
 *
 * @author    Ziyege
 * @link          https://Ziyege.com
  * 本模板基于 HTML5 UP 的 Lens 主题二次开发，用于展示照片集。
 * 数据格式：文章内容每行一条记录，格式为：标题,描述,图片URL         描述可以空置
 * 自定义字段说明：
 *   - about    : 页面描述（显示在标题下方）
 *   - CDN      : 对象存储供应商（UPYUN/OSS/KODO/COS），用于生成裁剪缩略图
 *   - Twitter  : Twitter 主页链接
 *   - Facebook : Facebook 主页链接
 *   - Instagram: Instagram 主页链接
 *   - GitHub   : GitHub 主页链接
 */
if (!defined('__TYPECHO_ROOT_DIR__')) exit;
?>

<!--
  页面作者：ZhangDi
  GitHub: https://github.com/zzd/photo-page-for-typecho
  最后更新：2024-01-15（代码审查与格式化：2025-03-xx）
  注意：请保留版权信息，感谢原作者的贡献。
-->

<?php
/**
 * 根据对象存储供应商返回图片裁剪参数
 * 用于追加到原图URL后生成固定尺寸(640x400)的缩略图
 *
 * @param string $storage 供应商标识
 * @return string 裁剪参数（带前缀符号），若无匹配则返回空字符串
 */
function storage($storage)
{
    switch ($storage) {
        case 'UPYUN':
            return '!/fw/640/quality/85/clip/640x400a0a0/gravity/center';
        case 'OSS':
            return '?x-oss-process=image/crop,x_0,y_0,w_640,h_400,g_center';
        case 'KODO':
        case 'COS':
            return '?imageMogr2/gravity/center/crop/!640x400';
        default:
            return '';
    }
}
?>

<!DOCTYPE HTML>
<html>
<head>
    <title><?php $this->title() ?> - <?php $this->options->title() ?></title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1, user-scalable=no" />

    <!-- 核心样式：带文件修改时间戳用于缓存刷新 -->
    <link rel="stylesheet" href="<?php $this->options->themeUrl(); ?>/Lens/css/main.css" />
    <noscript>
        <link rel="stylesheet" href="<?php $this->options->themeUrl(); ?>/Lens/css/noscript.css" />
    </noscript>
    <!-- 此处可插入百度统计等第三方脚本 -->
</head>

<!--
  预加载状态类，由 main.js 控制逐步移除，
  实现页面元素的渐进式动画效果。
-->
<body class="is-preload-0 is-preload-1 is-preload-2">

    <!-- 主容器 -->
    <div id="main">

        <!-- 头部区域：标题、描述、社交链接 -->
        <header id="header">
            <h1><?php $this->title() ?></h1>
            <?php if ($this->fields->about): ?>
                <p><?php echo htmlspecialchars($this->fields->about); ?></p>
            <?php else: ?>
                <p>在自定义字段内添加 about 字段，即可编辑此内容。</p>
            <?php endif; ?>

            <!-- 社交图标：仅当对应自定义字段有值时显示 -->
            <ul class="icons">
                <?php if ($this->fields->Twitter): ?>
                    <li><a href="<?php echo htmlspecialchars($this->fields->Twitter); ?>" class="icon brands fa-twitter" target="_blank"><span class="label">Twitter</span></a></li>
                <?php endif; ?>
                <?php if ($this->fields->Facebook): ?>
                    <li><a href="<?php echo htmlspecialchars($this->fields->Facebook); ?>" class="icon brands fa-facebook-f" target="_blank"><span class="label">Facebook</span></a></li>
                <?php endif; ?>
                <?php if ($this->fields->Instagram): ?>
                    <li><a href="<?php echo htmlspecialchars($this->fields->Instagram); ?>" class="icon brands fa-instagram" target="_blank"><span class="label">Instagram</span></a></li>
                <?php endif; ?>
                <?php if ($this->fields->GitHub): ?>
                    <li><a href="<?php echo htmlspecialchars($this->fields->GitHub); ?>" class="icon brands fa-github" target="_blank"><span class="label">GitHub</span></a></li>
                <?php endif; ?>
            </ul>
        </header>

        <!-- 相册缩略图容器（由 JavaScript 动态填充） -->
        <section id="thumbnails"></section>

        <!-- 页脚：版权与技术支持 -->
        <footer id="footer">
            <ul class="copyright">
                <li>&copy; <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a></li>
                <li>Powered by <a href="https://blog.ziyege.com">ZIYEGE</a> Based HTML5UP.</li>
            </ul>
        </footer>
    </div>

    <!-- ===================================================================
         动态相册数据生成与渲染（核心逻辑）
         =================================================================== -->

    <script type="text/javascript">
    // --------------------------------------------------------------------
    // 1. 从 PHP 解析文章内容，转换为 JavaScript 对象数组
    // --------------------------------------------------------------------
    <?php
        // 获取当前文章的原始文本内容
        $rawContent = $this->text;
        $lines = explode("\n", trim($rawContent));
        $photoArray = [];

        foreach ($lines as $line) {
            $line = trim($line);
            if ($line === '') continue;

            // 以逗号分隔，最多分割3部分（标题、描述、URL）
            $parts = explode(',', $line, 3);
            if (count($parts) < 3) continue;

            $photoArray[] = [
                'title' => htmlspecialchars($parts[0], ENT_QUOTES, 'UTF-8'),
                'desc'  => htmlspecialchars($parts[1], ENT_QUOTES, 'UTF-8'),
                'url'   => filter_var(trim($parts[2]), FILTER_SANITIZE_URL)
            ];
        }
    ?>

    // 将 PHP 数组安全地转换为 JSON 字符串，并赋值给 JS 变量
    var photoList = <?php echo json_encode(
        $photoArray,
        JSON_UNESCAPED_SLASHES | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
    ); ?>;

    // --------------------------------------------------------------------
    // 2. 透明占位图（1x1 像素 GIF），用于懒加载初始显示
    // --------------------------------------------------------------------
    var PLACEHOLDER = 'data:image/gif;base64,R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7';

    // --------------------------------------------------------------------
    // 3. 动态构建相册 DOM 元素
    // --------------------------------------------------------------------
    function createArticle(photoList) {
        var container = document.getElementById('thumbnails');
        // 清空容器，避免重复渲染
        container.innerHTML = '';

        // 获取 CDN 裁剪参数（在 PHP 渲染时直接输出字符串）
        var cdnSuffix = '<?php echo storage($this->fields->CDN); ?>';

        for (var i = 0; i < photoList.length; i++) {
            var photo = photoList[i];

            // <article class="thumb"> 外层包裹
            var article = document.createElement('article');
            article.className = 'thumb';
            container.appendChild(article);

            // <a class="thumbnail" href="原图URL">
            var a = document.createElement('a');
            a.className = 'thumbnail';
            a.href = photo.url;          // 点击后查看原图
            article.appendChild(a);

            // <img> 使用懒加载：src 为占位图，真实地址放在 data-src 中
            var img = document.createElement('img');
            img.src = PLACEHOLDER;
            img.dataset.src = photo.url + cdnSuffix;   // 拼接裁剪参数
            img.className = 'lazyload';
            a.appendChild(img);

            // 标题 <h2>
            var h2 = document.createElement('h2');
            h2.textContent = photo.title;   // 纯文本，防止 XSS
            article.appendChild(h2);

            // 描述 <p>
            var p = document.createElement('p');
            p.textContent = photo.desc;
            article.appendChild(p);
        }
    }

    // --------------------------------------------------------------------
    // 4. 执行渲染（如果存在数据）
    // --------------------------------------------------------------------
    if (photoList.length > 0) {
        createArticle(photoList);
    } else {
        // 没有图片数据时显示提示信息（可选）
        console.warn('当前页面未包含任何相册数据，请按照“标题,描述,URL”格式在文章内容中添加。');
    }

    // --------------------------------------------------------------------
    // 5. 懒加载初始化（使用 Intersection Observer）
    // --------------------------------------------------------------------
    function initLazyLoad() {
        var lazyImages = [].slice.call(document.querySelectorAll('img.lazyload'));

        if ('IntersectionObserver' in window) {
            var lazyImageObserver = new IntersectionObserver(function(entries, observer) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        var lazyImage = entry.target;
                        lazyImage.src = lazyImage.dataset.src;   // 替换真实地址
                        lazyImage.classList.remove('lazyload');
                        lazyImageObserver.unobserve(lazyImage);
                    }
                });
            });

            lazyImages.forEach(function(lazyImage) {
                lazyImageObserver.observe(lazyImage);
            });
        } else {
            // 降级方案：不支持 IntersectionObserver 时直接加载所有图片
            lazyImages.forEach(function(lazyImage) {
                lazyImage.src = lazyImage.dataset.src;
                lazyImage.classList.remove('lazyload');
            });
        }
    }

    // --------------------------------------------------------------------
    // 6. 启动懒加载（在 DOM 结构生成后调用）
    // --------------------------------------------------------------------
    initLazyLoad();
    </script>

    <!--
        模板依赖的 JavaScript 库：
        - jquery.min.js   : 基础DOM操作（由main.js调用）
        - browser.min.js  : 浏览器特性检测
        - breakpoints.min.js : 响应式断点处理
        - main.js         : 主题核心交互（全屏、画廊动画等）
        每个文件均附加文件修改时间戳，避免缓存问题。
    -->
    <script src="<?php $this->options->themeUrl(); ?>/Lens/js/jquery.min.js></script>
    <script src="<?php $this->options->themeUrl(); ?>/Lens/js/browser.min.js>"></script>
    <script src="<?php $this->options->themeUrl(); ?>/Lens/js/breakpoints.min.js>"></script>
    <script src="<?php $this->options->themeUrl(); ?>/Lens/js/main.js>"></script>
</body>
</html>
