<ul class="tree list">
    <?php
        foreach ($this->tree as $key => $name) {
            ?>
            <li class="list__item tree__item">
                <a class="link tree__trash list__link link_red confirm" title="В корзину" data-confirm="Удалить <?php echo $name; ?>?" href="/?<?php echo $param ?>=<?php echo $key; ?>&del=1">
                    [x]
                </a>
                <a class="link tree__link list__link" href="/?<?php echo $param ?>=<?php echo $key; ?>">
                    <?php echo $name; ?>
                </a>
            </li>
            <?php
        }
    ?>
</ul>