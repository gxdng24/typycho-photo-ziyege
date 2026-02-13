
typecho单页模板，可与主题共存！基于HTML5UP开发设计的静态单页。
本项目仅需安装一个php文件，引入了两个相册主题Multiverse和Lens，可自行选择。

在线演示：

https://ziyege.rf.gd/903.html

https://blog.ziyege.com/photo.html

Multiverse风格

<img width="1885" height="920" alt="image" src="https://github.com/user-attachments/assets/0ee390fa-bd99-4da6-872a-926d14d39559" />

Lens风格

<img width="1856" height="918" alt="image" src="https://github.com/user-attachments/assets/cb5f6ab5-0928-4b18-959b-70401093e300" />

特性:
 本地运行
 单页模板，可与主题共存
 后台编辑简单
 两种可选样式
 版本更新     1.0. 初次推送

使用说明

将文件夹内的.php文件上传至你所使用的主题根目录（可选两种模板），如XX主题路径/usr/themes/XX，

然后在typecho后台创建独立页面，将模板选择Multiverse风格照片集或Lens风格照片集。

调用格式

标题,简介,图片链接

多图以回车结束，每一行代表一张图片的信息，简介可以省略。

例如：

图1,风景,https://ww2.sinaimg.cn/large/006uAlqKgy1fzlbjrxju2j31400u04qz.jpg

图2,人物,https://ww2.sinaimg.cn/large/006uAlqKgy1fzlbjrxju2j31400u04qz.jpg

图3,静物,https://ww2.sinaimg.cn/large/006uAlqKgy1fzlbjrxju2j31400u04qz.jpg

自定义字段
[可选] about：控制指定位置的文本，可自定义关于等信息；

[可选] CDN：用以匹配你所使用的对象存储服务商，目前支持又拍云、阿里云OSS、七牛云、腾讯云，本字段目的在于使用云图像处理动态生成缩略图。对应填写内容为：UPYUN/OSS/KODO/COS；

[可选] 社交链接字段 Twitter, Facebook, Instagram, GitHub，给相应字段填入链接即可。
