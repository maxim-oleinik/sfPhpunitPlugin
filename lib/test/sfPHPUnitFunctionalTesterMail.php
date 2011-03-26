<?php

/**
 * Extended mail tester
 *
 * @author Max <maxim.olenik@gmail.com>
 */
class sfPHPUnitFunctionalTesterMail extends sfTesterMailer
{
    /**
     * Get mail fixtures directory
     *
     * @return string
     */
    public function getFixturesDir()
    {
        return sfConfig::get('sf_test_dir').'/fixtures/mail';
    }


    /**
     * Get expected sample mail to compare with actual
     *
     * @param  string $name - Sample file name
     * @return string
     */
    public function getSampleMessage($name)
    {
        $file = $this->getFixturesDir().'/'.$name;
        if (!file_exists($file)) {
            throw new Exception(__METHOD__.": mail template not found `{$file}`");
        }
        return file_get_contents($file);
    }


    /**
     * Compare sample mail with actual
     *
     * @param  string $sampleName    - Sample file name
     * @param  array  $replacements  - Sample mail replacements
     * @param  string $errorMessage
     */
    public function checkMessage($sampleName, array $replacements = array(), $errorMessage = null)
    {
        if (!$this->message) {
            throw new Exception(__METHOD__.": Message not found");
        }

        $expectedText = trim($this->getSampleMessage($sampleName));
        $replacements["\r"] = '';
        if ($replacements) {
            $expectedText = strtr($expectedText, $replacements);
        }

        $actualText = $this->_messagetoString($this->message);

        if ($expectedText !== $actualText) {
            $expectedFile = tempnam('/tmp', 'expected_');
            file_put_contents($expectedFile, $expectedText);
            $actualFile = tempnam('/tmp', 'actual_');
            file_put_contents($actualFile, $actualText);
            $diff = `colordiff {$expectedFile} {$actualFile}`;

            unlink($expectedFile);
            unlink($actualFile);

            $this->tester->fail($errorMessage."\nExpected (red) VS Actual (blue)\n".$diff);
        } else {
            $this->tester->pass($errorMessage);
        }

        return $this->getObjectToReturn();
    }


    /**
     * Convert Swift_Message to human readble string
     *
     * @param  Swift_Message $message
     * @return string
     */
    protected function _messagetoString(Swift_Message $message)
    {
        $result = $this->_headersToString($message);
        $body = rtrim($message->getBody());
        $result .= "\n\n".$body;

        return $result;
    }


    /**
     * Convert Swift_Message headers to human readble string
     *
     * @param  Swift_Message $message
     * @return string
     */
    protected function _headersToString(Swift_Message $message)
    {
        $headers = array();
        foreach ($message->getHeaders()->getAll() as $header) {
            switch ($header->getFieldType()) {
                case Swift_Mime_Header::TYPE_DATE:
                case Swift_Mime_Header::TYPE_ID:
                    continue 2;

                case Swift_Mime_Header::TYPE_TEXT:
                    $value = $header->getValue();
                    break;

                case Swift_Mime_Header::TYPE_MAILBOX:
                    $emails = array();
                    foreach ($header->getFieldBodyModel() as $email => $name) {
                        if (strlen($name)) {
                            $emails[] = sprintf('"%s" <%s>', $name, $email);
                        } else {
                            $emails[] = $email;
                        }
                    }
                    $value = implode(', ', $emails);
                    break;

                default:
                    $value = $header->getFieldBody();
                    break;
            }
            $headers[$header->getFieldName()] = $value;
        }

        $this->_sortHeaders($headers);
        $result = '';
        foreach ($headers as $name => $value) {
            $result .= sprintf("%s: %s\n", $name, $value);
        }

        return $result;
    }


    /**
     * Sort headers with predefined order
     *
     * @param  array &$headers
     * @return void
     */
    protected function _sortHeaders(array &$headers)
    {
        $order = array(
            'Subject'      => null,
            'From'         => null,
            'To'           => null,
            'Reply-To'     => null,
            'MIME-Version' => null,
            'Content-Type' => null,
            'Content-Transfer-Encoding' => null,
        );
        $order = array_intersect_key($order, $headers);
        $headers = array_merge($order, $headers);
    }
}
