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
 * Service definition for Dns (v1beta1).
 *
 * <p>
 * The Google Cloud DNS API provides services for configuring and serving authoritative DNS records.
 * </p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/cloud-dns" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Analytify_Google_Service_Dns extends Analytify_Google_Service
{
  /** View and manage your data across Google Cloud Platform services. */
  const CLOUD_PLATFORM = "https://www.googleapis.com/auth/cloud-platform";
  /** View your DNS records hosted by Google Cloud DNS. */
  const NDEV_CLOUDDNS_READONLY = "https://www.googleapis.com/auth/ndev.clouddns.readonly";
  /** View and manage your DNS records hosted by Google Cloud DNS. */
  const NDEV_CLOUDDNS_READWRITE = "https://www.googleapis.com/auth/ndev.clouddns.readwrite";

  public $changes;
  public $managedZones;
  public $projects;
  public $resourceRecordSets;
  

  /**
   * Constructs the internal representation of the Dns service.
   *
   * @param Analytify_Google_Client $client
   */
  public function __construct(Analytify_Google_Client $client)
  {
    parent::__construct($client);
    $this->servicePath = 'dns/v1beta1/projects/';
    $this->version = 'v1beta1';
    $this->serviceName = 'dns';

    $this->changes = new Analytify_Google_Service_Dns_Changes_Resource(
        $this,
        $this->serviceName,
        'changes',
        array(
          'methods' => array(
            'create' => array(
              'path' => '{project}/managedZones/{managedZone}/changes',
              'httpMethod' => 'POST',
              'parameters' => array(
                'project' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'managedZone' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'get' => array(
              'path' => '{project}/managedZones/{managedZone}/changes/{changeId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'project' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'managedZone' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'changeId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => '{project}/managedZones/{managedZone}/changes',
              'httpMethod' => 'GET',
              'parameters' => array(
                'project' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'managedZone' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'maxResults' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'sortBy' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'sortOrder' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
              ),
            ),
          )
        )
    );
    $this->managedZones = new Analytify_Google_Service_Dns_ManagedZones_Resource(
        $this,
        $this->serviceName,
        'managedZones',
        array(
          'methods' => array(
            'create' => array(
              'path' => '{project}/managedZones',
              'httpMethod' => 'POST',
              'parameters' => array(
                'project' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'delete' => array(
              'path' => '{project}/managedZones/{managedZone}',
              'httpMethod' => 'DELETE',
              'parameters' => array(
                'project' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'managedZone' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'get' => array(
              'path' => '{project}/managedZones/{managedZone}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'project' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'managedZone' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => '{project}/managedZones',
              'httpMethod' => 'GET',
              'parameters' => array(
                'project' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'maxResults' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
              ),
            ),
          )
        )
    );
    $this->projects = new Analytify_Google_Service_Dns_Projects_Resource(
        $this,
        $this->serviceName,
        'projects',
        array(
          'methods' => array(
            'get' => array(
              'path' => '{project}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'project' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),
          )
        )
    );
    $this->resourceRecordSets = new Analytify_Google_Service_Dns_ResourceRecordSets_Resource(
        $this,
        $this->serviceName,
        'resourceRecordSets',
        array(
          'methods' => array(
            'list' => array(
              'path' => '{project}/managedZones/{managedZone}/rrsets',
              'httpMethod' => 'GET',
              'parameters' => array(
                'project' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'managedZone' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'name' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'maxResults' => array(
                  'location' => 'query',
                  'type' => 'integer',
                ),
                'pageToken' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'type' => array(
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
 * The "changes" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dnsService = new Analytify_Google_Service_Dns(...);
 *   $changes = $dnsService->changes;
 *  </code>
 */
class Analytify_Google_Service_Dns_Changes_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Atomically update the ResourceRecordSet collection. (changes.create)
   *
   * @param string $project
   * Identifies the project addressed by this request.
   * @param string $managedZone
   * Identifies the managed zone addressed by this request. Can be the managed zone name or id.
   * @param Analytify_Google_Change $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dns_Change
   */
  public function create($project, $managedZone, Analytify_Google_Service_Dns_Change $postBody, $optParams = array())
  {
    $params = array('project' => $project, 'managedZone' => $managedZone, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('create', array($params), "Analytify_Google_Service_Dns_Change");
  }
  /**
   * Fetch the representation of an existing Change. (changes.get)
   *
   * @param string $project
   * Identifies the project addressed by this request.
   * @param string $managedZone
   * Identifies the managed zone addressed by this request. Can be the managed zone name or id.
   * @param string $changeId
   * The identifier of the requested change, from a previous ResourceRecordSetsChangeResponse.
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dns_Change
   */
  public function get($project, $managedZone, $changeId, $optParams = array())
  {
    $params = array('project' => $project, 'managedZone' => $managedZone, 'changeId' => $changeId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Analytify_Google_Service_Dns_Change");
  }
  /**
   * Enumerate Changes to a ResourceRecordSet collection. (changes.listChanges)
   *
   * @param string $project
   * Identifies the project addressed by this request.
   * @param string $managedZone
   * Identifies the managed zone addressed by this request. Can be the managed zone name or id.
   * @param array $optParams Optional parameters.
   *
   * @opt_param int maxResults
   * Optional. Maximum number of results to be returned. If unspecified, the server will decide how
    * many results to return.
   * @opt_param string pageToken
   * Optional. A tag returned by a previous list request that was truncated. Use this parameter to
    * continue a previous list request.
   * @opt_param string sortBy
   * Sorting criterion. The only supported value is change sequence.
   * @opt_param string sortOrder
   * Sorting order direction: 'ascending' or 'descending'.
   * @return Analytify_Google_Service_Dns_ChangesListResponse
   */
  public function listChanges($project, $managedZone, $optParams = array())
  {
    $params = array('project' => $project, 'managedZone' => $managedZone);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Analytify_Google_Service_Dns_ChangesListResponse");
  }
}

/**
 * The "managedZones" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dnsService = new Analytify_Google_Service_Dns(...);
 *   $managedZones = $dnsService->managedZones;
 *  </code>
 */
class Analytify_Google_Service_Dns_ManagedZones_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Create a new ManagedZone. (managedZones.create)
   *
   * @param string $project
   * Identifies the project addressed by this request.
   * @param Analytify_Google_ManagedZone $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dns_ManagedZone
   */
  public function create($project, Analytify_Google_Service_Dns_ManagedZone $postBody, $optParams = array())
  {
    $params = array('project' => $project, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('create', array($params), "Analytify_Google_Service_Dns_ManagedZone");
  }
  /**
   * Delete a previously created ManagedZone. (managedZones.delete)
   *
   * @param string $project
   * Identifies the project addressed by this request.
   * @param string $managedZone
   * Identifies the managed zone addressed by this request. Can be the managed zone name or id.
   * @param array $optParams Optional parameters.
   */
  public function delete($project, $managedZone, $optParams = array())
  {
    $params = array('project' => $project, 'managedZone' => $managedZone);
    $params = array_merge($params, $optParams);
    return $this->call('delete', array($params));
  }
  /**
   * Fetch the representation of an existing ManagedZone. (managedZones.get)
   *
   * @param string $project
   * Identifies the project addressed by this request.
   * @param string $managedZone
   * Identifies the managed zone addressed by this request. Can be the managed zone name or id.
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dns_ManagedZone
   */
  public function get($project, $managedZone, $optParams = array())
  {
    $params = array('project' => $project, 'managedZone' => $managedZone);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Analytify_Google_Service_Dns_ManagedZone");
  }
  /**
   * Enumerate ManagedZones that have been created but not yet deleted.
   * (managedZones.listManagedZones)
   *
   * @param string $project
   * Identifies the project addressed by this request.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken
   * Optional. A tag returned by a previous list request that was truncated. Use this parameter to
    * continue a previous list request.
   * @opt_param int maxResults
   * Optional. Maximum number of results to be returned. If unspecified, the server will decide how
    * many results to return.
   * @return Analytify_Google_Service_Dns_ManagedZonesListResponse
   */
  public function listManagedZones($project, $optParams = array())
  {
    $params = array('project' => $project);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Analytify_Google_Service_Dns_ManagedZonesListResponse");
  }
}

/**
 * The "projects" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dnsService = new Analytify_Google_Service_Dns(...);
 *   $projects = $dnsService->projects;
 *  </code>
 */
class Analytify_Google_Service_Dns_Projects_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Fetch the representation of an existing Project. (projects.get)
   *
   * @param string $project
   * Identifies the project addressed by this request.
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dns_Project
   */
  public function get($project, $optParams = array())
  {
    $params = array('project' => $project);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Analytify_Google_Service_Dns_Project");
  }
}

/**
 * The "resourceRecordSets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dnsService = new Analytify_Google_Service_Dns(...);
 *   $resourceRecordSets = $dnsService->resourceRecordSets;
 *  </code>
 */
class Analytify_Google_Service_Dns_ResourceRecordSets_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Enumerate ResourceRecordSets that have been created but not yet deleted.
   * (resourceRecordSets.listResourceRecordSets)
   *
   * @param string $project
   * Identifies the project addressed by this request.
   * @param string $managedZone
   * Identifies the managed zone addressed by this request. Can be the managed zone name or id.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string name
   * Restricts the list to return only records with this fully qualified domain name.
   * @opt_param int maxResults
   * Optional. Maximum number of results to be returned. If unspecified, the server will decide how
    * many results to return.
   * @opt_param string pageToken
   * Optional. A tag returned by a previous list request that was truncated. Use this parameter to
    * continue a previous list request.
   * @opt_param string type
   * Restricts the list to return only records of this type. If present, the "name" parameter must
    * also be present.
   * @return Analytify_Google_Service_Dns_ResourceRecordSetsListResponse
   */
  public function listResourceRecordSets($project, $managedZone, $optParams = array())
  {
    $params = array('project' => $project, 'managedZone' => $managedZone);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Analytify_Google_Service_Dns_ResourceRecordSetsListResponse");
  }
}




class Analytify_Google_Service_Dns_Change extends Analytify_Google_Collection
{
  protected $additionsType = 'Analytify_Google_Service_Dns_ResourceRecordSet';
  protected $additionsDataType = 'array';
  protected $deletionsType = 'Analytify_Google_Service_Dns_ResourceRecordSet';
  protected $deletionsDataType = 'array';
  public $id;
  public $kind;
  public $startTime;
  public $status;

  public function setAdditions($additions)
  {
    $this->additions = $additions;
  }

  public function getAdditions()
  {
    return $this->additions;
  }

  public function setDeletions($deletions)
  {
    $this->deletions = $deletions;
  }

  public function getDeletions()
  {
    return $this->deletions;
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

  public function setStartTime($startTime)
  {
    $this->startTime = $startTime;
  }

  public function getStartTime()
  {
    return $this->startTime;
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

class Analytify_Google_Service_Dns_ChangesListResponse extends Analytify_Google_Collection
{
  protected $changesType = 'Analytify_Google_Service_Dns_Change';
  protected $changesDataType = 'array';
  public $kind;
  public $nextPageToken;

  public function setChanges($changes)
  {
    $this->changes = $changes;
  }

  public function getChanges()
  {
    return $this->changes;
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
}

class Analytify_Google_Service_Dns_ManagedZone extends Analytify_Google_Collection
{
  public $creationTime;
  public $description;
  public $dnsName;
  public $id;
  public $kind;
  public $name;
  public $nameServers;

  public function setCreationTime($creationTime)
  {
    $this->creationTime = $creationTime;
  }

  public function getCreationTime()
  {
    return $this->creationTime;
  }

  public function setDescription($description)
  {
    $this->description = $description;
  }

  public function getDescription()
  {
    return $this->description;
  }

  public function setDnsName($dnsName)
  {
    $this->dnsName = $dnsName;
  }

  public function getDnsName()
  {
    return $this->dnsName;
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

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setNameServers($nameServers)
  {
    $this->nameServers = $nameServers;
  }

  public function getNameServers()
  {
    return $this->nameServers;
  }
}

class Analytify_Google_Service_Dns_ManagedZonesListResponse extends Analytify_Google_Collection
{
  public $kind;
  protected $managedZonesType = 'Analytify_Google_Service_Dns_ManagedZone';
  protected $managedZonesDataType = 'array';
  public $nextPageToken;

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setManagedZones($managedZones)
  {
    $this->managedZones = $managedZones;
  }

  public function getManagedZones()
  {
    return $this->managedZones;
  }

  public function setNextPageToken($nextPageToken)
  {
    $this->nextPageToken = $nextPageToken;
  }

  public function getNextPageToken()
  {
    return $this->nextPageToken;
  }
}

class Analytify_Google_Service_Dns_Project extends Analytify_Google_Model
{
  public $id;
  public $kind;
  public $number;
  protected $quotaType = 'Analytify_Google_Service_Dns_Quota';
  protected $quotaDataType = '';

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

  public function setNumber($number)
  {
    $this->number = $number;
  }

  public function getNumber()
  {
    return $this->number;
  }

  public function setQuota(Analytify_Google_Service_Dns_Quota $quota)
  {
    $this->quota = $quota;
  }

  public function getQuota()
  {
    return $this->quota;
  }
}

class Analytify_Google_Service_Dns_Quota extends Analytify_Google_Model
{
  public $kind;
  public $managedZones;
  public $resourceRecordsPerRrset;
  public $rrsetAdditionsPerChange;
  public $rrsetDeletionsPerChange;
  public $rrsetsPerManagedZone;
  public $totalRrdataSizePerChange;

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setManagedZones($managedZones)
  {
    $this->managedZones = $managedZones;
  }

  public function getManagedZones()
  {
    return $this->managedZones;
  }

  public function setResourceRecordsPerRrset($resourceRecordsPerRrset)
  {
    $this->resourceRecordsPerRrset = $resourceRecordsPerRrset;
  }

  public function getResourceRecordsPerRrset()
  {
    return $this->resourceRecordsPerRrset;
  }

  public function setRrsetAdditionsPerChange($rrsetAdditionsPerChange)
  {
    $this->rrsetAdditionsPerChange = $rrsetAdditionsPerChange;
  }

  public function getRrsetAdditionsPerChange()
  {
    return $this->rrsetAdditionsPerChange;
  }

  public function setRrsetDeletionsPerChange($rrsetDeletionsPerChange)
  {
    $this->rrsetDeletionsPerChange = $rrsetDeletionsPerChange;
  }

  public function getRrsetDeletionsPerChange()
  {
    return $this->rrsetDeletionsPerChange;
  }

  public function setRrsetsPerManagedZone($rrsetsPerManagedZone)
  {
    $this->rrsetsPerManagedZone = $rrsetsPerManagedZone;
  }

  public function getRrsetsPerManagedZone()
  {
    return $this->rrsetsPerManagedZone;
  }

  public function setTotalRrdataSizePerChange($totalRrdataSizePerChange)
  {
    $this->totalRrdataSizePerChange = $totalRrdataSizePerChange;
  }

  public function getTotalRrdataSizePerChange()
  {
    return $this->totalRrdataSizePerChange;
  }
}

class Analytify_Google_Service_Dns_ResourceRecordSet extends Analytify_Google_Collection
{
  public $kind;
  public $name;
  public $rrdatas;
  public $ttl;
  public $type;

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setRrdatas($rrdatas)
  {
    $this->rrdatas = $rrdatas;
  }

  public function getRrdatas()
  {
    return $this->rrdatas;
  }

  public function setTtl($ttl)
  {
    $this->ttl = $ttl;
  }

  public function getTtl()
  {
    return $this->ttl;
  }

  public function setType($type)
  {
    $this->type = $type;
  }

  public function getType()
  {
    return $this->type;
  }
}

class Analytify_Google_Service_Dns_ResourceRecordSetsListResponse extends Analytify_Google_Collection
{
  public $kind;
  public $nextPageToken;
  protected $rrsetsType = 'Analytify_Google_Service_Dns_ResourceRecordSet';
  protected $rrsetsDataType = 'array';

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

  public function setRrsets($rrsets)
  {
    $this->rrsets = $rrsets;
  }

  public function getRrsets()
  {
    return $this->rrsets;
  }
}
