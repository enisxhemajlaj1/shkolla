<?php

function setEnumType($filetype) {
    $format = explode("/", $filetype)[0];
    return ($format === 'image') ? 'photo' : $format;
}