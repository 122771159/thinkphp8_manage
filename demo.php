<?php
//判断变量的类型
// 将object类型
class MyClass {
    public $prop1 = "value1";
    protected $prop2 = "value2";
    private $prop3 = "value3";

    public function getProperties() {
        return get_object_vars($this);
    }
}

$obj = new MyClass();

// 获取包括私有属性在内的对象属性数组
$arr = $obj->getProperties();

print_r($arr);
