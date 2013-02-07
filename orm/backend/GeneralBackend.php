<?php
namespace Orm\Backend;

interface GeneralBackend {
    /**
     * Find rows where - $wh, with $manager
     * @var \Orm\Manager
     * @var array 
     * @return array
     */
    public function select($manager, $wh);

    /**
     * Insert row - set $kv, with $manager
     * @var \Orm\Manager
     * @var array 
     * @return array 
     */
    public function insert($manager, $kv);

    /**
     * Update row - where $wh, set - $kv, with $manager
     * @var \Orm\Manager
     * @var array
     * @var array 
     * @return array 
     */
    public function update($manager, $kv, $wh);

    /**
     * Prepare db for $manager
     * @var \Orm\Manager
     */
    public function prepare($manager);
}
