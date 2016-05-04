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

/**
 * Class User
 * @package CFM2\Models
 */
class User extends SimpleModel
{
    /**
     * dispense
     * FUSE method for pre-loading RedBean
     */
    public function dispense() {
        $this->bean->display_name = '';
        $this->bean->username = '';
        $this->bean->password = '';
        $this->bean->email = '';
        $this->bean->enabled = true;
        $this->bean->role = 'user';
        $this->bean->created_on = time();
        // @TODO Add username from the creator
        $this->bean->created_by = '';
        $this->bean->modified_on = '';
        $this->bean->modified_by = '';
        $this->bean->last_access = '';
    }
    /**
     * update
     * FUSE method for RedBean validation use
     */
    public function update() {
        // Hash the password if it changes
        if($this->bean->hasChanged('password')) {
            $this->bean->password = password_hash($this->bean->password, CF_PASS_ALGO);
        }
        if(!is_null($this->bean->id)) {
            $this->bean->modified_on = time();
            // @TODO Add username from the editor
            $this->bean->modified_by = '';
        }
    }

}