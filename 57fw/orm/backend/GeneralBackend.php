<?php
namespace Orm\Backend;

/**
 * Interface for general backend
 */
interface GeneralBackend {
    /**
     * Find rows where - $wh, with $manager
     * @param \Orm\Manager
     * @param array 
     * @return array
     */
    public function select($manager, $wh, $fields);

    /**
     * Insert row - set $kv, with $manager
     * @param \Orm\Manager
     * @param array 
     * @return array 
     */
    public function insert($manager, $kv);

    /**
     * Update row - where $wh, set - $kv, with $manager
     * @param \Orm\Manager
     * @param array
     * @param array 
     * @return array 
     */
    public function update($manager, $kv, $wh);

    /**
     * Delete row - where $wh, with $manager
     * @param \Orm\Manager
     * @param array
     */
    public function delete($manager, $wh);

    /**
     * Prepare db for $manager
     * @param \Orm\Manager
     * @param array
     */
    public function prepare($manager, $opts, $print_callback);
}
