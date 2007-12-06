<?php
/**
 * Unit test
 *
 * LICENSE: Licensed under the terms of the PHProjekt 6 License
 *
 * @copyright  Copyright (c) 2007 Mayflower GmbH (http://www.mayflower.de)
 * @license    http://phprojekt.com/license PHProjekt 6 License
 * @version    CVS: $Id:
 * @link       http://www.phprojekt.com
 * @since      File available since Release 1.0
*/
require_once 'PHPUnit/Framework.php';

/**
 * Tests History
 *
 * @copyright  Copyright (c) 2007 Mayflower GmbH (http://www.mayflower.de)
 * @license    http://phprojekt.com/license PHProjekt 6 License
 * @version    Release: @package_version@
 * @link       http://www.phprojekt.com
 * @since      File available since Release 1.0
 * @author     Gustavo Solt <solt@mayflower.de>
 */
class Phprojekt_HistoryTest extends PHPUnit_Framework_TestCase
{
    /**
     * Test empty call
     *
     */
    public function testEmptyObject()
    {
        $history = new Phprojekt_History(array('db' => $this->sharedFixture));

        $this->setExpectedException('Exception');
        $history->saveFields('','add');
    }

    /**
     * Test add history
     *
     */
    public function testAddCall()
    {
        $project = new Project_Models_Project(array('db' => $this->sharedFixture));

        $project->parent = 2;
        $project->title = 'TEST';
        $project->startDate = '1981-05-12';
        $project->endDate = '1981-05-12';
        $project->priority = 1;
        $project->currentStatus = 2;
        $project->read = 1;
        $project->write = 1;
        $project->admin = 1;
        $project->save();
        Zend_Registry::set('insertedId', $project->id);

        $history = new Phprojekt_History(array('db' => $this->sharedFixture));
        $data = $history->getHistoryData($project, $project->id);
        $array = array('userId' => '1',
                       'module' => 'Project',
                       'dataobjectId' => $project->id,
                       'field' => 'currentStatus',
                       'oldValue' => '',
                       'newValue' => '2',
                       'action' => 'add',
                       'datetime' => date("Y-m-d"));
        $found = 0;
        foreach ($data as $key => $values) {
            /* Remove the hour */
            $values['datetime'] = substr($values['datetime'],0,10);
            $result = array_diff_assoc($values,$array);

            if (empty($result)) {
                $found = 1;
            }
        }
        if (!$found) {
            $this->fail('Save add history error');
        }
    }

    /**
     * Test edit history
     *
     */
    public function testEditCall()
    {
        $project = new Project_Models_Project(array('db' => $this->sharedFixture));
        $history = new Phprojekt_History(array('db' => $this->sharedFixture));

        $project->find(Zend_Registry::get('insertedId'));
        $project->title = 'EDITED TEST';
        $project->save();

        $history = new Phprojekt_History(array('db' => $this->sharedFixture));

        $data = $history->getHistoryData($project,$project->id);
        $array = array('userId' => '1',
                       'module' => 'Project',
                       'dataobjectId' => Zend_Registry::get('insertedId'),
                       'field' => 'title',
                       'oldValue' => 'TEST',
                       'newValue' => 'EDITED TEST',
                       'action' => 'edit',
                       'datetime' => date("Y-m-d"));
        $found = 0;
        foreach ($data as $key => $values) {
            /* Remove the hour */
            $values['datetime'] = substr($values['datetime'],0,10);
            $result = array_diff_assoc($values,$array);

            if (empty($result)) {
                $found = 1;
            }
        }
        if (!$found) {
            $this->fail('Save edit history error');
        }
    }

    public function testGetHistoryData()
    {
        $project = new Project_Models_Project(array('db' => $this->sharedFixture));
        $history = new Phprojekt_History(array('db' => $this->sharedFixture));

        $data = $history->getHistoryData($project, Zend_Registry::get('insertedId'));
        $array = array('userId' => '1',
                       'module' => 'Project',
                       'dataobjectId' => Zend_Registry::get('insertedId'),
                       'field' => 'title',
                       'oldValue' => 'TEST',
                       'newValue' => 'EDITED TEST',
                       'action' => 'edit',
                       'datetime' => date("Y-m-d"));
        $found = 0;
        foreach ($data as $key => $values) {
            /* Remove the hour */
            $values['datetime'] = substr($values['datetime'],0,10);
            $result = array_diff_assoc($values,$array);

            if (empty($result)) {
                $found = 1;
            }
        }
        if (!$found) {
            $this->fail('Get history error');
        }
    }

    /**
     * Test delete history
     *
     */
    public function testDeleteCall()
    {
        $project = new Project_Models_Project(array('db' => $this->sharedFixture));
        $history = new Phprojekt_History(array('db' => $this->sharedFixture));

        $project->find(Zend_Registry::get('insertedId'));
        $project->delete();

        $history = new Phprojekt_History(array('db' => $this->sharedFixture));
        $data = $history->getHistoryData($project, Zend_Registry::get('insertedId'));
        $array = array('userId' => '1',
                       'module' => 'Project',
                       'dataobjectId' => Zend_Registry::get('insertedId'),
                       'field' => 'title',
                       'oldValue' => 'EDITED TEST',
                       'newValue' => '',
                       'action' => 'delete',
                       'datetime' => date("Y-m-d"));
        $found = 0;
        foreach ($data as $key => $values) {
            /* Remove the hour */
            $values['datetime'] = substr($values['datetime'],0,10);
            $result = array_diff_assoc($values,$array);

            if (empty($result)) {
                $found = 1;
            }
        }
        if (!$found) {
            $this->fail('Save delete history error');
        }
    }
}