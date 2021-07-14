<?php
namespace Riskified\Decider\Model\Api\Data;

use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Framework\HTTP\Header;
use Magento\Framework\Session\SessionManager;
use Riskified\Decider\Api\Data\SessionDetailsInterface;
use Riskified\Decider\Model\DateFormatter;

class SessionDetails implements SessionDetailsInterface
{
    /**
     * @var \Magento\Framework\Session\SessionManager
     */
    private $session;
    private $remoteAddress;
    private $mobileAgent;
    private $httpHeader;

    use DateFormatter;

    public function __construct(
        SessionManager $sessionManager,
        RemoteAddress $remoteAddress,
        Header $httpHeader
    ) {
        $this->session = $sessionManager;
        $this->remoteAddress = $remoteAddress;
        $this->httpHeader = $httpHeader;
    }

    /**
     * @return array
     */
    public function getData()
    {
        $userAgent = $this->httpHeader->getHttpUserAgent();
        $isMobile = \Zend_Http_UserAgent_Mobile::match($userAgent, $_SERVER);

        return [
            'created_at' => $this->formatDateAsIso8601(date('Y-m-d H:i:s')),
            'cart_token' => $this->session->getSessionId(),
            'browser_ip' => $this->remoteAddress->getRemoteAddress(),
            'source' => $isMobile ? 'mobile_web' : 'desktop_web'
        ];
    }

    /**
     * @return array
     */
    public function getCleanData()
    {
        return array_filter($this->getData(), 'strlen');
    }
}