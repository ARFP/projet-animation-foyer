<?php
// fichier test.php
class Test {
    public function clear_all_transients() {
        echo "Method is accessible";
    }
}

$test = new Test();
$test->clear_all_transients();
