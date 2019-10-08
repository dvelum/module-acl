<?php
/**
 *  DVelum project https://github.com/dvelum/dvelum
 *  Copyright (C) 2011-2019  Kirill Yegorov
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

namespace Dvelum\Orm\Record;

use Dvelum\App\Session\User;
use Dvelum\Orm\Model;
use Dvelum\Orm\RecordInterface;
use Dvelum\Orm\Exception;
use Dvelum\Utils;
use Psr\Log\LogLevel;

class DataModelAcl extends DataModel
{
    /**
     * @param RecordInterface $record
     * @param bool $useTransaction
     * @return bool
     * @throws Exception
     */
    public function save(RecordInterface $record, bool $useTransaction) : bool
    {
        $recordName = $record->getName();
        $model = Model::factory($recordName);
        $log = $model->getLogsAdapter();
        $store = $model->getStore();

        if($log){
            $store->setLog($log);
        }

        $acl = $record->getAcl();

        if($acl){
            if($record->getId() && !$acl->canEdit($record)){
                $message = ErrorMessage::factory()->cantEdit($record);
                $record->addErrorMessage($message);
                if($log){
                    $log->log(LogLevel::ERROR, $message);
                }
                return false;
            }
            if(!$record->getId() && !$acl->canCreate($record)){
                $message = ErrorMessage::factory()->cantCreate($record);
                $record->addErrorMessage($message);
                if($log){
                    $log->log(LogLevel::ERROR, $message);
                }
                return false;
            }
        }
        return parent::save($record, $useTransaction);
    }


    /**
     * @param RecordInterface $record
     * @param bool $useTransaction
     * @return bool
     * @throws Exception
     */
    public function saveVersion(RecordInterface $record, bool $useTransaction = true) : bool
    {
        $recordName = $record->getName();
        $model = Model::factory($recordName);
        $log = $model->getLogsAdapter();
        $store = $model->getStore();

        if($log){
            $store->setLog($log);
        }

        $acl = $record->getAcl();

        if($acl) {
            if(!$acl->canEdit($record)){
                $message = ErrorMessage::factory()->cantEdit($record);
                $record->addErrorMessage($message);
                if($log){
                    $log->log(LogLevel::ERROR, $message);
                }
                return false;
            }
        }
       return parent::saveVersion($record, $useTransaction);
    }

    /**
     * @inheritDoc
     */
    public function unpublish(RecordInterface $record, bool $useTransaction) : bool
    {
        $recordName = $record->getName();
        $model = Model::factory($recordName);
        $log = $model->getLogsAdapter();
        $store = $model->getStore();
        $acl = $record->getAcl();

        if ($log) {
            $store->setLog($log);
        }

        if($acl) {
            if(!$acl->canPublish($record)){
                $message = ErrorMessage::factory()->cantPublish($record);
                $record->addErrorMessage($message);
                if($log){
                    $log->log(LogLevel::ERROR, $message);
                }
                return false;
            }
        }
        return parent::unpublish($record, $useTransaction);
    }

    /**
     * @inheritDoc
     */
    public function publish(RecordInterface $record, ?int $version, bool $useTransaction): bool
    {
        $recordName = $record->getName();
        $model = Model::factory($recordName);
        $log = $model->getLogsAdapter();
        $store = $model->getStore();
        $acl = $record->getAcl();

        if ($log) {
            $store->setLog($log);
        }

        if($acl) {
            if(!$acl->canPublish($record)){
                $message = ErrorMessage::factory()->cantPublish($record);
                $record->addErrorMessage($message);
                if($log){
                    $log->log(LogLevel::ERROR, $message);
                }
                return false;
            }
        }
        return parent::publish($record, $version, $useTransaction);
    }

    /**
     * @inheritDoc
     */
    public function delete(RecordInterface $record, bool $useTransaction): bool
    {
        $recordName = $record->getName();
        $model = Model::factory($recordName);
        $log = $model->getLogsAdapter();
        $store = $model->getStore();
        $acl = $record->getAcl();

        if ($log) {
            $store->setLog($log);
        }

        if($acl) {
            if(!$acl->canDelete($record)){
                $message = ErrorMessage::factory()->cantDelete($record);
                $record->addErrorMessage($message);
                if($log){
                    $log->log(LogLevel::ERROR, $message);
                }
                return false;
            }
        }
        return parent::delete($record, $useTransaction);
    }
}