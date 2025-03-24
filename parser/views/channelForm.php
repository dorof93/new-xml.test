<!-- Канал -->
<div class="form__field">
    <!-- ИД (скрыто) -->
    <input type="hidden" name="[<?php echo $key ?>][id]"
            value="<?php echo $row->id ?>"
        >

    <!-- Название канала в программе -->
    <div class="form__field form__field_full">
        <input 
            class="form__text form__input" 
            type="text" 
            placeholder="Название канала в программе" 
            name="[<?php echo $key ?>][name]" 
            value="<?php echo $row->name ?>"
        >
    </div>
    <!-- Выбор категории (селект) -->
    <div class="form__field form__field_full">
        <select class="form__select" name="[<?php echo $key ?>][cat_id]">
            <?php foreach ($cats as $cat) { ?>
                <option value="<?php echo $cat['id'] ?>"<?php echo Helper::checkElemActive($cat['id'], $row->cat_id, ' selected') ?>>
                    <?php echo $cat['name'] ?>
                </option>
            <?php } ?> 
        </select>
    </div>
    <!-- Примечание -->
    <div class="form__field form__field_full">
        <input 
            class="form__text form__input" 
            type="text" 
            placeholder="Примечание" 
            name="[<?php echo $key ?>][note]"
            value="<?php echo $row->note ?>"
        >
    </div>
    <!-- Час. пояс летнего времени (по умолчанию +0300) -->
    <div class="form__field form__field_full">
        <input 
            class="form__text form__input" 
            type="text" 
            placeholder="Час. пояс летнего времени" 
            name="[<?php echo $key ?>][utc_tz]"
            value="<?php echo $row->utc_tz ?>"
        >
    </div>
    <!-- Час. пояс зимнего времени (по умолчанию +0300) -->
    <div class="form__field form__field_full">
        <input 
            class="form__text form__input" 
            type="text" 
            placeholder="Час. пояс зимнего времени" 
            name="[<?php echo $key ?>][utc_tz_wt]"
            value="<?php echo $row->utc_tz_wt ?>"
        >
    </div>
    <!-- Сортировка (по умолчанию 99) -->
    <div class="form__field form__field_full">
        <input 
            class="form__text form__input" 
            type="Сортировка" 
            placeholder="text" 
            name="[<?php echo $key ?>][sort]"
            value="<?php echo $row->sort ?>"
        >
    </div>
</div>
<!-- Соответствия в ЕПГ-файлах -->
<div class="form__field">
    <?php 
        foreach ($row->epg as $epg_key => $epg) { 
            ?>
            <!-- ЕПГ-источник -->
            <div class="form__field form__field_full">
                <!-- <input 
                    class="form__text form__input" 
                    type="text" 
                    placeholder="ЕПГ-источник" 
                    name="[<?php echo $key ?>][epg][<?php echo $epg_key ?>][source_id]"
                    value="<?php echo $epg->source_id ?>"
                > -->
                ЕПГ-источник: <?php echo $epg->source_id ?>
            </div>
            <!-- ИД канала в ЕПГ (под ним ИД из xml-файла) -->
            <div class="form__field form__field_full">
                <input 
                    class="form__text form__input" 
                    type="text" 
                    placeholder="ИД канала в ЕПГ" 
                    name="[<?php echo $key ?>][epg][<?php echo $epg_key ?>][epg_id]"
                    value="<?php echo $epg->xmlEpgId ?>"
                >
                ИД из БД: <?php echo $epg->epg_id; ?>
            </div>
            <!-- Названия канала в ЕПГ (под ним названия из xml-файла) -->
            <div class="form__field form__field_full">
                <input 
                    class="form__text form__input" 
                    type="text" 
                    placeholder="Названия канала в ЕПГ" 
                    name="[<?php echo $key ?>][epg][<?php echo $epg_key ?>][epg_names]"
                    value="<?php echo $epg->xmlEpgNames ?>"
                >
                Названия канала из БД: <?php echo $epg->epg_names; ?>
            </div>
            <!-- Час. пояс летнего времени в ЕПГ -->
            <div class="form__field form__field_full">
                <input 
                    class="form__text form__input" 
                    type="text" 
                    placeholder="Час. пояс летнего времени  в ЕПГ" 
                    name="[<?php echo $key ?>][epg][<?php echo $epg_key ?>][tz_offset]"
                    value="<?php echo $epg->tz_offset ?>"
                >
            </div>
            <!-- Час. пояс зимнего времени в ЕПГ -->
            <div class="form__field form__field_full">
                <input 
                    class="form__text form__input" 
                    type="text" 
                    placeholder="Час. пояс зимнего времени в ЕПГ" 
                    name="[<?php echo $key ?>][epg][<?php echo $epg_key ?>][tz_offset_wt]"
                    value="<?php echo $epg->tz_offset_wt ?>"
                >
            </div>
            <!-- Приоритет -->
            <div class="form__field form__field_full">
                <input 
                    class="form__text form__input" 
                    type="text" 
                    placeholder="Приоритет" 
                    name="[<?php echo $key ?>][epg][<?php echo $epg_key ?>][prior]"
                    value="<?php echo $epg->prior ?>"
                >
            </div>
            <!-- Исключить из тестов (select) -->
            <div class="form__field form__field_full">
                <input 
                    class="form__text form__input" 
                    type="text" 
                    name="[<?php echo $key ?>][epg][<?php echo $epg_key ?>][exl_test]"
                    value="<?php echo $epg->exl_test ?>"
                >
            </div>
        <?php
        }
    ?>
</div>