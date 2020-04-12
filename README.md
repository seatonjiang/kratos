<p align="center">
<img src="https://raw.githubusercontent.com/Vtrois/Kratos/master/inc/options-framework/images/about.png">
</p>

<p align="center">
<img src="https://img.shields.io/badge/php-%3E%3D7.0.0-blue">
<img src="https://img.shields.io/badge/wordpress-v5.4.0%20tested-%234c1">
<a href="https://vtrois.crowdin.com/kratos" target="_blank"><img src="https://badges.crowdin.net/e/f1d1a7eaa6af337dba7aa4a39b28e67c/localized.svg"></a>
<a href="https://www.jsdelivr.com/package/gh/vtrois/kratos" target="_blank"><img src="https://data.jsdelivr.com/v1/package/gh/vtrois/kratos/badge?style=rounded"></a>
<img src="https://img.shields.io/github/license/Vtrois/Kratos?color=%234c1">
</p>

## About

Kratos is a responsive WordPress theme focused on the user reading experience, just for fun 🎉

## Install

1. First download the theme's .zip file from the source file to your local computer.

2. From your WordPress Administration area, head to Appearance > Themes and click `Add New`.

3. The Add New theme screen has a new option, `Upload Theme`.

4. The theme upload form is now open, click `Choose File`, select the theme zip file on your computer and click `Install Now`.

5. The theme can now be activated from the administrator. Select the `Appearance` tab, then open the theme catalog, find the theme, and click the `Activate link`.

## Structure
Within the download you'll find the following directories and files. You'll see something like this 👇

```
Kratos
├── 404.php
├── LICENSE
├── README.md
├── assets
│   ├── css
│   │   ├── bootstrap.min.css
│   │   ├── iconfont.min.css
│   │   ├── kratos.css
│   │   ├── kratos.min.css
│   │   ├── layer.min.css
│   │   └── widget.min.css
│   ├── fonts
│   │   ├── iconfont.eot
│   │   ├── iconfont.svg
│   │   ├── iconfont.ttf
│   │   ├── iconfont.woff
│   │   └── iconfont.woff2
│   ├── img
│   │   ├── 404.jpg
│   │   ├── 404.svg
│   │   ├── about-background.png
│   │   ├── ad.png
│   │   ├── background.png
│   │   ├── default.jpg
│   │   ├── donate.png
│   │   ├── gravatar.png
│   │   ├── layer
│   │   │   ├── icon-ext.png
│   │   │   └── icon.png
│   │   ├── nothing.svg
│   │   ├── payment
│   │   │   ├── alipay.png
│   │   │   └── wechat.png
│   │   ├── police-ico.png
│   │   ├── smilies(has some emoji pic)
│   │   └── wechat.png
│   └── js
│       ├── bootstrap.min.js
│       ├── buttons
│       │   ├── images(has some button pic)
│       │   └── more.js
│       ├── comments.min.js
│       ├── jquery.min.js
│       ├── kratos.js
│       ├── kratos.min.js
│       ├── layer.min.js
│       └── widget.min.js
├── comments.php
├── custom
│   ├── custom.css
│   ├── custom.js
│   └── custom.php
├── footer.php
├── functions.php
├── header.php
├── inc
│   ├── options-framework
│   │   ├── autoload.php
│   │   ├── css
│   │   │   └── optionsframework.css
│   │   ├── images(has some options pic)
│   │   ├── includes
│   │   │   ├── class-options-framework-admin.php
│   │   │   ├── class-options-framework.php
│   │   │   ├── class-options-interface.php
│   │   │   ├── class-options-media-uploader.php
│   │   │   └── class-options-sanitization.php
│   │   └── js
│   │       ├── media-uploader.js
│   │       └── options-custom.js
│   ├── theme-article.php
│   ├── theme-core.php
│   ├── theme-navwalker.php
│   ├── theme-options.php
│   ├── theme-setting.php
│   ├── theme-shortcode.php
│   ├── theme-smtp.php
│   ├── theme-widgets.php
│   └── update-checker
│       ├── Puc
│       │   ├── v4
│       │   │   └── Factory.php
│       │   └── v4p9
│       │       ├── Autoloader.php
│       │       ├── DebugBar
│       │       │   ├── Extension.php
│       │       │   ├── Panel.php
│       │       │   ├── PluginExtension.php
│       │       │   ├── PluginPanel.php
│       │       │   └── ThemePanel.php
│       │       ├── Factory.php
│       │       ├── InstalledPackage.php
│       │       ├── Metadata.php
│       │       ├── OAuthSignature.php
│       │       ├── Plugin
│       │       │   ├── Info.php
│       │       │   ├── Package.php
│       │       │   ├── Ui.php
│       │       │   ├── Update.php
│       │       │   └── UpdateChecker.php
│       │       ├── Scheduler.php
│       │       ├── StateStore.php
│       │       ├── Theme
│       │       │   ├── Package.php
│       │       │   ├── Update.php
│       │       │   └── UpdateChecker.php
│       │       ├── Update.php
│       │       ├── UpdateChecker.php
│       │       ├── UpgraderStatus.php
│       │       ├── Utils.php
│       │       └── Vcs
│       │           ├── Api.php
│       │           ├── BaseChecker.php
│       │           ├── BitBucketApi.php
│       │           ├── GitHubApi.php
│       │           ├── GitLabApi.php
│       │           ├── PluginUpdateChecker.php
│       │           ├── Reference.php
│       │           └── ThemeUpdateChecker.php
│       ├── autoload.php
│       ├── css
│       │   └── puc-debug-bar.css
│       ├── js
│       │   └── debug-bar.js
│       └── vendor
│           ├── Parsedown.php
│           ├── ParsedownLegacy.php
│           ├── ParsedownModern.php
│           └── PucReadmeParser.php
├── index.php
├── languages
│   └── kratos.pot
├── page.php
├── pages
│   ├── page-content.php
│   ├── page-smilies.php
│   └── page-toolbar.php
├── screenshot.png
├── single.php
└── style.css
```

## Changelog
Detailed changes for each release are documented in the [release notes](https://github.com/Vtrois/Kratos/releases).

## Donation
If you find Kratos useful, you can buy us a cup of coffee

<p align="center">
<img width="650" src="https://raw.githubusercontent.com/Vtrois/Kratos/master/inc/options-framework/images/donate.png">
</p>

## Sponsors

Special thanks to the generous sponsorship by:

<p>
<a width="200" href="https://www.maoyuncloud.com/" target="_blank"><img src="https://raw.githubusercontent.com/Vtrois/Kratos/master/inc/options-framework/images/maocloud.png"></a>
</p>

## License

The code is available under the [MIT](https://github.com/Vtrois/Kratos/blob/master/LICENSE) license.

The document is licensed under a [Creative Commons Attribution-NonCommercial-NoDerivatives 4.0 International License](http://creativecommons.org/licenses/by-nc-nd/4.0/).