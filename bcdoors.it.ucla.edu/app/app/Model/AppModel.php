<?php
/**
 * Application model for CakePHP.
 *
 * This file is application-wide model file. You can put all
 * application-wide model-related methods here.
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * For full copyright and license information, please see the LICENSE.txt
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright (c) Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       app.Model
 * @since         CakePHP(tm) v 0.2.9
 * @license       http://www.opensource.org/licenses/mit-license.php MIT License
 */

App::uses('Model', 'Model');

/**
 * Application model for Cake.
 *
 * Add your application-wide methods in the class below, your models
 * will inherit them.
 *
 * @package       app.Model
 */
class AppModel extends Model
{
    public function getColumnType($column) {
        $db = $this->getDataSource();
        $cols = $this->schema();
        $model = null;

        $startQuote = isset($db->startQuote) ? $db->startQuote : null;
        $endQuote = isset($db->endQuote) ? $db->endQuote : null;
        $column = str_replace(array($startQuote, $endQuote), '', $column);

        if (strpos($column, '.')) {
            list($model, $column) = explode('.', $column);
        }

        if (isset($model) && $model != $this->alias && isset($this->{$model})) {
            return $this->{$model}->getColumnType($column);
        }

        if (isset($cols[$column]) && isset($cols[$column]['data_type'])) {
            return $cols[$column]['data_type'];
        }

        return null;
    }
    /*
    public function delete($id = null, $cascade = true)
    {
        if (!empty($id)) {
                $this->id = $id;
        }

        $id = $this->id;
echo "AppModel.delete<BR><BR>";
        $event = new CakeEvent('Model.beforeDelete', $this, array($cascade));
        list($event->break, $event->breakOn) = array(true, array(false, null));
        $this->getEventManager()->dispatch($event);
        if ($event->isStopped()) {
                return false;
        }

        if (!$this->exists()) {
                return false;
        }

        $this->_deleteDependent($id, $cascade);
        $this->_deleteLinks($id);
        $this->id = $id;

        if (!empty($this->belongsTo)) {
                foreach ($this->belongsTo as $assoc) {
                        if (empty($assoc['counterCache'])) {
                                continue;
                        }

                        $keys = $this->find('first', array(
                                'fields' => $this->_collectForeignKeys(),
                                'conditions' => array($this->alias . '.' . $this->primaryKey => $id),
                                'recursive' => -1,
                                'callbacks' => false
                        ));
                        break;
                }
        }
pr(array($this->alias . '.' . $this->primaryKey => $id));
var_dump($id);
        //if (!$this->getDataSource()->delete($this, array($this->alias . '.' . $this->primaryKey => $id))) {
        if (!$this->getDataSource()->delete($this, array($this->alias . '.' . $this->primaryKey => $id))) {
                return false;
        }

        if (!empty($keys[$this->alias])) {
                $this->updateCounterCache($keys[$this->alias]);
        }

        $this->getEventManager()->dispatch(new CakeEvent('Model.afterDelete', $this));
        $this->_clearCache();
        $this->id = false;

        return true;
    }
    public function deleteAll($conditions, $cascade = true, $callbacks = false)
    {
        if (empty($conditions)) {
                return false;
        }

        $db = $this->getDataSource();

        if (!$cascade && !$callbacks) {
                return $db->delete($this, $conditions);
        }

        $ids = $this->find('all', array_merge(array(
                           'fields' => "{$this->alias}.{$this->primaryKey}",
                           'order' => false,
                           'group' => "{$this->alias}.{$this->primaryKey}",
                           'recursive' => 0), compact('conditions')));

        if ($ids === false || $ids === null) {
                return false;
        }

        $ids = Hash::extract($ids, "{n}.{$this->alias}.{$this->primaryKey}");
        if (empty($ids)) {
                return true;
        }

        if ($callbacks) {
                $_id = $this->id;
                $result = true;
                foreach ($ids as $id) {
                        $result = $result && $this->delete($id, $cascade);
                }

                $this->id = $_id;
                return $result;
        }

        foreach ($ids as $id) {
                $this->_deleteLinks($id);
                if ($cascade) {
                        $this->_deleteDependent($id, $cascade);
                }
        }

        return $db->delete($this, array($this->alias . '.' . $this->primaryKey => $ids));
    }
     * 
     */
}
