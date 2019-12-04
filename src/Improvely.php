<?php

namespace LukaPeharda\Improvely;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;

use LukaPeharda\Improvely\Errors\BadRequest;
use LukaPeharda\Improvely\Errors\MissingProfile;
use LukaPeharda\Improvely\Errors\MissingRequiredParameter;

/**
 * Class Improvely
 *
 * @property string $apiKey
 * @property string $apiEndpointUrl
 * @property string $projectId
 * @property GuzzleHttp\Client $client
 *
 * @package LukaPeharda\Improvely
 */
class Improvely
{
    const VERSION = '1.0.0';

    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';

    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var string
     */
    private $apiEndpointUrl = 'https://api.improvely.com/v1/';

    /**
     * @var string
     */
    private $projectId;

    /**
     * @var \GuzzleHttp\Client
     */
    private $client;

    /**
     * Init API key, project ID and HTTP client.
     *
     * @param   string  $apiKey
     * @param   string  $projectId
     *
     * @return  void
     */
    public function __construct($apiKey = null, $projectId = null)
    {
        // Initialize API key and project ID
        $this->apiKey = $apiKey;
        $this->projectId = $projectId;

        // Set up Guzzle HTTP client
        $this->client = new Client();
    }

    /**
     * Set API key.
     *
     * API key is set through constructor though there may be some scenarios
     * where one needs to set it up afterwards or event change it.
     *
     * @param   string  $apiKey
     *
     * @return  void
     */
    public function setApiKey($apiKey)
    {
        $this->apiKey = $apiKey;
    }

    /**
     * Return API key.
     *
     * @return  string
     */
    public function getApiKey()
    {
        return $this->apiKey;
    }

    /**
     * Set project ID.
     *
     * Project ID is set through constructor though there may be some scenarios
     * where one needs to set it up afterwards or event change it.
     *
     * @param   string  $projectId
     *
     * @return  void
     */
    public function setProjectId($projectId)
    {
        $this->projectId = $projectId;
    }

    /**
     * Return project ID.
     *
     * @return  string
     */
    public function getProjectId()
    {
        return $this->projectId;
    }

    /**
     * Do an HTTP request with given params.
     *
     * @param   string  $method
     * @param   string  $url
     * @param   array  $params
     *
     * @return  array
     */
    public function request($method, $url, $params = [])
    {
        // Merge API key and project ID
        // API key and project ID can be passed on as regular params and won't be overriden
        $params = $params + [
            'key' => $this->getApiKey(),
            'project' => $this->getProjectId()
        ];

        try {
            if ($method === self::METHOD_GET) {
                $response = $this->client->request($method, $this->formApiUrl($url, $params));
            } else if ($method === self::METHOD_POST) {
                $response = $this->client->request($method, $this->formApiUrl($url), [
                    'form_params' => $params,
                ]);
            }
        } catch (ClientException $exception) {
            throw $this->interceptException(
                $exception,
                $exception->hasResponse() ? $exception->getResponse() : null
            );
        }

        return $this->interpretResponse($response);
    }

    /**
     * Prepend API base URL. Append API token to the end of the URL.
     *
     * @param   string  $url
     * @param   array   $params
     *
     * @return  string
     */
    protected function formApiUrl($url, $params = [])
    {
        if (count($params) === 0) {
            return $this->apiEndpointUrl . $url . '.json';
        }

        return $this->apiEndpointUrl . $url . '.json?' .  http_build_query($params);
    }

    /**
     * Throw custom exceptions for some specialized cases.
     *
     * @param ClientException  $exception
     *
     * @return BadRequest
     */
    protected function interceptException(ClientException $exception, $response = null)
    {
        if ($response !== null) {
            $data = json_decode((string) $response->getBody(), $asAssoc = false);

            if ( ! isset($data->type)) {
                return new BadRequest($data->message);
            }

            switch ($data->type) {
                case 'parameters':
                    return new MissingRequiredParameter($data->message);
                case 'profile':
                    return new MissingProfile($data->message);
                default:
                    return new BadRequest($data->message);
            }
        }

        return new BadRequest($exception->getMessage());
    }

    /**
     * Parse and form response body.
     *
     * @param string  $body
     *
     * @return object
     */
    protected function interpretResponse($response)
    {
        $body = $response->getBody();
        $data = json_decode((string) $body->getContents(), $asAssoc = false);

        if ($data->status === 'error') {
            throw new BadRequest($data->message);
        }

        return $data;
    }
}
