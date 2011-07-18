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

class WoW_DBStorage {
    protected $m_storageName = null;
    protected $m_storageIndex = '';
    protected $m_entry = 0;
    protected $m_data = array();
    
    public function __construct($table_name) {
        $this->m_storageName = $table_name;
    }
    
    protected function InitStorage() {
        exit;
    }
    
    protected function BeforeLoad() {}
    protected function AfterLoad()  {}
    
    public function FindEntry($entry) {
        $this->BeforeLoad();
        
        if(!$this->m_storageIndex || !$this->m_storageName) {
            WoW_Log::WriteError('%s : fatal error: storage name or storage index was not defined!', __METHOD__);
            return false;
        }
        $this->m_entry = $entry;
        $this->m_data = DB::WoW()->selectRow("SELECT * FROM `DBPREFIX_%s` WHERE `%s` = '%s' LIMIT 1", $this->m_storageName, $this->m_storageIndex, $this->m_entry);
        if($this->m_data) {
            WoW_Log::WriteLog('%s : entry "%s" was not found in "%s" storage (index: "%s").', __METHOD__, $this->m_entry, $this->m_storageName, $this->m_storageIndex);
            return false;
        }
        return $this->AfterLoad();
    }
}
?>