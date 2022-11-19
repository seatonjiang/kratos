<?php

/**
 * 表情图标
 * @author Seaton Jiang <hi@seatonjiang.com>
 * @license GPL-3.0 License
 * @version 2022.01.26
 */
$smilies = join("\n", array_map(function ($s) {
    return '<a href="javascript:grin(\':' . $s . ':\')"><img class="d-block" src="'
        . apply_filters('smilies_src', ASSET_PATH . "/assets/img/smilies/{$s}.png", "{$s}.png", site_url())
        . "\" alt='{$s}'></a>";
}, [
    'razz',
    'evil',
    'exclaim',
    'smile',
    'redface',
    'biggrin',
    'eek',
    'confused',
    'idea',
    'lol',
    'mad',
    'twisted',
    'rolleyes',
    'wink',
    'cool',
    'arrow',
    'neutral',
    'cry',
    'mrgreen',
    'drooling',
    'persevering'
]));
