<?php

class UniqueKeyInstancePoolingBehaviorTest extends PHPUnit_Framework_TestCase
{
    protected static $initialized = false;

    protected function setUp()
    {
        if (static::$initialized) {
            return;
        }

        static::$initialized = true;

        $builder = new PropelQuickBuilder();

        $config  = $builder->getConfig();
        $config->setBuildProperty('behavior.unique_key_instance_pooling.class', '../src/UniqueKeyInstancePoolingBehavior');

        $builder->setConfig($config);
        $builder->setSchema($this->getSchema());

        $builder->build();
    }

    protected function getSchema()
    {
        return <<<XML
<database name="default" defaultIdMethod="native">
    <table name="user" phpName="UKIPUser">
        <column name="id" type="integer" autoIncrement="true" primaryKey="true" />
        <column name="email" type="varchar" size="255" required="true" primaryString="true" />

        <behavior name="unique_key_instance_pooling" />

        <unique>
            <unique-column name="email" />
        </unique>
    </table>
</database>
XML;
    }

    public function testSetupIsFine()
    {
        $this->assertTrue(class_exists('UKIPUserQuery'),
            'The schema has been loaded correctly.');
    }

    /**
     * @depends testSetupIsFine
     */
    public function testMethodHasBeenGenerated()
    {
        $this->assertTrue(method_exists('UKIPUserQuery', 'findOneByEmail'));
    }

    /**
     * @depends testMethodHasBeenGenerated
     */
    public function testInstanceIsPutIntoPool()
    {
        $user = new UKIPUser;
        $user->setEmail('mail@example.com');
        $user->save();

        UKIPUserPeer::clearInstancePool();
        $this->assertEmpty(UKIPUserPeer::$instances,
            'The instance pool has been cleared.');

        UKIPUserQuery::create()->findOneByEmail('mail@example.com');

        $key = md5('unique_email_mail@example.com');
        $this->assertArrayHasKey($key, UKIPUserPeer::$instances,
            'The unique key based instances pool entry has been creasted.');
        $this->assertEquals($user, UKIPUserPeer::$instances[$key],
            'The object has been saved in the instances pool.');
    }

    /**
     * @depends testMethodHasBeenGenerated
     */
    public function testInstanceIsReadFromPool()
    {
        $this->assertNotEmpty(UKIPUserPeer::$instances,
            'The instance pool is filled.');

        $con = Propel::getConnection(UKIPUserPeer::DATABASE_NAME);
        $queryCount = $con->getQueryCount();

        $this->assertInstanceOf('UKIPUser', UKIPUserQuery::create()->findOneByEmail('mail@example.com'),
            'The object has been found.');
        $this->assertEquals($queryCount, $con->getQueryCount(),
            'No additional queries have been issued.');
    }
}
