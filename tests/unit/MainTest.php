<?php

namespace nategay\manage_staging_email_wpe;

class MainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Make sure init only executes in staging environment
     *
     * @param $isStaging bool True if on staging
     * @param $expect bool|null
     *
     * @dataProvider dataTestInit
     */
    public function testInit($isStaging, $expect)
    {
        $mock = $this->getMockBuilder('nategay\manage_staging_email_wpe\Main')
            ->setMethods(array('checkStaging', 'addHooks'))
            ->getMock();
        $mock->method('checkStaging')->will($this->returnValue($isStaging));
        $mock->method('addHooks')->will($this->returnValue(true));

        $admin;
        $redirectEmail;
        $selection;

        $this->assertEquals($mock->init($selection, $redirectEmail, $admin), $expect);
    }

    public function dataTestInit()
    {
        return array(
            array(true, true),
            array(false, null),
        );
    }
}
