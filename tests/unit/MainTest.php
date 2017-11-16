<?php

namespace ManageStagingEmailWPE;

require_once __DIR__ . '../../vendor/autoload.php';

class MainTest extends \PHPUnit\Framework\TestCase
{
    /**
     * Make sure runIfOnStaging() only executes in staging environment
     *
     * @param $isStaging bool True if on staging
     * @param $expect bool|null
     *
     * @dataProvider dataTestRunOnStaging
     */
    public function testRunOnStaging($isStaging, $expect)
    {
        $settings = $this->getMockBuilder('ManageStagingEmailWPE\Settings')->getMock();
        $admin = $this->getMockBuilder('ManageStagingEmailWPE\Admin')->getMock();
        $redirectEmail = $this->getMockBuilder('ManageStagingEmailWPE\RedirectEmail')->getMock();

        $main = $this->getMockBuilder('ManageStagingEmailWPE\Main')
                ->setMethods(array('__construct', 'manageEmailBehavior', 'manageAddMenuItem'))
                ->setConstructorArgs(array($settings, $admin, $redirectEmail))
                ->getMock();

        $result = $main->runOnStaging($isStaging);
        $this->assertEquals($retult, $expect);
    }

    public function dataTestRunOnStaging()
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
        $mock = $this->getMockBuilder('ManageStagingEmailWPE\Main')
            ->setMethods(array('getSelection', 'wpHookToRedirectEmail', 'wpHookToReplacePhpMailer'))
            ->getMock();
        $mock->method('getSelection')->will($this->returnValue($selection));

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
        $mock = $this->getMockBuilder('ManageStagingEmailWPE\Main')
            ->setMethods(array('checkAdmin', 'wpHookToAddMenuItem'))
            ->getMock();
        $mock->method('checkAdmin')->will($this->returnValue($isAdmin));

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
