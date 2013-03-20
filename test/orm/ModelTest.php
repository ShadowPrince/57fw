<?php
namespace Test\Orm;

class ModelTest extends \PHPUnit_Framework_Testcase {
    public function testCreate() {
        $e = new \Core\Engine();
        $e->register('db', new \Orm\Backend\PDO\PDO(array(
            'uri' => 'mysql:host=localhost;dbname=57fw',
            'user' => 'root',
            'password' => '1',
            'debug' => true
        )));
        $e->register('man', '\Orm\Manager::manGetter');

        return $e;
    }

    /** @depends testCreate */
    public function testManGetter($e) {
        $man = $e->man('\Test\Orm\Model\PlainModel');
        $this->assertEquals($man->getModel(), '\Test\Orm\Model\PlainModel');

        $man = $e->man(new \Test\Orm\Model\PlainModel());
        $this->assertEquals($man->getModel(), 'Test\Orm\Model\PlainModel');

        $this->assertEquals(get_class($man), 'Test\Orm\Model\PlainMan');

        $this->assertEquals($e->man(null), null);

        return $e;
    }

    /** @depends testManGetter */
    public function testPrepare($e) {
        $dm = $e->man('\Test\Orm\Model\DummyModel');
        $dm->prepare(array(), function ($x) {});

        $dm = $e->man('\Test\Orm\Model\FooModel');
        $dm->prepare(array(), function ($x) {});

        $dm = $e->man('\Test\Orm\Model\FailModel');

        $this->__raised = false;
        $dm->prepare(array(), function ($x) {
            if (0 === strpos($x, 'Model ')) {
                $this->__raised = true;
            }
        });
        $this->assertTrue($this->__raised);

        $dm = $e->man('\Test\Orm\Model\PlainModel');
        $dm->prepare(array(), function ($x) {});

        return $e;
    }

    /** @depends testPrepare */
    public function testOldSetter($e) {
        $man = $e->man('\Test\Orm\Model\PlainModel');
        $ins = $man->getModelInstance();
        $this->assertEquals($ins->text(), '1');
        $ins->text('abc');
        $this->assertEquals($ins->text(), 'abc');

        $this->assertNull($ins->text(1, 2, 3));

        return $e;
    }

    /** @depends testPrepare */
    public function testPlainModel($e) {
        $man = $e->man('\Test\Orm\Model\PlainModel');
        $ins = $man->getModelInstance();
        $man->save($ins, 1);

        $ins = $man->find()->current();
        $this->assertEquals($ins->text, '1');

        $ins->text = '123';
        $man->save($ins, 1);
        $this->assertEquals($ins->text, '123');

        try {
            $man->save($ins);
            $this->fail('save() no $inkowpk on non-pkey model');
        } catch (\Orm\Ex\OrmException $ex) {

        }

        return $e;
    }


    /** @depends testPrepare */
    public function testModel($e) {
        $dm = $e->man('\Test\Orm\Model\DummyModel');
        $ins = $dm->getModelInstance();

        $this->assertTrue($ins instanceof \Test\Orm\Model\DummyModel);
        $ins->varchar = md5(1);
        $ins->text = '123';
        try {
            $ins->varchar = $ins;
            $this->fail('Value dont converts to string');
        } catch (\Orm\Ex\FieldValueException $ex) {}

        try {
            $ins->text = $ins;
            $this->fail('Value dont converts to string');
        } catch (\Orm\Ex\FieldValueException $ex) {}

        try {
            $ins->varchar = md5(1) . md5(1);
            $this->fail('Value is longer than length of varchar');
        } catch (\Orm\Ex\FieldValueException $ex) {}

        $ins->int = 123;
        $this->dt = new \DateTime();
        $ins->dt = $this->dt;
        $ins->dt = '2013-04-03 00:59:51';

        $format = $ins->getField('dt');
        $format = $format::$format;

        $ins->dt = array($format, '2013-04-03 00:59:51');

        try {
            $ins->dt = new \Test\Orm\Model\FooModel();
            $this->fail('Must throw exception');
        } catch (\Orm\Ex\FieldValueException $ex) {}

        try {
            $ins->dt = false;
            $this->fail('Must throw exception');
        } catch (\Orm\Ex\FieldValueException $ex) {}

        try {
            $ins->dt = 'abcdef';
            $this->fail('Must throw exception');
        } catch (\Orm\Ex\FieldValueException $ex) {}

        try {
            $ins->dt = array(1, 'abcef');
            $this->fail('Must throw exception');
        } catch (\Orm\Ex\FieldValueException $ex) {}

        try {
            $ins->xyz = 123;
            $this->fail('There is no field xyz, must throw \Orm\Ex\FieldNotFoundException');
        } catch (\Orm\Ex\FieldNotFoundException $ex) {}

        $e->cache['dummy'] = $ins;

        return $e;
    }

    /** @depends testModel */
    public function testFooModel($e) {
        $dm = $e->man('\Test\Orm\Model\FooModel');
        $ins = $dm->getModelInstance();
        $dm->save($ins);

        $e->cache['foo'] = $ins;
        return $e;
    }

    /** @depends testFooModel */
    public function testModelFKey($e) {
        $dummy = $e->cache['dummy'];
        $foo = $e->cache['foo'];

        $dummy->getField('fkey')->setModel('\Test\Orm\Model\FooModel');
        $dummy->getField('fkey')->setModel(null);
        $dummy->getField('fkey')->setModel(new \Test\Orm\Model\FooModel());

        $dummy->fkey = $foo;
        $dummy->fklist = array($foo, $foo);

        $dummy->fkey = 1;
        $dummy->fklist2 = array(1, 1);

        try {
            $dummy->fklist = array(new \Test\Orm\Model\FailModel());
            $this->fail('Value not corrent array of models');
        } catch (\Orm\Ex\FieldValueException $ex) {}

        try {
            $dummy->fklist = array('x', 'y');
            $this->fail('Value not array of model instance or int');
        } catch (\Orm\Ex\FieldValueException $ex) {}

        try {
            $dummy->fkey = 'abc';
            $this->fail('Value not model instance or int');
        } catch (\Orm\Ex\FieldValueException $ex) {}


        return $e;
    }

    /** @depends testModelFKey */
    public function testModelSave($e) {
        $dummy = $e->cache['dummy'];
        $dm = $e->man('\Test\Orm\Model\DummyModel');
        $dm->save($dummy);

        $this->assertNotEquals($dummy->id, null);
        return $e;
    }

    /** @depends testModelSave */
    public function testModelUpdate($e) {
        $dm = $e->man('\Test\Orm\Model\DummyModel');
        $dummy = $dm->get(1);
        $dummy->varchar = md5(3);
        $dm->save($dummy);

        return $e;
    }

    /** @depends testModelSave */
    public function testModelGet($e) {
        $dm = $e->man('\Test\Orm\Model\DummyModel');
        $model = $dm->get(1);

        $this->assertEquals($model->int, 123);
        $this->assertEquals($model->fkey->id, 1);
        $this->assertEquals($model->fkey->id, 1);
        $this->assertEquals($model->fkeyn, null);
        $this->assertEquals($model->dt, $this->dt);

        foreach ($model->fklist as $x) {
            $this->assertEquals($x->id, 1);
        }

        foreach ($model->fklist2 as $x) {
            $this->assertEquals($x->id, 1);
        }

        return $e;
    }

    /** @depends testModelGet */
    public function testDrop($e) {
        $e->db->buildExecute('DROP TABLE test_dummy, test_foo');
    }   
}
