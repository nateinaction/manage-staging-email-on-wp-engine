<?php

namespace nategay\manage_staging_email_wpe;

class RedirectEmailTest extends \PHPUnit_Framework_TestCase
{
    /**
     * Test that headers are correctly set when redirecting email to another address
     *
     * @param array $mail_args Array of settings for sending the message.
     *
     * @dataProvider dataSendToAddress
     */
    public function testSendToAddress($mail_args, $expect)
    {
        $mock = $this->getMockBuilder('nategay\manage_staging_email_wpe\RedirectEmail')
            ->setMethods(array('getPreferredAddress'))
            ->getMock();

        $mock->method('getPreferredAddress')
            ->will($this->returnValue('custom@address.com'));

        $this->assertEquals($mock->sendToAddress($mail_args), $expect);
    }

    public function dataSendToAddress()
    {
        return array(
            array(
                array(
                    'to' => 'original@email.com',
                    'headers' => array(
                        'cc' => 'coppied@address.com',
                        'bcc' => 'blind@address.com',
                    ),
                    'body' => 'This is a test email',
                ),
                array(
                    'to' => 'custom@address.com',
                    'headers' => array(),
                    'body' => 'This is a test email',
                ),
            ),
        );
    }
}
