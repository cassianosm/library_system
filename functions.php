<?php

function getTwigTemplate($template)
{
    $loader = new Twig_Loader_Filesystem('./templates');
    $twig = new Twig_Environment($loader);
    return $twig->load($template);
}

function clearConnection($mysql)
{
    while ($mysql->more_results()) {
        $mysql->next_result();
        $mysql->use_result();
    }
}

?>