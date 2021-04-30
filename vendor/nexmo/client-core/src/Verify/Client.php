<?php
/**
 * Nexmo Client Library for PHP
 *
 * @copyright Copyright (c) 2016 Nexmo, Inc. (http://nexmo.com)
 * @license   https://github.com/Nexmo/nexmo-php/blob/master/LICENSE.txt MIT License
 */

namespace Nexmo\Verify;

use Nexmo\Client\ClientAwareInterface;
use Nexmo\Client\ClientAwareTrait;
use Nexmo\Client\Exception;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Request;

class Client implements ClientAwareInterface
{
    use ClientAwareTrait;

    /**
     * @param array|Verification $verification
     */
    public function start($verification)
    {
        $verification = $this->createVerification($verification);

        $params = $verification->getRequestData(false);

        $request = $this->getRequest($params);
        $response = $this->client->send($request);

        $data = $this->processReqRes($verification, $request, $response, true);
        return $this->checkError($verification, $data);
    }

    /**
     * @param string|Verification $verification
     */
    public function search($verification)
    {
        $verification = $this->createVerification($verification);

        $params = [
            'request_id' => $verification->getRequestId()
        ];

        $request = $this->getRequest($params, 'search');
        $response = $this->client->send($request);

        $data = $this->processReqRes($verification, $request, $response, true);

        if (!isset($data['status'])) {
            $e = new Exception\Request('unexpected response from API');
            $e->setEntity($response);
            throw $e;
        }

        //verify API returns text status on success
        if (!is_numeric($data['status'])) {
            return $verification;
        }

        //normalize errors (client vrs server)
        if ('5' === $data['status']) {
            $e = new Exception\Server($data['error_text'], $data['status']);
            $e->setEntity($response);
        } else {
            $e = new Exception\Request($data['error_text'], $data['status']);
            $e->setEntity($response);
        }

        $e->setEntity($verification);
        throw $e;
    }

    public function cancel($verification)
    {
        return $this->control($verification, 'cancel');
    }

    public function trigger($verification)
    {
        return $this->control($verification, 'trigger_next_event');
    }

    public function check($verification, $code, $ip = null)
    {
        $verification = $this->createVerification($verification);

        $params = [
            'request_id' => $verification->getRequestId(),
            'code' => $code
        ];

        if (!is_null($ip)) {
            $params['ip'] = $ip;
        }

        $request = $this->getRequest($params, 'check');
        $response = $this->client->send($request);

        $data = $this->processReqRes($verification, $request, $response, false);
        return $this->checkError($verification, $data);
    }

    public function serialize(Verification $verification)
    {
        return serialize($verification);
    }

    public function unserialize($verification)
    {
        if (is_string($verification)) {
            $verification = unserialize($verification);
        }

        if (!($verification instanceof Verification)) {
            throw new \InvalidArgumentException('expected verification object or serialize verification object');
        }

        $verification->setClient($this);
        return $verification;
    }

    protected function control($verification, $cmd)
    {
        $verification = $this->createVerification($verification);

        $params = [
            'request_id' => $verification->getRequestId(),
            'cmd' => $cmd
        ];

        $request = $this->getRequest($params, 'control');
        $response = $this->client->send($request);

        $data = $this->processReqRes($verification, $request, $response, false);
        return $this->checkError($verification, $data);
    }

    protected function checkError(Verification $verification, $data)
    {
        if (!isset($data['status'])) {
            $e = new Exception\Request('unexpected response from API');
            $e->setEntity($data);
            throw $e;
        }

        //normalize errors (client vrs server)
        switch ($data['status']) {
            case '0':
                return $verification;
            case '5':
                $e = new Exception\Server($data['error_text'], $data['status']);
                $e->setEntity($data);
                break;
            default:
                $e = new Exception\Request($data['error_text'], $data['status']);
                $e->setEntity($data);
                break;
        }

        $e->setEntity($verification);
        throw $e;
    }

    protected function processReqRes(Verification $verification, RequestInterface $req, ResponseInterface $res, $replace = true)
    {
        $verification->setClient($this);

        if ($replace || !$verification->getRequest()) {
            $verification->setRequest($req);
        }

        if ($replace || !$verification->getResponse()) {
            $verification->setResponse($res);
            return $verification->getResponseData();
        }

        if ($res->getBody()->isSeekable()) {
            $res->getBody()->rewind();
        }

        return json_decode($res->getBody()->getContents(), true);
    }

    protected function getRequest($params, $path = null)
    {
        if (!is_null($path)) {
            $path = '/verify/' . $path . '/json';
        } else {
            $path = '/verify/json';
        }

        $request = new Request(
            $this->getClient()->getApiUrl() . $path,
            'POST',
            'php://temp',
            [
                'content-type' => 'application/json'
            ]
        );
        
        $request->getBody()->write(json_encode($params));
        return $request;
    }

    /**
     * Creates a verification object from a variety of sources
     *
     * @throws \RuntimeException
     * @return Verification
     */
    protected function createVerification($verification)
    {
        if ($verification instanceof Verification) {
            return $verification;
        }

        if (is_array($verification)) {
            return $this->createVerificationFromArray($verification);
        }

        if (\is_string($verification)) {
            return new Verification($verification);
        }

        throw new \RuntimeException('Unable to create Verification object from source data');
    }

    /**
     * @param $array
     * @return Verification
     */
    protected function createVerificationFromArray($array)
    {
        if (!is_array($array)) {
            throw new \RuntimeException('verification must implement `' . VerificationInterface::class . '` or be an array`');
        }

        foreach (['number', 'brand'] as $param) {
            if (!isset($array[$param])) {
                throw new \InvalidArgumentException('missing expected key `' . $param . '`');
            }
        }

        $number = $array['number'];
        $brand  = $array['brand'];

        unset($array['number']);
        unset($array['brand']);

        return new Verification($number, $brand, $array);
    }
}
