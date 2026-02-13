# 前言

　　最近想折腾一个相册页面，发现了photo page for typecho觉得不错，独立页面模板支持两种样式，但他使用的是静态资源全球jsDelivr加速。有时候就加载不出来，所以自己折腾一下,把CSS、js改到本地运行，同时增加图片懒加载。因为技术有限，可能还有很多不足之处。请大家指正！

# 声明

　　参考原代码：https://github.com/zzd/photo-page-for-typecho

# 介绍

　　typecho相册单页模板，单页模板非主题，可与主题共存！本项目仅需安装一个php文件，引入了两个相册主题Multiverse和Lens，可自行选择。

在线演示：

[https://ziyege.rf.gd/903.html](https://ziyege.rf.gd/903.html)

[https://blog.ziyege.com/photo.html](https://blog.ziyege.com/photo.html)

Multiverse风格

<img width="1885" height="920" alt="image" src="https://github.com/user-attachments/assets/0ee390fa-bd99-4da6-872a-926d14d39559" />

Lens风格

<img width="1856" height="918" alt="image" src="https://github.com/user-attachments/assets/cb5f6ab5-0928-4b18-959b-70401093e300" />

# 使用说明

### 安装

将文件夹内的文件上传至你所使用的主题根目录（可选两种模板），如默认主题路径`/usr/themes/default`，然后在typecho后台创建独立页面，将模板选择`Multiverse风格照片集`或`Lens风格照片集`即可。

### 调用格式

```
标题,简介,图片链接
```

多图以回车结束，每一行代表一张图片的信息，简介可以省略空白即可。

例如：

```
pic1,2020年01月01日拍摄,https://xxx.cn/large/xxx.jpg
pic2,2020年01月02日拍摄,https://xxx.cn/large/xxx.jpg
pic3,2020年01月03日拍摄,https://xxx.cn/large/xxx.jpg
```

### 自定义字段


[可选] `about`：控制指定位置的文本，可自定义关于等信息；

[可选] `CDN`：用以匹配你所使用的对象存储服务商，目前支持又拍云、阿里云OSS、七牛云、腾讯云，本字段目的在于使用云图像处理动态生成缩略图。对应填写内容为：`UPYUN`/`OSS`/`KODO`/`COS`；

[可选] 社交链接字段 `Twitter`, `Facebook`, `Instagram`, `GitHub`，给相应字段填入链接即可。
