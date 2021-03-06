<?php

namespace Pecee\Http\Service;

use Pecee\Http\Rest\RestBase;

class Tinder extends RestBase
{

    protected $serviceUrl = 'https://api.gotinder.com/';

    protected $fbUserId;
    protected $fbToken;
    protected $authToken;
    protected $user;

    public function __construct($facebookUserId, $facebookToken)
    {
        parent::__construct();
        $this->fbUserId = $facebookUserId;
        $this->fbToken = $facebookToken;

        $this->authenticate();
    }

    /**
     * @param null $url
     * @param string $method
     * @param array $data
     * @return Object
     * @throws \Pecee\Http\Rest\RestException
     */
    public function api($url = null, $method = self::METHOD_GET, array $data = [])
    {

        $this->httpRequest->setOptions([
            CURLOPT_SSL_VERIFYHOST => false,
            CURLOPT_SSL_VERIFYPEER => false,
        ]);

        $this->httpRequest->setPostJson(true);

        $this->httpRequest->setHeaders([
            'X-Auth-Token: ' . $this->authToken,
            'Content-type: application/json',
            'app_version: 3',
            'platform: ios',
            'User-Agent: Tinder/3.0.4 (iPhone; iOS 7.1; Scale/2.00)',
            'os_version: 700001',
        ]);

        return json_decode(parent::api($url, $method, $data)->getResponse());
    }

    protected function authenticate()
    {
        $response = $this->api('auth', self::METHOD_POST, ['facebook_token' => $this->fbToken, 'facebook_id' => (int)$this->fbUserId]);
        if ($response && isset($response->token)) {
            $this->authToken = $response->token;
            $this->user = $response->user;
        }
    }

    public function meta()
    {
        return $this->api('meta');
    }

    public function reportUser($userId, $causeId)
    {
        return $this->api('report/' . $userId, self::METHOD_POST, ['cause' => $causeId]);
    }

    public function updateProfile(array $data)
    {
        return $this->api('profile', self::METHOD_POST, $data);
    }

    public function sendMessage($userId, $message)
    {
        return $this->api('user/matches/' . $userId, self::METHOD_POST, ['message' => $message]);
    }

    public function updateLocation($lat, $lon)
    {
        return $this->api('user/ping', self::METHOD_POST, ['lat' => $lat, 'lon' => $lon]);
    }

    public function like($userId)
    {
        return $this->api('like/' . $userId);
    }

    public function superLike($userId)
    {
        return $this->api('like/' . $userId . '/super/');
    }

    public function pass($userId)
    {
        return $this->api('pass/' . $userId);
    }

    public function updates($lastActivityTime = '')
    {
        return $this->api('updates', self::METHOD_POST, ['last_activity_date' => $lastActivityTime]);
    }

    public function recommendations()
    {
        return $this->api('user/recs');
    }

    public function friends()
    {
        return $this->api('group/friends');
    }

    public function user($userId)
    {
        return $this->api('user/' . $userId);
    }

    /**
     * @return object|null
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return string
     */
    public function getAuthToken()
    {
        return $this->authToken;
    }

    /**
     * @return string
     */
    public function getFbToken()
    {
        return $this->fbToken;
    }

    /**
     * @return int
     */
    public function getFbUserId()
    {
        return $this->fbUserId;
    }

}