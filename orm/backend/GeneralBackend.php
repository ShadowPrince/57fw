<?php
namespace Orm\Backend;

interface GeneralBackend {
    public function select($manager, $kv);
    public function insert($manager, $kv);
    public function update($manager, $kv, $wh);
}
