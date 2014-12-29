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
 * Service definition for Reseller (v1).
 *
 * <p>
 * Lets you create and manage your customers and their subscriptions.
 * </p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/google-apps/reseller/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Analytify_Google_Service_Reseller extends Analytify_Google_Service
{
  /** Manage users on your domain. */
  const APPS_ORDER = "https://www.googleapis.com/auth/apps.order";
  /** Manage users on your domain. */
  const APPS_ORDER_READONLY = "https://www.googleapis.com/auth/apps.order.readonly";

  public $customers;
  public $subscriptions;
  

  /**
   * Constructs the internal representation of the Reseller service.
   *
   * @param Analytify_Google_Client $client
   */
  public function __construct(Analytify_Google_Client $client)
  {
    parent::__construct($client);
    $this->servicePath = 'apps/reseller/v1/';
    $this->version = 'v1';
    $this->serviceName = 'reseller';

    $this->customers = new Analytify_Google_Service_Reseller_Customers_Resource(
        $this,
        $this->serviceName,
        'customers',
        array(
          'methods' => array(
            'get' => array(
              'path' => 'customers/{customerId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'customerId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'insert' => array(
              'path' => 'customers',
              'httpMethod' => 'POST',
              'parameters' => array(
                'customerAuthToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
              ),
            ),'patch' => array(
              'path' => 'customers/{customerId}',
              'httpMethod' => 'PATCH',
              'parameters' => array(
                'customerId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'update' => array(
              'path' => 'customers/{customerId}',
              'httpMethod' => 'PUT',
              'parameters' => array(
                'customerId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),
          )
        )
    );
    $this->subscriptions = new Analytify_Google_Service_Reseller_Subscriptions_Resource(
        $this,
        $this->serviceName,
        'subscriptions',
        array(
          'methods' => array(
            'changePlan' => array(
              'path' => 'customers/{customerId}/subscriptions/{subscriptionId}/changePlan',
              'httpMethod' => 'POST',
              'parameters' => array(
                'customerId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'subscriptionId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'changeRenewalSettings' => array(
              'path' => 'customers/{customerId}/subscriptions/{subscriptionId}/changeRenewalSettings',
              'httpMethod' => 'POST',
              'parameters' => array(
                'customerId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'subscriptionId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'changeSeats' => array(
              'path' => 'customers/{customerId}/subscriptions/{subscriptionId}/changeSeats',
              'httpMethod' => 'POST',
              'parameters' => array(
                'customerId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'subscriptionId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'delete' => array(
              'path' => 'customers/{customerId}/subscriptions/{subscriptionId}',
              'httpMethod' => 'DELETE',
              'parameters' => array(
                'customerId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'subscriptionId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'deletionType' => array(
                  'location' => 'query',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'get' => array(
              'path' => 'customers/{customerId}/subscriptions/{subscriptionId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'customerId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'subscriptionId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'insert' => array(
              'path' => 'customers/{customerId}/subscriptions',
              'httpMethod' => 'POST',
              'parameters' => array(
                'customerId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'customerAuthToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
              ),
            ),'list' => array(
              'path' => 'subscriptions',
              'httpMethod' => 'GET',
              'parameters' => array(
                'customerAuthToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'customerId' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'maxResults' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
                'customerNamePrefix' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
              ),
            ),'startPaidService' => array(
              'path' => 'customers/{customerId}/subscriptions/{subscriptionId}/startPaidService',
              'httpMethod' => 'POST',
              'parameters' => array(
                'customerId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'subscriptionId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),
          )
        )
    );
  }
}


/**
 * The "customers" collection of methods.
 * Typical usage is:
 *  <code>
 *   $resellerService = new Analytify_Google_Service_Reseller(...);
 *   $customers = $resellerService->customers;
 *  </code>
 */
class Analytify_Google_Service_Reseller_Customers_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Gets a customer resource if one exists and is owned by the reseller.
   * (customers.get)
   *
   * @param string $customerId
   * Id of the Customer
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Reseller_Customer
   */
  public function get($customerId, $optParams = array())
  {
    $params = array('customerId' => $customerId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Analytify_Google_Service_Reseller_Customer");
  }
  /**
   * Creates a customer resource if one does not already exist. (customers.insert)
   *
   * @param Analytify_Google_Customer $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string customerAuthToken
   * An auth token needed for inserting a customer for which domain already exists. Can be generated
    * at https://www.google.com/a/cpanel//TransferToken. Optional.
   * @return Analytify_Google_Service_Reseller_Customer
   */
  public function insert(Analytify_Google_Service_Reseller_Customer $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('insert', array($params), "Analytify_Google_Service_Reseller_Customer");
  }
  /**
   * Update a customer resource if one it exists and is owned by the reseller.
   * This method supports patch semantics. (customers.patch)
   *
   * @param string $customerId
   * Id of the Customer
   * @param Analytify_Google_Customer $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Reseller_Customer
   */
  public function patch($customerId, Analytify_Google_Service_Reseller_Customer $postBody, $optParams = array())
  {
    $params = array('customerId' => $customerId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('patch', array($params), "Analytify_Google_Service_Reseller_Customer");
  }
  /**
   * Update a customer resource if one it exists and is owned by the reseller.
   * (customers.update)
   *
   * @param string $customerId
   * Id of the Customer
   * @param Analytify_Google_Customer $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Reseller_Customer
   */
  public function update($customerId, Analytify_Google_Service_Reseller_Customer $postBody, $optParams = array())
  {
    $params = array('customerId' => $customerId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('update', array($params), "Analytify_Google_Service_Reseller_Customer");
  }
}

/**
 * The "subscriptions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $resellerService = new Analytify_Google_Service_Reseller(...);
 *   $subscriptions = $resellerService->subscriptions;
 *  </code>
 */
class Analytify_Google_Service_Reseller_Subscriptions_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Changes the plan of a subscription (subscriptions.changePlan)
   *
   * @param string $customerId
   * Id of the Customer
   * @param string $subscriptionId
   * Id of the subscription, which is unique for a customer
   * @param Analytify_Google_ChangePlanRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Reseller_Subscription
   */
  public function changePlan($customerId, $subscriptionId, Analytify_Google_Service_Reseller_ChangePlanRequest $postBody, $optParams = array())
  {
    $params = array('customerId' => $customerId, 'subscriptionId' => $subscriptionId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('changePlan', array($params), "Analytify_Google_Service_Reseller_Subscription");
  }
  /**
   * Changes the renewal settings of a subscription
   * (subscriptions.changeRenewalSettings)
   *
   * @param string $customerId
   * Id of the Customer
   * @param string $subscriptionId
   * Id of the subscription, which is unique for a customer
   * @param Analytify_Google_RenewalSettings $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Reseller_Subscription
   */
  public function changeRenewalSettings($customerId, $subscriptionId, Analytify_Google_Service_Reseller_RenewalSettings $postBody, $optParams = array())
  {
    $params = array('customerId' => $customerId, 'subscriptionId' => $subscriptionId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('changeRenewalSettings', array($params), "Analytify_Google_Service_Reseller_Subscription");
  }
  /**
   * Changes the seats configuration of a subscription (subscriptions.changeSeats)
   *
   * @param string $customerId
   * Id of the Customer
   * @param string $subscriptionId
   * Id of the subscription, which is unique for a customer
   * @param Analytify_Google_Seats $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Reseller_Subscription
   */
  public function changeSeats($customerId, $subscriptionId, Analytify_Google_Service_Reseller_Seats $postBody, $optParams = array())
  {
    $params = array('customerId' => $customerId, 'subscriptionId' => $subscriptionId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('changeSeats', array($params), "Analytify_Google_Service_Reseller_Subscription");
  }
  /**
   * Cancels/Downgrades a subscription. (subscriptions.delete)
   *
   * @param string $customerId
   * Id of the Customer
   * @param string $subscriptionId
   * Id of the subscription, which is unique for a customer
   * @param string $deletionType
   * Whether the subscription is to be fully cancelled or downgraded
   * @param array $optParams Optional parameters.
   */
  public function delete($customerId, $subscriptionId, $deletionType, $optParams = array())
  {
    $params = array('customerId' => $customerId, 'subscriptionId' => $subscriptionId, 'deletionType' => $deletionType);
    $params = array_merge($params, $optParams);
    return $this->call('delete', array($params));
  }
  /**
   * Gets a subscription of the customer. (subscriptions.get)
   *
   * @param string $customerId
   * Id of the Customer
   * @param string $subscriptionId
   * Id of the subscription, which is unique for a customer
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Reseller_Subscription
   */
  public function get($customerId, $subscriptionId, $optParams = array())
  {
    $params = array('customerId' => $customerId, 'subscriptionId' => $subscriptionId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Analytify_Google_Service_Reseller_Subscription");
  }
  /**
   * Creates/Transfers a subscription for the customer. (subscriptions.insert)
   *
   * @param string $customerId
   * Id of the Customer
   * @param Analytify_Google_Subscription $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string customerAuthToken
   * An auth token needed for transferring a subscription. Can be generated at
    * https://www.google.com/a/cpanel/customer-domain/TransferToken. Optional.
   * @return Analytify_Google_Service_Reseller_Subscription
   */
  public function insert($customerId, Analytify_Google_Service_Reseller_Subscription $postBody, $optParams = array())
  {
    $params = array('customerId' => $customerId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('insert', array($params), "Analytify_Google_Service_Reseller_Subscription");
  }
  /**
   * Lists subscriptions of a reseller, optionally filtered by a customer name
   * prefix. (subscriptions.listSubscriptions)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string customerAuthToken
   * An auth token needed if the customer is not a resold customer of this reseller. Can be generated
    * at https://www.google.com/a/cpanel/customer-domain/TransferToken.Optional.
   * @opt_param string pageToken
   * Token to specify next page in the list
   * @opt_param string customerId
   * Id of the Customer
   * @opt_param string maxResults
   * Maximum number of results to return
   * @opt_param string customerNamePrefix
   * Prefix of the customer's domain name by which the subscriptions should be filtered. Optional
   * @return Analytify_Google_Service_Reseller_Subscriptions
   */
  public function listSubscriptions($optParams = array())
  {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Analytify_Google_Service_Reseller_Subscriptions");
  }
  /**
   * Starts paid service of a trial subscription (subscriptions.startPaidService)
   *
   * @param string $customerId
   * Id of the Customer
   * @param string $subscriptionId
   * Id of the subscription, which is unique for a customer
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Reseller_Subscription
   */
  public function startPaidService($customerId, $subscriptionId, $optParams = array())
  {
    $params = array('customerId' => $customerId, 'subscriptionId' => $subscriptionId);
    $params = array_merge($params, $optParams);
    return $this->call('startPaidService', array($params), "Analytify_Google_Service_Reseller_Subscription");
  }
}




class Analytify_Google_Service_Reseller_Address extends Analytify_Google_Model
{
  public $addressLine1;
  public $addressLine2;
  public $addressLine3;
  public $contactName;
  public $countryCode;
  public $kind;
  public $locality;
  public $organizationName;
  public $postalCode;
  public $region;

  public function setAddressLine1($addressLine1)
  {
    $this->addressLine1 = $addressLine1;
  }

  public function getAddressLine1()
  {
    return $this->addressLine1;
  }

  public function setAddressLine2($addressLine2)
  {
    $this->addressLine2 = $addressLine2;
  }

  public function getAddressLine2()
  {
    return $this->addressLine2;
  }

  public function setAddressLine3($addressLine3)
  {
    $this->addressLine3 = $addressLine3;
  }

  public function getAddressLine3()
  {
    return $this->addressLine3;
  }

  public function setContactName($contactName)
  {
    $this->contactName = $contactName;
  }

  public function getContactName()
  {
    return $this->contactName;
  }

  public function setCountryCode($countryCode)
  {
    $this->countryCode = $countryCode;
  }

  public function getCountryCode()
  {
    return $this->countryCode;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setLocality($locality)
  {
    $this->locality = $locality;
  }

  public function getLocality()
  {
    return $this->locality;
  }

  public function setOrganizationName($organizationName)
  {
    $this->organizationName = $organizationName;
  }

  public function getOrganizationName()
  {
    return $this->organizationName;
  }

  public function setPostalCode($postalCode)
  {
    $this->postalCode = $postalCode;
  }

  public function getPostalCode()
  {
    return $this->postalCode;
  }

  public function setRegion($region)
  {
    $this->region = $region;
  }

  public function getRegion()
  {
    return $this->region;
  }
}

class Analytify_Google_Service_Reseller_ChangePlanRequest extends Analytify_Google_Model
{
  public $kind;
  public $planName;
  public $purchaseOrderId;
  protected $seatsType = 'Analytify_Google_Service_Reseller_Seats';
  protected $seatsDataType = '';

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setPlanName($planName)
  {
    $this->planName = $planName;
  }

  public function getPlanName()
  {
    return $this->planName;
  }

  public function setPurchaseOrderId($purchaseOrderId)
  {
    $this->purchaseOrderId = $purchaseOrderId;
  }

  public function getPurchaseOrderId()
  {
    return $this->purchaseOrderId;
  }

  public function setSeats(Analytify_Google_Service_Reseller_Seats $seats)
  {
    $this->seats = $seats;
  }

  public function getSeats()
  {
    return $this->seats;
  }
}

class Analytify_Google_Service_Reseller_Customer extends Analytify_Google_Model
{
  public $alternateEmail;
  public $customerDomain;
  public $customerId;
  public $kind;
  public $phoneNumber;
  protected $postalAddressType = 'Analytify_Google_Service_Reseller_Address';
  protected $postalAddressDataType = '';
  public $resourceUiUrl;

  public function setAlternateEmail($alternateEmail)
  {
    $this->alternateEmail = $alternateEmail;
  }

  public function getAlternateEmail()
  {
    return $this->alternateEmail;
  }

  public function setCustomerDomain($customerDomain)
  {
    $this->customerDomain = $customerDomain;
  }

  public function getCustomerDomain()
  {
    return $this->customerDomain;
  }

  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }

  public function getCustomerId()
  {
    return $this->customerId;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setPhoneNumber($phoneNumber)
  {
    $this->phoneNumber = $phoneNumber;
  }

  public function getPhoneNumber()
  {
    return $this->phoneNumber;
  }

  public function setPostalAddress(Analytify_Google_Service_Reseller_Address $postalAddress)
  {
    $this->postalAddress = $postalAddress;
  }

  public function getPostalAddress()
  {
    return $this->postalAddress;
  }

  public function setResourceUiUrl($resourceUiUrl)
  {
    $this->resourceUiUrl = $resourceUiUrl;
  }

  public function getResourceUiUrl()
  {
    return $this->resourceUiUrl;
  }
}

class Analytify_Google_Service_Reseller_RenewalSettings extends Analytify_Google_Model
{
  public $kind;
  public $renewalType;

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setRenewalType($renewalType)
  {
    $this->renewalType = $renewalType;
  }

  public function getRenewalType()
  {
    return $this->renewalType;
  }
}

class Analytify_Google_Service_Reseller_Seats extends Analytify_Google_Model
{
  public $kind;
  public $maximumNumberOfSeats;
  public $numberOfSeats;

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setMaximumNumberOfSeats($maximumNumberOfSeats)
  {
    $this->maximumNumberOfSeats = $maximumNumberOfSeats;
  }

  public function getMaximumNumberOfSeats()
  {
    return $this->maximumNumberOfSeats;
  }

  public function setNumberOfSeats($numberOfSeats)
  {
    $this->numberOfSeats = $numberOfSeats;
  }

  public function getNumberOfSeats()
  {
    return $this->numberOfSeats;
  }
}

class Analytify_Google_Service_Reseller_Subscription extends Analytify_Google_Model
{
  public $creationTime;
  public $customerId;
  public $kind;
  protected $planType = 'Analytify_Google_Service_Reseller_SubscriptionPlan';
  protected $planDataType = '';
  public $purchaseOrderId;
  protected $renewalSettingsType = 'Analytify_Google_Service_Reseller_RenewalSettings';
  protected $renewalSettingsDataType = '';
  public $resourceUiUrl;
  protected $seatsType = 'Analytify_Google_Service_Reseller_Seats';
  protected $seatsDataType = '';
  public $skuId;
  public $status;
  public $subscriptionId;
  protected $transferInfoType = 'Analytify_Google_Service_Reseller_SubscriptionTransferInfo';
  protected $transferInfoDataType = '';
  protected $trialSettingsType = 'Analytify_Google_Service_Reseller_SubscriptionTrialSettings';
  protected $trialSettingsDataType = '';

  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }

  public function getCreationTime()
  {
    return $this->creationTime;
  }

  public function setCustomerId($customerId)
  {
    $this->customerId = $customerId;
  }

  public function getCustomerId()
  {
    return $this->customerId;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setPlan(Analytify_Google_Service_Reseller_SubscriptionPlan $plan)
  {
    $this->plan = $plan;
  }

  public function getPlan()
  {
    return $this->plan;
  }

  public function setPurchaseOrderId($purchaseOrderId)
  {
    $this->purchaseOrderId = $purchaseOrderId;
  }

  public function getPurchaseOrderId()
  {
    return $this->purchaseOrderId;
  }

  public function setRenewalSettings(Analytify_Google_Service_Reseller_RenewalSettings $renewalSettings)
  {
    $this->renewalSettings = $renewalSettings;
  }

  public function getRenewalSettings()
  {
    return $this->renewalSettings;
  }

  public function setResourceUiUrl($resourceUiUrl)
  {
    $this->resourceUiUrl = $resourceUiUrl;
  }

  public function getResourceUiUrl()
  {
    return $this->resourceUiUrl;
  }

  public function setSeats(Analytify_Google_Service_Reseller_Seats $seats)
  {
    $this->seats = $seats;
  }

  public function getSeats()
  {
    return $this->seats;
  }

  public function setSkuId($skuId)
  {
    $this->skuId = $skuId;
  }

  public function getSkuId()
  {
    return $this->skuId;
  }

  public function setStatus($status)
  {
    $this->status = $status;
  }

  public function getStatus()
  {
    return $this->status;
  }

  public function setSubscriptionId($subscriptionId)
  {
    $this->subscriptionId = $subscriptionId;
  }

  public function getSubscriptionId()
  {
    return $this->subscriptionId;
  }

  public function setTransferInfo(Analytify_Google_Service_Reseller_SubscriptionTransferInfo $transferInfo)
  {
    $this->transferInfo = $transferInfo;
  }

  public function getTransferInfo()
  {
    return $this->transferInfo;
  }

  public function setTrialSettings(Analytify_Google_Service_Reseller_SubscriptionTrialSettings $trialSettings)
  {
    $this->trialSettings = $trialSettings;
  }

  public function getTrialSettings()
  {
    return $this->trialSettings;
  }
}

class Analytify_Google_Service_Reseller_SubscriptionPlan extends Analytify_Google_Model
{
  protected $commitmentIntervalType = 'Analytify_Google_Service_Reseller_SubscriptionPlanCommitmentInterval';
  protected $commitmentIntervalDataType = '';
  public $isCommitmentPlan;
  public $planName;

  public function setCommitmentInterval(Analytify_Google_Service_Reseller_SubscriptionPlanCommitmentInterval $commitmentInterval)
  {
    $this->commitmentInterval = $commitmentInterval;
  }

  public function getCommitmentInterval()
  {
    return $this->commitmentInterval;
  }

  public function setIsCommitmentPlan($isCommitmentPlan)
  {
    $this->isCommitmentPlan = $isCommitmentPlan;
  }

  public function getIsCommitmentPlan()
  {
    return $this->isCommitmentPlan;
  }

  public function setPlanName($planName)
  {
    $this->planName = $planName;
  }

  public function getPlanName()
  {
    return $this->planName;
  }
}

class Analytify_Google_Service_Reseller_SubscriptionPlanCommitmentInterval extends Analytify_Google_Model
{
  public $endTime;
  public $startTime;

  public function setEndTime($endTime)
  {
    $this->endTime = $endTime;
  }

  public function getEndTime()
  {
    return $this->endTime;
  }

  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }

  public function getStartTime()
  {
    return $this->startTime;
  }
}

class Analytify_Google_Service_Reseller_SubscriptionTransferInfo extends Analytify_Google_Model
{
  public $minimumTransferableSeats;
  public $transferabilityExpirationTime;

  public function setMinimumTransferableSeats($minimumTransferableSeats)
  {
    $this->minimumTransferableSeats = $minimumTransferableSeats;
  }

  public function getMinimumTransferableSeats()
  {
    return $this->minimumTransferableSeats;
  }

  public function setTransferabilityExpirationTime($transferabilityExpirationTime)
  {
    $this->transferabilityExpirationTime = $transferabilityExpirationTime;
  }

  public function getTransferabilityExpirationTime()
  {
    return $this->transferabilityExpirationTime;
  }
}

class Analytify_Google_Service_Reseller_SubscriptionTrialSettings extends Analytify_Google_Model
{
  public $isInTrial;
  public $trialEndTime;

  public function setIsInTrial($isInTrial)
  {
    $this->isInTrial = $isInTrial;
  }

  public function getIsInTrial()
  {
    return $this->isInTrial;
  }

  public function setTrialEndTime($trialEndTime)
  {
    $this->trialEndTime = $trialEndTime;
  }

  public function getTrialEndTime()
  {
    return $this->trialEndTime;
  }
}

class Analytify_Google_Service_Reseller_Subscriptions extends Analytify_Google_Collection
{
  public $kind;
  public $nextPageToken;
  protected $subscriptionsType = 'Analytify_Google_Service_Reseller_Subscription';
  protected $subscriptionsDataType = 'array';

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

  public function setSubscriptions($subscriptions)
  {
    $this->subscriptions = $subscriptions;
  }

  public function getSubscriptions()
  {
    return $this->subscriptions;
  }
}
