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
 * Service definition for Pubsub (v1beta1).
 *
 * <p>
 * Provides reliable, many-to-many, asynchronous messaging between applications.
 * </p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/pubsub/v1beta1" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Analytify_Google_Service_Pubsub extends Analytify_Google_Service
{
  /** View and manage your data across Google Cloud Platform services. */
  const CLOUD_PLATFORM = "https://www.googleapis.com/auth/cloud-platform";
  /** View and manage Pub/Sub topics and subscriptions. */
  const PUBSUB = "https://www.googleapis.com/auth/pubsub";

  public $subscriptions;
  public $topics;
  

  /**
   * Constructs the internal representation of the Pubsub service.
   *
   * @param Analytify_Google_Client $client
   */
  public function __construct(Analytify_Google_Client $client)
  {
    parent::__construct($client);
    $this->servicePath = 'pubsub/v1beta1/';
    $this->version = 'v1beta1';
    $this->serviceName = 'pubsub';

    $this->subscriptions = new Analytify_Google_Service_Pubsub_Subscriptions_Resource(
        $this,
        $this->serviceName,
        'subscriptions',
        array(
          'methods' => array(
            'acknowledge' => array(
              'path' => 'subscriptions/acknowledge',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'create' => array(
              'path' => 'subscriptions',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'delete' => array(
              'path' => 'subscriptions/{+subscription}',
              'httpMethod' => 'DELETE',
              'parameters' => array(
                'subscription' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'get' => array(
              'path' => 'subscriptions/{+subscription}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'subscription' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => 'subscriptions',
              'httpMethod' => 'GET',
              'parameters' => array(
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'maxResults' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
                'query' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
              ),
            ),'modifyAckDeadline' => array(
              'path' => 'subscriptions/modifyAckDeadline',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'modifyPushConfig' => array(
              'path' => 'subscriptions/modifyPushConfig',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'pull' => array(
              'path' => 'subscriptions/pull',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),
          )
        )
    );
    $this->topics = new Analytify_Google_Service_Pubsub_Topics_Resource(
        $this,
        $this->serviceName,
        'topics',
        array(
          'methods' => array(
            'create' => array(
              'path' => 'topics',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),'delete' => array(
              'path' => 'topics/{+topic}',
              'httpMethod' => 'DELETE',
              'parameters' => array(
                'topic' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'get' => array(
              'path' => 'topics/{+topic}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'topic' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => 'topics',
              'httpMethod' => 'GET',
              'parameters' => array(
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'maxResults' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
                'query' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
              ),
            ),'publish' => array(
              'path' => 'topics/publish',
              'httpMethod' => 'POST',
              'parameters' => array(),
            ),
          )
        )
    );
  }
}


/**
 * The "subscriptions" collection of methods.
 * Typical usage is:
 *  <code>
 *   $pubsubService = new Analytify_Google_Service_Pubsub(...);
 *   $subscriptions = $pubsubService->subscriptions;
 *  </code>
 */
class Analytify_Google_Service_Pubsub_Subscriptions_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Acknowledges a particular received message: the Pub/Sub system can remove the
   * given message from the subscription. Acknowledging a message whose Ack
   * deadline has expired may succeed, but the message could have been already
   * redelivered. Acknowledging a message more than once will not result in an
   * error. This is only used for messages received via pull.
   * (subscriptions.acknowledge)
   *
   * @param Analytify_Google_AcknowledgeRequest $postBody
   * @param array $optParams Optional parameters.
   */
  public function acknowledge(Analytify_Google_Service_Pubsub_AcknowledgeRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('acknowledge', array($params));
  }
  /**
   * Creates a subscription on a given topic for a given subscriber. If the
   * subscription already exists, returns ALREADY_EXISTS. If the corresponding
   * topic doesn't exist, returns NOT_FOUND. (subscriptions.create)
   *
   * @param Analytify_Google_Subscription $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Pubsub_Subscription
   */
  public function create(Analytify_Google_Service_Pubsub_Subscription $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('create', array($params), "Analytify_Google_Service_Pubsub_Subscription");
  }
  /**
   * Deletes an existing subscription. All pending messages in the subscription
   * are immediately dropped. Calls to Pull after deletion will return NOT_FOUND.
   * (subscriptions.delete)
   *
   * @param string $subscription
   * The subscription to delete.
   * @param array $optParams Optional parameters.
   */
  public function delete($subscription, $optParams = array())
  {
    $params = array('subscription' => $subscription);
    $params = array_merge($params, $optParams);
    return $this->call('delete', array($params));
  }
  /**
   * Gets the configuration details of a subscription. (subscriptions.get)
   *
   * @param string $subscription
   * The name of the subscription to get.
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Pubsub_Subscription
   */
  public function get($subscription, $optParams = array())
  {
    $params = array('subscription' => $subscription);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Analytify_Google_Service_Pubsub_Subscription");
  }
  /**
   * Lists matching subscriptions. (subscriptions.listSubscriptions)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken
   * The value obtained in the last ListSubscriptionsResponse for continuation.
   * @opt_param int maxResults
   * Maximum number of subscriptions to return.
   * @opt_param string query
   * A valid label query expression.
   * @return Analytify_Google_Service_Pubsub_ListSubscriptionsResponse
   */
  public function listSubscriptions($optParams = array())
  {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Analytify_Google_Service_Pubsub_ListSubscriptionsResponse");
  }
  /**
   * Modifies the Ack deadline for a message received from a pull request.
   * (subscriptions.modifyAckDeadline)
   *
   * @param Analytify_Google_ModifyAckDeadlineRequest $postBody
   * @param array $optParams Optional parameters.
   */
  public function modifyAckDeadline(Analytify_Google_Service_Pubsub_ModifyAckDeadlineRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('modifyAckDeadline', array($params));
  }
  /**
   * Modifies the PushConfig for a specified subscription. This method can be used
   * to suspend the flow of messages to an end point by clearing the PushConfig
   * field in the request. Messages will be accumulated for delivery even if no
   * push configuration is defined or while the configuration is modified.
   * (subscriptions.modifyPushConfig)
   *
   * @param Analytify_Google_ModifyPushConfigRequest $postBody
   * @param array $optParams Optional parameters.
   */
  public function modifyPushConfig(Analytify_Google_Service_Pubsub_ModifyPushConfigRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('modifyPushConfig', array($params));
  }
  /**
   * Pulls a single message from the server. If return_immediately is true, and no
   * messages are available in the subscription, this method returns
   * FAILED_PRECONDITION. The system is free to return an UNAVAILABLE error if no
   * messages are available in a reasonable amount of time (to reduce system
   * load). (subscriptions.pull)
   *
   * @param Analytify_Google_PullRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Pubsub_PullResponse
   */
  public function pull(Analytify_Google_Service_Pubsub_PullRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('pull', array($params), "Analytify_Google_Service_Pubsub_PullResponse");
  }
}

/**
 * The "topics" collection of methods.
 * Typical usage is:
 *  <code>
 *   $pubsubService = new Analytify_Google_Service_Pubsub(...);
 *   $topics = $pubsubService->topics;
 *  </code>
 */
class Analytify_Google_Service_Pubsub_Topics_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Creates the given topic with the given name. (topics.create)
   *
   * @param Analytify_Google_Topic $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Pubsub_Topic
   */
  public function create(Analytify_Google_Service_Pubsub_Topic $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('create', array($params), "Analytify_Google_Service_Pubsub_Topic");
  }
  /**
   * Deletes the topic with the given name. All subscriptions to this topic are
   * also deleted. Returns NOT_FOUND if the topic does not exist. After a topic is
   * deleted, a new topic may be created with the same name. (topics.delete)
   *
   * @param string $topic
   * Name of the topic to delete.
   * @param array $optParams Optional parameters.
   */
  public function delete($topic, $optParams = array())
  {
    $params = array('topic' => $topic);
    $params = array_merge($params, $optParams);
    return $this->call('delete', array($params));
  }
  /**
   * Gets the configuration of a topic. Since the topic only has the name
   * attribute, this method is only useful to check the existence of a topic. If
   * other attributes are added in the future, they will be returned here.
   * (topics.get)
   *
   * @param string $topic
   * The name of the topic to get.
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Pubsub_Topic
   */
  public function get($topic, $optParams = array())
  {
    $params = array('topic' => $topic);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Analytify_Google_Service_Pubsub_Topic");
  }
  /**
   * Lists matching topics. (topics.listTopics)
   *
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken
   * The value obtained in the last ListTopicsResponse for continuation.
   * @opt_param int maxResults
   * Maximum number of topics to return.
   * @opt_param string query
   * A valid label query expression.
   * @return Analytify_Google_Service_Pubsub_ListTopicsResponse
   */
  public function listTopics($optParams = array())
  {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Analytify_Google_Service_Pubsub_ListTopicsResponse");
  }
  /**
   * Adds a message to the topic. Returns NOT_FOUND if the topic does not exist.
   * (topics.publish)
   *
   * @param Analytify_Google_PublishRequest $postBody
   * @param array $optParams Optional parameters.
   */
  public function publish(Analytify_Google_Service_Pubsub_PublishRequest $postBody, $optParams = array())
  {
    $params = array('postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('publish', array($params));
  }
}




class Analytify_Google_Service_Pubsub_AcknowledgeRequest extends Analytify_Google_Collection
{
  public $ackId;
  public $subscription;

  public function setAckId($ackId)
  {
    $this->ackId = $ackId;
  }

  public function getAckId()
  {
    return $this->ackId;
  }

  public function setSubscription($subscription)
  {
    $this->subscription = $subscription;
  }

  public function getSubscription()
  {
    return $this->subscription;
  }
}

class Analytify_Google_Service_Pubsub_Label extends Analytify_Google_Model
{
  public $key;
  public $numValue;
  public $strValue;

  public function setKey($key)
  {
    $this->key = $key;
  }

  public function getKey()
  {
    return $this->key;
  }

  public function setNumValue($numValue)
  {
    $this->numValue = $numValue;
  }

  public function getNumValue()
  {
    return $this->numValue;
  }

  public function setStrValue($strValue)
  {
    $this->strValue = $strValue;
  }

  public function getStrValue()
  {
    return $this->strValue;
  }
}

class Analytify_Google_Service_Pubsub_ListSubscriptionsResponse extends Analytify_Google_Collection
{
  public $nextPageToken;
  protected $subscriptionType = 'Analytify_Google_Service_Pubsub_Subscription';
  protected $subscriptionDataType = 'array';

  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }

  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }

  public function setSubscription($subscription)
  {
    $this->subscription = $subscription;
  }

  public function getSubscription()
  {
    return $this->subscription;
  }
}

class Analytify_Google_Service_Pubsub_ListTopicsResponse extends Analytify_Google_Collection
{
  public $nextPageToken;
  protected $topicType = 'Analytify_Google_Service_Pubsub_Topic';
  protected $topicDataType = 'array';

  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }

  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }

  public function setTopic($topic)
  {
    $this->topic = $topic;
  }

  public function getTopic()
  {
    return $this->topic;
  }
}

class Analytify_Google_Service_Pubsub_ModifyAckDeadlineRequest extends Analytify_Google_Model
{
  public $ackDeadlineSeconds;
  public $ackId;
  public $subscription;

  public function setAckDeadlineSeconds($ackDeadlineSeconds)
  {
    $this->ackDeadlineSeconds = $ackDeadlineSeconds;
  }

  public function getAckDeadlineSeconds()
  {
    return $this->ackDeadlineSeconds;
  }

  public function setAckId($ackId)
  {
    $this->ackId = $ackId;
  }

  public function getAckId()
  {
    return $this->ackId;
  }

  public function setSubscription($subscription)
  {
    $this->subscription = $subscription;
  }

  public function getSubscription()
  {
    return $this->subscription;
  }
}

class Analytify_Google_Service_Pubsub_ModifyPushConfigRequest extends Analytify_Google_Model
{
  protected $pushConfigType = 'Analytify_Google_Service_Pubsub_PushConfig';
  protected $pushConfigDataType = '';
  public $subscription;

  public function setPushConfig(Analytify_Google_Service_Pubsub_PushConfig $pushConfig)
  {
    $this->pushConfig = $pushConfig;
  }

  public function getPushConfig()
  {
    return $this->pushConfig;
  }

  public function setSubscription($subscription)
  {
    $this->subscription = $subscription;
  }

  public function getSubscription()
  {
    return $this->subscription;
  }
}

class Analytify_Google_Service_Pubsub_PublishRequest extends Analytify_Google_Model
{
  protected $messageType = 'Analytify_Google_Service_Pubsub_PubsubMessage';
  protected $messageDataType = '';
  public $topic;

  public function setMessage(Analytify_Google_Service_Pubsub_PubsubMessage $message)
  {
    $this->message = $message;
  }

  public function getMessage()
  {
    return $this->message;
  }

  public function setTopic($topic)
  {
    $this->topic = $topic;
  }

  public function getTopic()
  {
    return $this->topic;
  }
}

class Analytify_Google_Service_Pubsub_PubsubEvent extends Analytify_Google_Model
{
  public $deleted;
  protected $messageType = 'Analytify_Google_Service_Pubsub_PubsubMessage';
  protected $messageDataType = '';
  public $subscription;
  public $truncated;

  public function setDeleted($deleted)
  {
    $this->deleted = $deleted;
  }

  public function getDeleted()
  {
    return $this->deleted;
  }

  public function setMessage(Analytify_Google_Service_Pubsub_PubsubMessage $message)
  {
    $this->message = $message;
  }

  public function getMessage()
  {
    return $this->message;
  }

  public function setSubscription($subscription)
  {
    $this->subscription = $subscription;
  }

  public function getSubscription()
  {
    return $this->subscription;
  }

  public function setTruncated($truncated)
  {
    $this->truncated = $truncated;
  }

  public function getTruncated()
  {
    return $this->truncated;
  }
}

class Analytify_Google_Service_Pubsub_PubsubMessage extends Analytify_Google_Collection
{
  public $data;
  protected $labelType = 'Analytify_Google_Service_Pubsub_Label';
  protected $labelDataType = 'array';

  public function setData($data)
  {
    $this->data = $data;
  }

  public function getData()
  {
    return $this->data;
  }

  public function setLabel($label)
  {
    $this->label = $label;
  }

  public function getLabel()
  {
    return $this->label;
  }
}

class Analytify_Google_Service_Pubsub_PullRequest extends Analytify_Google_Model
{
  public $returnImmediately;
  public $subscription;

  public function setReturnImmediately($returnImmediately)
  {
    $this->returnImmediately = $returnImmediately;
  }

  public function getReturnImmediately()
  {
    return $this->returnImmediately;
  }

  public function setSubscription($subscription)
  {
    $this->subscription = $subscription;
  }

  public function getSubscription()
  {
    return $this->subscription;
  }
}

class Analytify_Google_Service_Pubsub_PullResponse extends Analytify_Google_Model
{
  public $ackId;
  protected $pubsubEventType = 'Analytify_Google_Service_Pubsub_PubsubEvent';
  protected $pubsubEventDataType = '';

  public function setAckId($ackId)
  {
    $this->ackId = $ackId;
  }

  public function getAckId()
  {
    return $this->ackId;
  }

  public function setPubsubEvent(Analytify_Google_Service_Pubsub_PubsubEvent $pubsubEvent)
  {
    $this->pubsubEvent = $pubsubEvent;
  }

  public function getPubsubEvent()
  {
    return $this->pubsubEvent;
  }
}

class Analytify_Google_Service_Pubsub_PushConfig extends Analytify_Google_Model
{
  public $pushEndpoint;

  public function setPushEndpoint($pushEndpoint)
  {
    $this->pushEndpoint = $pushEndpoint;
  }

  public function getPushEndpoint()
  {
    return $this->pushEndpoint;
  }
}

class Analytify_Google_Service_Pubsub_Subscription extends Analytify_Google_Model
{
  public $ackDeadlineSeconds;
  public $name;
  protected $pushConfigType = 'Analytify_Google_Service_Pubsub_PushConfig';
  protected $pushConfigDataType = '';
  public $topic;

  public function setAckDeadlineSeconds($ackDeadlineSeconds)
  {
    $this->ackDeadlineSeconds = $ackDeadlineSeconds;
  }

  public function getAckDeadlineSeconds()
  {
    return $this->ackDeadlineSeconds;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setPushConfig(Analytify_Google_Service_Pubsub_PushConfig $pushConfig)
  {
    $this->pushConfig = $pushConfig;
  }

  public function getPushConfig()
  {
    return $this->pushConfig;
  }

  public function setTopic($topic)
  {
    $this->topic = $topic;
  }

  public function getTopic()
  {
    return $this->topic;
  }
}

class Analytify_Google_Service_Pubsub_Topic extends Analytify_Google_Model
{
  public $name;

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }
}
