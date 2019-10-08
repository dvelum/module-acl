<?php
/**
 *  DVelum project https://github.com/dvelum/dvelum
 *  Copyright (C) 2011-2017  Kirill Yegorov
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
declare(strict_types=1);

namespace Dvelum\Orm;

use Dvelum\Orm\Record\Acl;
use Dvelum\Orm\Record\DataModelAcl;
use Dvelum\Orm\Record\ErrorMessage;

class AclRecord extends Record
{
    /**
     * Access Control List Adapter
     * @var Record\Acl | false
     */
    protected $acl = false;
    /**
     * System flag. Disable ACL create permissions check
     * @var bool
     */
    protected $disableAclCheck = false;


    public function __construct(string $name, $id = false)
    {
        parent::__construct($name, $id);

        $this->initAcl();

        if (!$this->id) {
            if ($this->acl && !$this->disableAclCheck) {
                $this->checkCanCreate();
            }
        }
    }

    /**
     * @return DataModelAcl
     */
    public function getDataModel(): DataModelAcl
    {
        if (empty($this->dataModel)) {
            $this->dataModel = new DataModelAcl();
        }
        return $this->dataModel;
    }

    /**
    * Init ACL adapter
    */
    protected function initAcl()
    {
        $acl = Acl::factory($this->config);
        if(!empty($acl)){
            $this->acl = $acl;
        }
    }

    /**
     * Load object data
     * @throws \Exception
     * @return void
     */
    protected function loadData(): void
    {
        $this->checkCanRead();
        $dataModel = $this->getDataModel();
        $data = $dataModel->load($this);
        $this->setRawData($data);
    }
    /**
     * Get the object data, returns the associative array ‘field name’
     * @param boolean $withUpdates , optional default true
     * @return array
     */
    public function getData($withUpdates = true): array
    {
        if ($this->acl && !$this->disableAclCheck) {
            $this->checkCanRead();
        }
        return parent::getData($withUpdates);
    }
    /**
     * Get updated, but not saved object data
     * @return array
     * @throws Exception
     */
    public function getUpdates(): array
    {
        if ($this->acl) {
            $this->checkCanRead();
        }
        return parent::getUpdates();
    }
    /**
     * Set the object identifier (existing DB ID)
     * @param mixed $id
     * @return void
     * @throws Exception
     */
    public function setId($id): void
    {
        if ($this->acl && !$this->disableAclCheck) {
            $this->checkCanCreate();
        }
        parent::setId($id);
    }
    /**
     * Set the object field val
     * @param string $name
     * @param mixed $value
     * @return bool
     * @throws Exception
     */
    public function set(string $name, $value): bool
    {
        if ($this->acl && !$this->getId()) {
            $this->checkCanCreate();
        }
        if ($this->acl && $this->getId()) {
            $this->checkCanEdit();
        }
        return parent::set($name, $value);
    }
    /**
     * Get the object field value
     * If field value was updated method returns new value
     * otherwise returns old value
     * @param string $name - field name
     * @throws Exception
     * @return mixed
     */
    public function get(string $name)
    {
        if ($this->acl && !$this->disableAclCheck) {
            $this->checkCanRead();
        }
        return parent::get($name);
    }
    /**
     * Get the initial object field value (received from the database)
     * whether the field value was updated or not
     * @param string $name - field name
     * @throws Exception
     * @return mixed
     */
    public function getOld(string $name)
    {
        if ($this->acl) {
            $this->checkCanRead();
        }
        return parent::getOld($name);
    }

    /**
     * Get Access control List
     * @return Record\Acl | false
     */
    public function getAcl()
    {
        return $this->acl;
    }

     /**
     * Disable ACL create permissions check
     * @param bool $bool
     * @return void
     */
    public function disableAcl(bool $bool): void
    {
        $this->disableAclCheck = $bool;
    }

    protected function checkCanRead()
    {
        if ($this->acl && !$this->acl->canRead($this)) {
            throw new Exception(ErrorMessage::factory()->cantRead($this));
        }
    }

    protected function checkCanEdit()
    {
        if ($this->acl && !$this->acl->canEdit($this)) {
            throw new Exception(ErrorMessage::factory()->cantEdit($this));
        }
    }

    protected function checkCanDelete()
    {
        if ($this->acl && !$this->acl->canDelete($this)) {
            throw new Exception(ErrorMessage::factory()->cantDelete($this));
        }
    }

    protected function checkCanCreate()
    {
        if ($this->acl && !$this->acl->canCreate($this)) {
            throw new Exception(ErrorMessage::factory()->cantCreate($this));
        }
    }

    protected function checkCanPublish()
    {
        if ($this->acl && !$this->acl->canPublish($this)) {
            throw new Exception(ErrorMessage::factory()->cantPublish($this));
        }
    }
}
