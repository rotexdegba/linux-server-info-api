<?php
namespace Lsia;

use RKA\Middleware\IpAddress;
use Psr\Http\Message\ServerRequestInterface;

/**
 * Description of ClientIpDetector
 *
 * @author rotimi
 */
class ClientIpDetector extends IpAddress {
    
    public function getDetectedIp(ServerRequestInterface $request) {
        
        return $this->determineClientIpAddress($request);
    }
}
