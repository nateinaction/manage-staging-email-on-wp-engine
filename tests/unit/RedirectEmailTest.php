<?php

namespace nategay\manage_staging_email_wpe;

class RedirectEmailTest extends \PHPUnit\Framework\TestCase
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
            ->setMethods(array('getPreferredAddress', 'logWhenEmailManaged'))
            ->getMock();
        $mock->method('logWhenEmailManaged')->will($this->returnValue(null));
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

    /**
     * Test standard message sent to log when email is redirected or halted.
     *
     * @param $past_tense_action string 
     *
     * @dataProvider dataLogWhenEmailManaged
     */
    public function testLogWhenEmailManaged($past_tense_action, $expect)
    {
        $mock = $this->getMockBuilder('nategay\manage_staging_email_wpe\RedirectEmail')
            ->setMethods(array('sendToErrorLog'))
            ->getMock();
        $mock->method('sendToErrorLog')->will($this->returnValue(null));

        $this->assertEquals($mock->logWhenEmailManaged($past_tense_action), $expect);
    }

    public function dataLogWhenEmailManaged()
    {
        return array(
            array(
                'sent to PHP Error',
                'Email sent to PHP Error by the Manage Staging Email on WP Engine plugin.',
            ),
        );
    }
}
