<?php

namespace nategay\manage_staging_email_wpe;

class MainTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test CheckStaging() if in staging environment
     */
    public function testCheckStagingTrue()
    {
        $this->markTestSkipped('Skipping');

        $main = new Main();
        $mock = $this->getMockBuilder('\is_wpe_snapshot')
            ->setMethods(array('is_wpe_snapshot'))
            ->getMock();
        $mock->expects($this->once())->method('is_wpe_snapshot')->will($this->returnValue(true));
        $this->assertTrue($main->checkStaging());
    }

    /**
     * Test CheckStaging() if not in staging environment
     */
    public function testCheckStagingFalse()
    {
        $this->markTestSkipped('Skipping');

        $mock = $this->getMockBuilder('\nategay\manage_staging_email_wpe\Main')
            ->setMethods(array('\is_wpe_snapshot'))
            ->getMock();
        $mock->expects($this->once())->method('\is_wpe_snapshot')->will($this->returnValue(false));
        $this->assertFalse($mock->checkStaging());
    }
}
