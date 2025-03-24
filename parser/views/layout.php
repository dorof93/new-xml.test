
<!DOCTYPE html>
<html lang="ru">
    <head>
        <title><?php echo $this->title; ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="description" content="<?php echo $this->description; ?>">
        <link rel="icon" href="/favicon.ico">
        <link href="/assets/style.css?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/style.css') ?>" rel="stylesheet">
    </head>
   <body class="">
        <div class="section">
            <div class="section__container">
                <div class="header">
                    <div class="logo header__logo">
                        <a href="/" class="title link link_big logo__link" title="Загрузить XML">
                            /Парсер EPG/
                        </a>
                    </div>
                    <ul class="menu menu_inline header__menu">
                        <!-- <li class="menu__item">
                            <a class="menu__link" href="/?mode=new_channels">Новые каналы в XML</a>
                        </li>
                        <li class="menu__item">
                            <a class="menu__link" href="/?mode=db_channels">Каналы в БД</a>
                        </li> -->
                        <li class="menu__item">
                            <a class="menu__link" href="/?mode=process_xml">Генерация программы</a>
                        </li>
                        <li class="menu__item">
                            <a class="menu__link" href="/">Загрузить XML</a>
                        </li>
                    </ul>
                    <div class="menu-switcher header__menu-switcher">
                        <span class="menu-switcher__line"></span>
                        <span class="menu-switcher__line"></span>
                        <span class="menu-switcher__line"></span>
                    </div>
                </div>
                <div class="workspace">
                    <?php 
                        $this->workspace();
                    ?>
                </div>
                <div class="footer">
                    (c) Oleg Dorofeev 2025
                </div>
            </div>
        </div>
        <script src="/assets/script.js?v=<?php echo filemtime($_SERVER['DOCUMENT_ROOT'] . '/assets/script.js') ?>"></script>
   </body>
</html>