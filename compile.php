<?php

if (!c::get('Compile'))
    return;

include_once('JSqueeze.php');

$kirby = kirby();
$JSqueeze = new JSqueeze();
$cssHandler = kirby()->option('css.handler');
$jsHandler  = kirby()->option('js.handler');

$kirby->options['css.handler'] = function($url, $media = false) use($cssHandler, $kirby) {
    $file = $kirby->roots()->index() . DS . $url;

    $mainCssFile = $kirby->roots()->index() . DS . 'site/cache/main.css';

    $content = file_get_contents($file);

    $content = preg_replace('!/\*[^*]*\*+([^/][^*]*\*+)*/!', '', $content);
    // Remove space after colons
    $content = str_replace(': ', ':', $content);
    // Remove whitespace
    $content = str_replace(array("\r\n", "\r", "\n", "\t", '  ', '    ', '    '), '', $content);

    $css = c::get('cssBuffer').$content;
    c::set('cssBuffer', $css);

    file_put_contents($mainCssFile, $css);


    if(c::get('cssInserted') == false) {
        c::set('cssInserted',  true);
        return call($cssHandler, array('site/cache/main.css', $media));
    }
    else
        return false;
};

$kirby->options['js.handler'] = function($url, $media = false) use($jsHandler, $kirby, $JSqueeze) {
    $file = $kirby->roots()->index() . DS . $url;

    $mainJSFile = $kirby->roots()->index() . DS . 'site/cache/main.js';

    $content = file_get_contents($file);

    $content = $JSqueeze->squeeze($content, false, false);

    $js = c::get('jsBuffer').$content;
    c::set('jsBuffer', $js);

    file_put_contents($mainJSFile, $js);


    if(c::get('jsInserted') == false) {
        c::set('jsInserted',  true);
        return call($jsHandler, array('site/cache/main.js', $media));
    }
    else
        return false;
};
