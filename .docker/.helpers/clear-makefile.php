<?php

$makefileContent = file_get_contents('Makefile');

$makefileContent = preg_replace('=\n# ---.*=s', '', $makefileContent);

file_put_contents('Makefile', $makefileContent);
