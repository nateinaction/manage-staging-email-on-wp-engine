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
            ->setMethods(array('checkStaging', 'manageEmailBehavior', 'manageAddMenuItem'))
            ->getMock();
        $mock->method('checkStaging')->will($this->returnValue($isStaging));
        $mock->method('manageEmailBehavior')->will($this->returnValue(null));
        $mock->method('manageAddMenuItem')->will($this->returnValue(null));

        $this->assertEquals($mock->init(), $expect);
    }

    public function dataTestInit()
    {
        return array(
            array(true, true),
            array(false, null),
        );
    }

    /**
     * Make sure to initialize the correct email hook based on settings
     *
     * @param $selection string Selection from plugin options admin|custom|log|halt
     * @param $expect string 'redirect' if email is redirected 'replace' if replacing PHPMailer
     *
     * @dataProvider dataTestManageEmailBehavior
     */
    public function testManageEmailBehavior($selection, $expect)
    {
        $mock = $this->getMockBuilder('nategay\manage_staging_email_wpe\Main')
            ->setMethods(array('getSelection', 'wpHookToRedirectEmail', 'wpHookToReplacePhpMailer'))
            ->getMock();
        $mock->method('getSelection')->will($this->returnValue($selection));
        $mock->method('wpHookToRedirectEmail')->will($this->returnValue(null));
        $mock->method('wpHookToReplacePhpMailer')->will($this->returnValue(null));

        $this->assertEquals($mock->manageEmailBehavior(), $expect);
    }

    public function dataTestManageEmailBehavior()
    {
        return array(
            array('admin', 'redirect'),
            array('custom', 'redirect'),
            array('log', 'replace'),
            array('halt', 'replace'),
            array('abc123', 'replace'),
        );
    }

    /**
     * Make sure to only show plugin menu to admins
     *
     * @param $isStaging bool True if on staging
     * @param $expect bool|null
     *
     * @dataProvider dataTestManageAddMenuItem
     */
    public function testManageAddMenuItem($isAdmin, $expect)
    {
        $mock = $this->getMockBuilder('nategay\manage_staging_email_wpe\Main')
            ->setMethods(array('checkAdmin', 'wpHookToAddMenuItem'))
            ->getMock();
        $mock->method('checkAdmin')->will($this->returnValue($isAdmin));
        $mock->method('wpHookToAddMenuItem')->will($this->returnValue(null));

        $this->assertEquals($mock->manageAddMenuItem(), $expect);
    }

    public function dataTestManageAddMenuItem()
    {
        return array(
            array(true, true),
            array(false, null),
        );
    }
}
