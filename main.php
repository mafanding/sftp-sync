<?php
if (file_exists(".git")) {
    exec("git status | grep modified: | wc -l", $output, $code);
    if ($code === 0 && intval($output[0]) > 0) {
        //todo
    }
} else {
    print_die(255, ".git directory doesn't exists");
}

function print_die($code = 0, $str = "") {
    if (!empty($str)) {
        printf("%s\n", $str);
    }
    exit((int) $code);
}
