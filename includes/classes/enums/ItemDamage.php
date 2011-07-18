<?php

/**
 * Copyright (C) 2011 Shadez <https://github.com/Shadez>
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 2 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 **/

class ItemDamageEntry extends WoW_DBStorage {
    public $id = 0;
    public $Values = array();
    public $ItemLevel = 0;
    
    protected function InitStorage() {
        $this->m_storageIndex = 'id';
    }
    
    protected function BeforeLoad() {
        for($i = 1; $i < 8; ++$i) {
            $this->Values[$i] = 0.0;
        }
    }
    
    protected function AfterLoad() {
        $this->id = $this->m_data['id'];
        for($i = 1; $i < 8; ++$i) {
            $this->Values[($i - 1)] = $this->m_data['Value' . $i];
        }
        $this->itemLevel = $this->m_data['ItemLevel'];
        return true;
    }
}
?>