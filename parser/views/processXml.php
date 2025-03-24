<?php $this->showTree(FILE_PARAM) ?>
<div class="process">
    <h1 class="title form__field_full"><?php echo $this->h1; ?></h1>
    <?php if ( ! empty($this->tree) ) { ?>
        <div class="process__status"></div>
        <div class="process__btns<?php echo Helper::checkElemActive($generate, true, ' hide') ?>">
            <button class="button" onclick="processXml()">Запустить генерацию</button>
        </div>
        <div class="process__new<?php echo Helper::checkElemActive($generate, true, ' hide') ?>">
            <a class="link" href="/">Загрузить еще XML-файл</a><br />
        </div>
        <div class="process__links<?php echo Helper::checkElemActive($generate, false, ' hide') ?>">
            <!-- <a class="link" onclick="getZip()" href="/?mode=archive_cats">Получить архив по категориям</a><br /> -->
            <span class="link" onclick="getZip(this)" data-link="/?mode=archive_channels">Получить архив по каналам</span><br />
        </div>
    <?php } else { ?>
        Пожалуйста, сначала <a class="link" href="/">загрузите XML-файлы</a>
    <?php } ?>
</div>