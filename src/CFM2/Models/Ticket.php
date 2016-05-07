<?php
/**
 * cf-manager2
 * @package cf-manager2
 * @copyright Copyright (c) 2016, Diftraku
 * @author diftraku
 * @license https://opensource.org/licenses/MIT The MIT License (MIT)
 */

namespace CFM2\Models;

use RedBeanPHP\SimpleModel;

class Ticket extends SimpleModel
{
    /**
     * dispense
     * FUSE method for pre-loading RedBean
     */
    public function dispense() {
        $this->bean->first_name = '';
        $this->bean->last_name = '';
        $this->bean->email = '';
        $this->bean->type = null;
        $this->bean->hash = '';
        $this->bean->created_on = time();
        // @TODO Add username from the creator
        $this->bean->created_by = 'system';
        $this->bean->modified_on = '';
        $this->bean->modified_by = '';
        $this->bean->metadata = json_encode(['__version' => 1]);
    }
    /**
     * update
     * FUSE method for RedBean validation use
     */
    public function update() {
        if (empty($this->bean->hash)) {
            $this->bean->hash = sha1(uniqid(time(), true));
        }
        if($this->bean->id > 0) {
            $this->bean->modified_on = time();
            // @TODO Add username from the editor
            $this->bean->modified_by = 'system';
        }
    }
}