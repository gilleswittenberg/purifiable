<?php
class HTMLPurifierTestSuite extends CakeTestSuite {
    public static function suite() {
        $suite = new CakeTestSuite('HTMLPurifier testsuite');
        $suite->addTestDirectoryRecursive(App::pluginPath('HTMLPurifier') . 'Test' . DS . 'Case');
        return $suite;
    }
}

