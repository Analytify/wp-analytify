<?php
/*
 * Copyright 2010 Google Inc.
 *
 * Licensed under the Apache License, Version 2.0 (the "License"); you may not
 * use this file except in compliance with the License. You may obtain a copy of
 * the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS, WITHOUT
 * WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied. See the
 * License for the specific language governing permissions and limitations under
 * the License.
 */

/**
 * Service definition for Urlshortener (v1).
 *
 * <p>
 * Lets you create, inspect, and manage goo.gl short URLs
 * </p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="http://code.google.com/apis/urlshortener/v1/getting_started.html" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Analytify_Google_Service_Urlshortener extends Analytify_Google_Service
{
  /** Manage your goo.gl short URLs. */
  const URLSHORTENER = "https://www.googleapis.com/auth/urlshortener";

  public $url;
  

  /**
   * Constructs the internal representation of the Urlshortener service.
   *
   * @param Analytify_Google_Client $client
   */
  public function __construct(Analytify_Google_Client $client)
  {
    parent::__construct($client);
    $this->servicePath = 'urlshortener/v1/';
    $this->version = 'v1';
    $this->serviceName = 'urlshortener';

    $this->url = new Analytify_Google_Service_Urlshortener_Url_Resource(
        $this,
        $this->serviceName,
        'url',
        array(
          'methods' => array(
            'get' => array(
              'path' => 'url',
              'httpMethod' => 'GET',
              'parameters' => array(
                'shortUrl' => array(
                  'location' => 'query',
                  'type' => 'string',
                  'required' => true,
                ),
                'projection' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
              ),
            ),'insert' => array(
              'path' => 'url',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'list' => array(
              'path' => 'url/history',
              'httpMethod' => 'GET',
              'parameters' => array(
                'start-token' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'projection' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
              ),
            ),
          )
        )
    );
  }
}


/**
 * The "url" collection of methods.
 * Typical usage is:
 *  <code>
 *   $urlshortenerService = new Analytify_Google_Service_Urlshortener(...);
 *   $url = $urlshortenerService->url;
 *  </code>
 */
class Analytify_Google_Service_Urlshortener_Url_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Expands a short URL or gets creation time and analytics. (url.get)
   *
   * @param string $shortUrl
   * The short URL, including the protocol.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string projection
   * Additional information to return.
   * @return Analytify_Google_Service_Urlshortener_Url
   */
  public function get($shortUrl, $optParams = array())
  {
    $params = array('shortUrl' => $shortUrl);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Analytify_Google_Service_Urlshortener_Url");
  }
  /**
   * Creates a new short URL. (url.insert)
   *
   * @param Analytify_Google_Url $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Urlshortener_Url
   */
  public function insert(Analytify_Google_Service_Urlshortener_Url $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('insert', array($params), "Analytify_Google_Service_Urlshortener_Url");
  }
  /**
   * Retrieves a list of URLs shortened by a user. (url.listUrl)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string start-token
   * Token for requesting successive pages of results.
   * @opt_param string projection
   * Additional information to return.
   * @return Analytify_Google_Service_Urlshortener_UrlHistory
   */
  public function listUrl($optParams = array())
  {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Analytify_Google_Service_Urlshortener_UrlHistory");
  }
}




class Analytify_Google_Service_Urlshortener_AnalyticsSnapshot extends Analytify_Google_Collection
{
  protected $browsersType = 'Analytify_Google_Service_Urlshortener_StringCount';
  protected $browsersDataType = 'array';
  protected $countriesType = 'Analytify_Google_Service_Urlshortener_StringCount';
  protected $countriesDataType = 'array';
  public $longUrlClicks;
  protected $platformsType = 'Analytify_Google_Service_Urlshortener_StringCount';
  protected $platformsDataType = 'array';
  protected $referrersType = 'Analytify_Google_Service_Urlshortener_StringCount';
  protected $referrersDataType = 'array';
  public $shortUrlClicks;

  public function setBrowsers($browsers)
  {
    $this->browsers = $browsers;
  }

  public function getBrowsers()
  {
    return $this->browsers;
  }

  public function setCountries($countries)
  {
    $this->countries = $countries;
  }

  public function getCountries()
  {
    return $this->countries;
  }

  public function setLongUrlClicks($longUrlClicks)
  {
    $this->longUrlClicks = $longUrlClicks;
  }

  public function getLongUrlClicks()
  {
    return $this->longUrlClicks;
  }

  public function setPlatforms($platforms)
  {
    $this->platforms = $platforms;
  }

  public function getPlatforms()
  {
    return $this->platforms;
  }

  public function setReferrers($referrers)
  {
    $this->referrers = $referrers;
  }

  public function getReferrers()
  {
    return $this->referrers;
  }

  public function setShortUrlClicks($shortUrlClicks)
  {
    $this->shortUrlClicks = $shortUrlClicks;
  }

  public function getShortUrlClicks()
  {
    return $this->shortUrlClicks;
  }
}

class Analytify_Google_Service_Urlshortener_AnalyticsSummary extends Analytify_Google_Model
{
  protected $allTimeType = 'Analytify_Google_Service_Urlshortener_AnalyticsSnapshot';
  protected $allTimeDataType = '';
  protected $dayType = 'Analytify_Google_Service_Urlshortener_AnalyticsSnapshot';
  protected $dayDataType = '';
  protected $monthType = 'Analytify_Google_Service_Urlshortener_AnalyticsSnapshot';
  protected $monthDataType = '';
  protected $twoHoursType = 'Analytify_Google_Service_Urlshortener_AnalyticsSnapshot';
  protected $twoHoursDataType = '';
  protected $weekType = 'Analytify_Google_Service_Urlshortener_AnalyticsSnapshot';
  protected $weekDataType = '';

  public function setAllTime(Analytify_Google_Service_Urlshortener_AnalyticsSnapshot $allTime)
  {
    $this->allTime = $allTime;
  }

  public function getAllTime()
  {
    return $this->allTime;
  }

  public function setDay(Analytify_Google_Service_Urlshortener_AnalyticsSnapshot $day)
  {
    $this->day = $day;
  }

  public function getDay()
  {
    return $this->day;
  }

  public function setMonth(Analytify_Google_Service_Urlshortener_AnalyticsSnapshot $month)
  {
    $this->month = $month;
  }

  public function getMonth()
  {
    return $this->month;
  }

  public function setTwoHours(Analytify_Google_Service_Urlshortener_AnalyticsSnapshot $twoHours)
  {
    $this->twoHours = $twoHours;
  }

  public function getTwoHours()
  {
    return $this->twoHours;
  }

  public function setWeek(Analytify_Google_Service_Urlshortener_AnalyticsSnapshot $week)
  {
    $this->week = $week;
  }

  public function getWeek()
  {
    return $this->week;
  }
}

class Analytify_Google_Service_Urlshortener_StringCount extends Analytify_Google_Model
{
  public $count;
  public $id;

  public function setCount($count)
  {
    $this->count = $count;
  }

  public function getCount()
  {
    return $this->count;
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }
}

class Analytify_Google_Service_Urlshortener_Url extends Analytify_Google_Model
{
  protected $analyticsType = 'Analytify_Google_Service_Urlshortener_AnalyticsSummary';
  protected $analyticsDataType = '';
  public $created;
  public $id;
  public $kind;
  public $longUrl;
  public $status;

  public function setAnalytics(Analytify_Google_Service_Urlshortener_AnalyticsSummary $analytics)
  {
    $this->analytics = $analytics;
  }

  public function getAnalytics()
  {
    return $this->analytics;
  }

  public function setCreated($created)
  {
    $this->created = $created;
  }

  public function getCreated()
  {
    return $this->created;
  }

  public function setId($id)
  {
    $this->id = $id;
  }

  public function getId()
  {
    return $this->id;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setLongUrl($longUrl)
  {
    $this->longUrl = $longUrl;
  }

  public function getLongUrl()
  {
    return $this->longUrl;
  }

  public function setStatus($status)
  {
    $this->status = $status;
  }

  public function getStatus()
  {
    return $this->status;
  }
}

class Analytify_Google_Service_Urlshortener_UrlHistory extends Analytify_Google_Collection
{
  protected $itemsType = 'Analytify_Google_Service_Urlshortener_Url';
  protected $itemsDataType = 'array';
  public $itemsPerPage;
  public $kind;
  public $nextPageToken;
  public $totalItems;

  public function setItems($items)
  {
    $this->items = $items;
  }

  public function getItems()
  {
    return $this->items;
  }

  public function setItemsPerPage($itemsPerPage)
  {
    $this->itemsPerPage = $itemsPerPage;
  }

  public function getItemsPerPage()
  {
    return $this->itemsPerPage;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }

  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }

  public function setTotalItems($totalItems)
  {
    $this->totalItems = $totalItems;
  }

  public function getTotalItems()
  {
    return $this->totalItems;
  }
}
