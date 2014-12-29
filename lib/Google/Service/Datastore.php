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
 * Service definition for Datastore (v1beta2).
 *
 * <p>
 * API for accessing Google Cloud Datastore.
 * </p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/datastore/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Analytify_Google_Service_Datastore extends Analytify_Google_Service
{
  /** View and manage your Google Cloud Datastore data. */
  const DATASTORE = "https://www.googleapis.com/auth/datastore";
  /** View your email address. */
  const USERINFO_EMAIL = "https://www.googleapis.com/auth/userinfo.email";

  public $datasets;
  

  /**
   * Constructs the internal representation of the Datastore service.
   *
   * @param Analytify_Google_Client $client
   */
  public function __construct(Analytify_Google_Client $client)
  {
    parent::__construct($client);
    $this->servicePath = 'datastore/v1beta2/datasets/';
    $this->version = 'v1beta2';
    $this->serviceName = 'datastore';

    $this->datasets = new Analytify_Google_Service_Datastore_Datasets_Resource(
        $this,
        $this->serviceName,
        'datasets',
        array(
          'methods' => array(
            'allocateIds' => array(
              'path' => '{datasetId}/allocateIds',
              'httpMethod' => 'POST',
              'parameters' => array(
                'datasetId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'beginTransaction' => array(
              'path' => '{datasetId}/beginTransaction',
              'httpMethod' => 'POST',
              'parameters' => array(
                'datasetId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'commit' => array(
              'path' => '{datasetId}/commit',
              'httpMethod' => 'POST',
              'parameters' => array(
                'datasetId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'lookup' => array(
              'path' => '{datasetId}/lookup',
              'httpMethod' => 'POST',
              'parameters' => array(
                'datasetId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'rollback' => array(
              'path' => '{datasetId}/rollback',
              'httpMethod' => 'POST',
              'parameters' => array(
                'datasetId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'runQuery' => array(
              'path' => '{datasetId}/runQuery',
              'httpMethod' => 'POST',
              'parameters' => array(
                'datasetId' => array(
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
 * The "datasets" collection of methods.
 * Typical usage is:
 *  <code>
 *   $datastoreService = new Analytify_Google_Service_Datastore(...);
 *   $datasets = $datastoreService->datasets;
 *  </code>
 */
class Analytify_Google_Service_Datastore_Datasets_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Allocate IDs for incomplete keys (useful for referencing an entity before it
   * is inserted). (datasets.allocateIds)
   *
   * @param string $datasetId
   * Identifies the dataset.
   * @param Analytify_Google_AllocateIdsRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Datastore_AllocateIdsResponse
   */
  public function allocateIds($datasetId, Analytify_Google_Service_Datastore_AllocateIdsRequest $postBody, $optParams = array())
  {
    $params = array('datasetId' => $datasetId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('allocateIds', array($params), "Analytify_Google_Service_Datastore_AllocateIdsResponse");
  }
  /**
   * Begin a new transaction. (datasets.beginTransaction)
   *
   * @param string $datasetId
   * Identifies the dataset.
   * @param Analytify_Google_BeginTransactionRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Datastore_BeginTransactionResponse
   */
  public function beginTransaction($datasetId, Analytify_Google_Service_Datastore_BeginTransactionRequest $postBody, $optParams = array())
  {
    $params = array('datasetId' => $datasetId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('beginTransaction', array($params), "Analytify_Google_Service_Datastore_BeginTransactionResponse");
  }
  /**
   * Commit a transaction, optionally creating, deleting or modifying some
   * entities. (datasets.commit)
   *
   * @param string $datasetId
   * Identifies the dataset.
   * @param Analytify_Google_CommitRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Datastore_CommitResponse
   */
  public function commit($datasetId, Analytify_Google_Service_Datastore_CommitRequest $postBody, $optParams = array())
  {
    $params = array('datasetId' => $datasetId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('commit', array($params), "Analytify_Google_Service_Datastore_CommitResponse");
  }
  /**
   * Look up some entities by key. (datasets.lookup)
   *
   * @param string $datasetId
   * Identifies the dataset.
   * @param Analytify_Google_LookupRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Datastore_LookupResponse
   */
  public function lookup($datasetId, Analytify_Google_Service_Datastore_LookupRequest $postBody, $optParams = array())
  {
    $params = array('datasetId' => $datasetId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('lookup', array($params), "Analytify_Google_Service_Datastore_LookupResponse");
  }
  /**
   * Roll back a transaction. (datasets.rollback)
   *
   * @param string $datasetId
   * Identifies the dataset.
   * @param Analytify_Google_RollbackRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Datastore_RollbackResponse
   */
  public function rollback($datasetId, Analytify_Google_Service_Datastore_RollbackRequest $postBody, $optParams = array())
  {
    $params = array('datasetId' => $datasetId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('rollback', array($params), "Analytify_Google_Service_Datastore_RollbackResponse");
  }
  /**
   * Query for entities. (datasets.runQuery)
   *
   * @param string $datasetId
   * Identifies the dataset.
   * @param Analytify_Google_RunQueryRequest $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Datastore_RunQueryResponse
   */
  public function runQuery($datasetId, Analytify_Google_Service_Datastore_RunQueryRequest $postBody, $optParams = array())
  {
    $params = array('datasetId' => $datasetId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('runQuery', array($params), "Analytify_Google_Service_Datastore_RunQueryResponse");
  }
}




class Analytify_Google_Service_Datastore_AllocateIdsRequest extends Analytify_Google_Collection
{
  protected $keysType = 'Analytify_Google_Service_Datastore_Key';
  protected $keysDataType = 'array';

  public function setKeys($keys)
  {
    $this->keys = $keys;
  }

  public function getKeys()
  {
    return $this->keys;
  }
}

class Analytify_Google_Service_Datastore_AllocateIdsResponse extends Analytify_Google_Collection
{
  protected $headerType = 'Analytify_Google_Service_Datastore_ResponseHeader';
  protected $headerDataType = '';
  protected $keysType = 'Analytify_Google_Service_Datastore_Key';
  protected $keysDataType = 'array';

  public function setHeader(Analytify_Google_Service_Datastore_ResponseHeader $header)
  {
    $this->header = $header;
  }

  public function getHeader()
  {
    return $this->header;
  }

  public function setKeys($keys)
  {
    $this->keys = $keys;
  }

  public function getKeys()
  {
    return $this->keys;
  }
}

class Analytify_Google_Service_Datastore_BeginTransactionRequest extends Analytify_Google_Model
{
  public $isolationLevel;

  public function setIsolationLevel($isolationLevel)
  {
    $this->isolationLevel = $isolationLevel;
  }

  public function getIsolationLevel()
  {
    return $this->isolationLevel;
  }
}

class Analytify_Google_Service_Datastore_BeginTransactionResponse extends Analytify_Google_Model
{
  protected $headerType = 'Analytify_Google_Service_Datastore_ResponseHeader';
  protected $headerDataType = '';
  public $transaction;

  public function setHeader(Analytify_Google_Service_Datastore_ResponseHeader $header)
  {
    $this->header = $header;
  }

  public function getHeader()
  {
    return $this->header;
  }

  public function setTransaction($transaction)
  {
    $this->transaction = $transaction;
  }

  public function getTransaction()
  {
    return $this->transaction;
  }
}

class Analytify_Google_Service_Datastore_CommitRequest extends Analytify_Google_Model
{
  public $ignoreReadOnly;
  public $mode;
  protected $mutationType = 'Analytify_Google_Service_Datastore_Mutation';
  protected $mutationDataType = '';
  public $transaction;

  public function setIgnoreReadOnly($ignoreReadOnly)
  {
    $this->ignoreReadOnly = $ignoreReadOnly;
  }

  public function getIgnoreReadOnly()
  {
    return $this->ignoreReadOnly;
  }

  public function setMode($mode)
  {
    $this->mode = $mode;
  }

  public function getMode()
  {
    return $this->mode;
  }

  public function setMutation(Analytify_Google_Service_Datastore_Mutation $mutation)
  {
    $this->mutation = $mutation;
  }

  public function getMutation()
  {
    return $this->mutation;
  }

  public function setTransaction($transaction)
  {
    $this->transaction = $transaction;
  }

  public function getTransaction()
  {
    return $this->transaction;
  }
}

class Analytify_Google_Service_Datastore_CommitResponse extends Analytify_Google_Model
{
  protected $headerType = 'Analytify_Google_Service_Datastore_ResponseHeader';
  protected $headerDataType = '';
  protected $mutationResultType = 'Analytify_Google_Service_Datastore_MutationResult';
  protected $mutationResultDataType = '';

  public function setHeader(Analytify_Google_Service_Datastore_ResponseHeader $header)
  {
    $this->header = $header;
  }

  public function getHeader()
  {
    return $this->header;
  }

  public function setMutationResult(Analytify_Google_Service_Datastore_MutationResult $mutationResult)
  {
    $this->mutationResult = $mutationResult;
  }

  public function getMutationResult()
  {
    return $this->mutationResult;
  }
}

class Analytify_Google_Service_Datastore_CompositeFilter extends Analytify_Google_Collection
{
  protected $filtersType = 'Analytify_Google_Service_Datastore_Filter';
  protected $filtersDataType = 'array';
  public $operator;

  public function setFilters($filters)
  {
    $this->filters = $filters;
  }

  public function getFilters()
  {
    return $this->filters;
  }

  public function setOperator($operator)
  {
    $this->operator = $operator;
  }

  public function getOperator()
  {
    return $this->operator;
  }
}

class Analytify_Google_Service_Datastore_Entity extends Analytify_Google_Model
{
  protected $keyType = 'Analytify_Google_Service_Datastore_Key';
  protected $keyDataType = '';
  protected $propertiesType = 'Analytify_Google_Service_Datastore_Property';
  protected $propertiesDataType = 'map';

  public function setKey(Analytify_Google_Service_Datastore_Key $key)
  {
    $this->key = $key;
  }

  public function getKey()
  {
    return $this->key;
  }

  public function setProperties($properties)
  {
    $this->properties = $properties;
  }

  public function getProperties()
  {
    return $this->properties;
  }
}

class Analytify_Google_Service_Datastore_EntityProperties extends Analytify_Google_Model
{

}

class Analytify_Google_Service_Datastore_EntityResult extends Analytify_Google_Model
{
  protected $entityType = 'Analytify_Google_Service_Datastore_Entity';
  protected $entityDataType = '';

  public function setEntity(Analytify_Google_Service_Datastore_Entity $entity)
  {
    $this->entity = $entity;
  }

  public function getEntity()
  {
    return $this->entity;
  }
}

class Analytify_Google_Service_Datastore_Filter extends Analytify_Google_Model
{
  protected $compositeFilterType = 'Analytify_Google_Service_Datastore_CompositeFilter';
  protected $compositeFilterDataType = '';
  protected $propertyFilterType = 'Analytify_Google_Service_Datastore_PropertyFilter';
  protected $propertyFilterDataType = '';

  public function setCompositeFilter(Analytify_Google_Service_Datastore_CompositeFilter $compositeFilter)
  {
    $this->compositeFilter = $compositeFilter;
  }

  public function getCompositeFilter()
  {
    return $this->compositeFilter;
  }

  public function setPropertyFilter(Analytify_Google_Service_Datastore_PropertyFilter $propertyFilter)
  {
    $this->propertyFilter = $propertyFilter;
  }

  public function getPropertyFilter()
  {
    return $this->propertyFilter;
  }
}

class Analytify_Google_Service_Datastore_GqlQuery extends Analytify_Google_Collection
{
  public $allowLiteral;
  protected $nameArgsType = 'Analytify_Google_Service_Datastore_GqlQueryArg';
  protected $nameArgsDataType = 'array';
  protected $numberArgsType = 'Analytify_Google_Service_Datastore_GqlQueryArg';
  protected $numberArgsDataType = 'array';
  public $queryString;

  public function setAllowLiteral($allowLiteral)
  {
    $this->allowLiteral = $allowLiteral;
  }

  public function getAllowLiteral()
  {
    return $this->allowLiteral;
  }

  public function setNameArgs($nameArgs)
  {
    $this->nameArgs = $nameArgs;
  }

  public function getNameArgs()
  {
    return $this->nameArgs;
  }

  public function setNumberArgs($numberArgs)
  {
    $this->numberArgs = $numberArgs;
  }

  public function getNumberArgs()
  {
    return $this->numberArgs;
  }

  public function setQueryString($queryString)
  {
    $this->queryString = $queryString;
  }

  public function getQueryString()
  {
    return $this->queryString;
  }
}

class Analytify_Google_Service_Datastore_GqlQueryArg extends Analytify_Google_Model
{
  public $cursor;
  public $name;
  protected $valueType = 'Analytify_Google_Service_Datastore_Value';
  protected $valueDataType = '';

  public function setCursor($cursor)
  {
    $this->cursor = $cursor;
  }

  public function getCursor()
  {
    return $this->cursor;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setValue(Analytify_Google_Service_Datastore_Value $value)
  {
    $this->value = $value;
  }

  public function getValue()
  {
    return $this->value;
  }
}

class Analytify_Google_Service_Datastore_Key extends Analytify_Google_Collection
{
  protected $partitionIdType = 'Analytify_Google_Service_Datastore_PartitionId';
  protected $partitionIdDataType = '';
  protected $pathType = 'Analytify_Google_Service_Datastore_KeyPathElement';
  protected $pathDataType = 'array';

  public function setPartitionId(Analytify_Google_Service_Datastore_PartitionId $partitionId)
  {
    $this->partitionId = $partitionId;
  }

  public function getPartitionId()
  {
    return $this->partitionId;
  }

  public function setPath($path)
  {
    $this->path = $path;
  }

  public function getPath()
  {
    return $this->path;
  }
}

class Analytify_Google_Service_Datastore_KeyPathElement extends Analytify_Google_Model
{
  public $id;
  public $kind;
  public $name;

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
}

class Analytify_Google_Service_Datastore_KindExpression extends Analytify_Google_Model
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

class Analytify_Google_Service_Datastore_LookupRequest extends Analytify_Google_Collection
{
  protected $keysType = 'Analytify_Google_Service_Datastore_Key';
  protected $keysDataType = 'array';
  protected $readOptionsType = 'Analytify_Google_Service_Datastore_ReadOptions';
  protected $readOptionsDataType = '';

  public function setKeys($keys)
  {
    $this->keys = $keys;
  }

  public function getKeys()
  {
    return $this->keys;
  }

  public function setReadOptions(Analytify_Google_Service_Datastore_ReadOptions $readOptions)
  {
    $this->readOptions = $readOptions;
  }

  public function getReadOptions()
  {
    return $this->readOptions;
  }
}

class Analytify_Google_Service_Datastore_LookupResponse extends Analytify_Google_Collection
{
  protected $deferredType = 'Analytify_Google_Service_Datastore_Key';
  protected $deferredDataType = 'array';
  protected $foundType = 'Analytify_Google_Service_Datastore_EntityResult';
  protected $foundDataType = 'array';
  protected $headerType = 'Analytify_Google_Service_Datastore_ResponseHeader';
  protected $headerDataType = '';
  protected $missingType = 'Analytify_Google_Service_Datastore_EntityResult';
  protected $missingDataType = 'array';

  public function setDeferred($deferred)
  {
    $this->deferred = $deferred;
  }

  public function getDeferred()
  {
    return $this->deferred;
  }

  public function setFound($found)
  {
    $this->found = $found;
  }

  public function getFound()
  {
    return $this->found;
  }

  public function setHeader(Analytify_Google_Service_Datastore_ResponseHeader $header)
  {
    $this->header = $header;
  }

  public function getHeader()
  {
    return $this->header;
  }

  public function setMissing($missing)
  {
    $this->missing = $missing;
  }

  public function getMissing()
  {
    return $this->missing;
  }
}

class Analytify_Google_Service_Datastore_Mutation extends Analytify_Google_Collection
{
  protected $deleteType = 'Analytify_Google_Service_Datastore_Key';
  protected $deleteDataType = 'array';
  public $force;
  protected $insertType = 'Analytify_Google_Service_Datastore_Entity';
  protected $insertDataType = 'array';
  protected $insertAutoIdType = 'Analytify_Google_Service_Datastore_Entity';
  protected $insertAutoIdDataType = 'array';
  protected $updateType = 'Analytify_Google_Service_Datastore_Entity';
  protected $updateDataType = 'array';
  protected $upsertType = 'Analytify_Google_Service_Datastore_Entity';
  protected $upsertDataType = 'array';

  public function setDelete($delete)
  {
    $this->delete = $delete;
  }

  public function getDelete()
  {
    return $this->delete;
  }

  public function setForce($force)
  {
    $this->force = $force;
  }

  public function getForce()
  {
    return $this->force;
  }

  public function setInsert($insert)
  {
    $this->insert = $insert;
  }

  public function getInsert()
  {
    return $this->insert;
  }

  public function setInsertAutoId($insertAutoId)
  {
    $this->insertAutoId = $insertAutoId;
  }

  public function getInsertAutoId()
  {
    return $this->insertAutoId;
  }

  public function setUpdate($update)
  {
    $this->update = $update;
  }

  public function getUpdate()
  {
    return $this->update;
  }

  public function setUpsert($upsert)
  {
    $this->upsert = $upsert;
  }

  public function getUpsert()
  {
    return $this->upsert;
  }
}

class Analytify_Google_Service_Datastore_MutationResult extends Analytify_Google_Collection
{
  public $indexUpdates;
  protected $insertAutoIdKeysType = 'Analytify_Google_Service_Datastore_Key';
  protected $insertAutoIdKeysDataType = 'array';

  public function setIndexUpdates($indexUpdates)
  {
    $this->indexUpdates = $indexUpdates;
  }

  public function getIndexUpdates()
  {
    return $this->indexUpdates;
  }

  public function setInsertAutoIdKeys($insertAutoIdKeys)
  {
    $this->insertAutoIdKeys = $insertAutoIdKeys;
  }

  public function getInsertAutoIdKeys()
  {
    return $this->insertAutoIdKeys;
  }
}

class Analytify_Google_Service_Datastore_PartitionId extends Analytify_Google_Model
{
  public $datasetId;
  public $namespace;

  public function setDatasetId($datasetId)
  {
    $this->datasetId = $datasetId;
  }

  public function getDatasetId()
  {
    return $this->datasetId;
  }

  public function setNamespace($namespace)
  {
    $this->namespace = $namespace;
  }

  public function getNamespace()
  {
    return $this->namespace;
  }
}

class Analytify_Google_Service_Datastore_Property extends Analytify_Google_Collection
{
  public $blobKeyValue;
  public $blobValue;
  public $booleanValue;
  public $dateTimeValue;
  public $doubleValue;
  protected $entityValueType = 'Analytify_Google_Service_Datastore_Entity';
  protected $entityValueDataType = '';
  public $indexed;
  public $integerValue;
  protected $keyValueType = 'Analytify_Google_Service_Datastore_Key';
  protected $keyValueDataType = '';
  protected $listValueType = 'Analytify_Google_Service_Datastore_Value';
  protected $listValueDataType = 'array';
  public $meaning;
  public $stringValue;

  public function setBlobKeyValue($blobKeyValue)
  {
    $this->blobKeyValue = $blobKeyValue;
  }

  public function getBlobKeyValue()
  {
    return $this->blobKeyValue;
  }

  public function setBlobValue($blobValue)
  {
    $this->blobValue = $blobValue;
  }

  public function getBlobValue()
  {
    return $this->blobValue;
  }

  public function setBooleanValue($booleanValue)
  {
    $this->booleanValue = $booleanValue;
  }

  public function getBooleanValue()
  {
    return $this->booleanValue;
  }

  public function setDateTimeValue($dateTimeValue)
  {
    $this->dateTimeValue = $dateTimeValue;
  }

  public function getDateTimeValue()
  {
    return $this->dateTimeValue;
  }

  public function setDoubleValue($doubleValue)
  {
    $this->doubleValue = $doubleValue;
  }

  public function getDoubleValue()
  {
    return $this->doubleValue;
  }

  public function setEntityValue(Analytify_Google_Service_Datastore_Entity $entityValue)
  {
    $this->entityValue = $entityValue;
  }

  public function getEntityValue()
  {
    return $this->entityValue;
  }

  public function setIndexed($indexed)
  {
    $this->indexed = $indexed;
  }

  public function getIndexed()
  {
    return $this->indexed;
  }

  public function setIntegerValue($integerValue)
  {
    $this->integerValue = $integerValue;
  }

  public function getIntegerValue()
  {
    return $this->integerValue;
  }

  public function setKeyValue(Analytify_Google_Service_Datastore_Key $keyValue)
  {
    $this->keyValue = $keyValue;
  }

  public function getKeyValue()
  {
    return $this->keyValue;
  }

  public function setListValue($listValue)
  {
    $this->listValue = $listValue;
  }

  public function getListValue()
  {
    return $this->listValue;
  }

  public function setMeaning($meaning)
  {
    $this->meaning = $meaning;
  }

  public function getMeaning()
  {
    return $this->meaning;
  }

  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }

  public function getStringValue()
  {
    return $this->stringValue;
  }
}

class Analytify_Google_Service_Datastore_PropertyExpression extends Analytify_Google_Model
{
  public $aggregationFunction;
  protected $propertyType = 'Analytify_Google_Service_Datastore_PropertyReference';
  protected $propertyDataType = '';

  public function setAggregationFunction($aggregationFunction)
  {
    $this->aggregationFunction = $aggregationFunction;
  }

  public function getAggregationFunction()
  {
    return $this->aggregationFunction;
  }

  public function setProperty(Analytify_Google_Service_Datastore_PropertyReference $property)
  {
    $this->property = $property;
  }

  public function getProperty()
  {
    return $this->property;
  }
}

class Analytify_Google_Service_Datastore_PropertyFilter extends Analytify_Google_Model
{
  public $operator;
  protected $propertyType = 'Analytify_Google_Service_Datastore_PropertyReference';
  protected $propertyDataType = '';
  protected $valueType = 'Analytify_Google_Service_Datastore_Value';
  protected $valueDataType = '';

  public function setOperator($operator)
  {
    $this->operator = $operator;
  }

  public function getOperator()
  {
    return $this->operator;
  }

  public function setProperty(Analytify_Google_Service_Datastore_PropertyReference $property)
  {
    $this->property = $property;
  }

  public function getProperty()
  {
    return $this->property;
  }

  public function setValue(Analytify_Google_Service_Datastore_Value $value)
  {
    $this->value = $value;
  }

  public function getValue()
  {
    return $this->value;
  }
}

class Analytify_Google_Service_Datastore_PropertyOrder extends Analytify_Google_Model
{
  public $direction;
  protected $propertyType = 'Analytify_Google_Service_Datastore_PropertyReference';
  protected $propertyDataType = '';

  public function setDirection($direction)
  {
    $this->direction = $direction;
  }

  public function getDirection()
  {
    return $this->direction;
  }

  public function setProperty(Analytify_Google_Service_Datastore_PropertyReference $property)
  {
    $this->property = $property;
  }

  public function getProperty()
  {
    return $this->property;
  }
}

class Analytify_Google_Service_Datastore_PropertyReference extends Analytify_Google_Model
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

class Analytify_Google_Service_Datastore_Query extends Analytify_Google_Collection
{
  public $endCursor;
  protected $filterType = 'Analytify_Google_Service_Datastore_Filter';
  protected $filterDataType = '';
  protected $groupByType = 'Analytify_Google_Service_Datastore_PropertyReference';
  protected $groupByDataType = 'array';
  protected $kindsType = 'Analytify_Google_Service_Datastore_KindExpression';
  protected $kindsDataType = 'array';
  public $limit;
  public $offset;
  protected $orderType = 'Analytify_Google_Service_Datastore_PropertyOrder';
  protected $orderDataType = 'array';
  protected $projectionType = 'Analytify_Google_Service_Datastore_PropertyExpression';
  protected $projectionDataType = 'array';
  public $startCursor;

  public function setEndCursor($endCursor)
  {
    $this->endCursor = $endCursor;
  }

  public function getEndCursor()
  {
    return $this->endCursor;
  }

  public function setFilter(Analytify_Google_Service_Datastore_Filter $filter)
  {
    $this->filter = $filter;
  }

  public function getFilter()
  {
    return $this->filter;
  }

  public function setGroupBy($groupBy)
  {
    $this->groupBy = $groupBy;
  }

  public function getGroupBy()
  {
    return $this->groupBy;
  }

  public function setKinds($kinds)
  {
    $this->kinds = $kinds;
  }

  public function getKinds()
  {
    return $this->kinds;
  }

  public function setLimit($limit)
  {
    $this->limit = $limit;
  }

  public function getLimit()
  {
    return $this->limit;
  }

  public function setOffset($offset)
  {
    $this->offset = $offset;
  }

  public function getOffset()
  {
    return $this->offset;
  }

  public function setOrder($order)
  {
    $this->order = $order;
  }

  public function getOrder()
  {
    return $this->order;
  }

  public function setProjection($projection)
  {
    $this->projection = $projection;
  }

  public function getProjection()
  {
    return $this->projection;
  }

  public function setStartCursor($startCursor)
  {
    $this->startCursor = $startCursor;
  }

  public function getStartCursor()
  {
    return $this->startCursor;
  }
}

class Analytify_Google_Service_Datastore_QueryResultBatch extends Analytify_Google_Collection
{
  public $endCursor;
  public $entityResultType;
  protected $entityResultsType = 'Analytify_Google_Service_Datastore_EntityResult';
  protected $entityResultsDataType = 'array';
  public $moreResults;
  public $skippedResults;

  public function setEndCursor($endCursor)
  {
    $this->endCursor = $endCursor;
  }

  public function getEndCursor()
  {
    return $this->endCursor;
  }

  public function setEntityResultType($entityResultType)
  {
    $this->entityResultType = $entityResultType;
  }

  public function getEntityResultType()
  {
    return $this->entityResultType;
  }

  public function setEntityResults($entityResults)
  {
    $this->entityResults = $entityResults;
  }

  public function getEntityResults()
  {
    return $this->entityResults;
  }

  public function setMoreResults($moreResults)
  {
    $this->moreResults = $moreResults;
  }

  public function getMoreResults()
  {
    return $this->moreResults;
  }

  public function setSkippedResults($skippedResults)
  {
    $this->skippedResults = $skippedResults;
  }

  public function getSkippedResults()
  {
    return $this->skippedResults;
  }
}

class Analytify_Google_Service_Datastore_ReadOptions extends Analytify_Google_Model
{
  public $readConsistency;
  public $transaction;

  public function setReadConsistency($readConsistency)
  {
    $this->readConsistency = $readConsistency;
  }

  public function getReadConsistency()
  {
    return $this->readConsistency;
  }

  public function setTransaction($transaction)
  {
    $this->transaction = $transaction;
  }

  public function getTransaction()
  {
    return $this->transaction;
  }
}

class Analytify_Google_Service_Datastore_ResponseHeader extends Analytify_Google_Model
{
  public $kind;

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }
}

class Analytify_Google_Service_Datastore_RollbackRequest extends Analytify_Google_Model
{
  public $transaction;

  public function setTransaction($transaction)
  {
    $this->transaction = $transaction;
  }

  public function getTransaction()
  {
    return $this->transaction;
  }
}

class Analytify_Google_Service_Datastore_RollbackResponse extends Analytify_Google_Model
{
  protected $headerType = 'Analytify_Google_Service_Datastore_ResponseHeader';
  protected $headerDataType = '';

  public function setHeader(Analytify_Google_Service_Datastore_ResponseHeader $header)
  {
    $this->header = $header;
  }

  public function getHeader()
  {
    return $this->header;
  }
}

class Analytify_Google_Service_Datastore_RunQueryRequest extends Analytify_Google_Model
{
  protected $gqlQueryType = 'Analytify_Google_Service_Datastore_GqlQuery';
  protected $gqlQueryDataType = '';
  protected $partitionIdType = 'Analytify_Google_Service_Datastore_PartitionId';
  protected $partitionIdDataType = '';
  protected $queryType = 'Analytify_Google_Service_Datastore_Query';
  protected $queryDataType = '';
  protected $readOptionsType = 'Analytify_Google_Service_Datastore_ReadOptions';
  protected $readOptionsDataType = '';

  public function setGqlQuery(Analytify_Google_Service_Datastore_GqlQuery $gqlQuery)
  {
    $this->gqlQuery = $gqlQuery;
  }

  public function getGqlQuery()
  {
    return $this->gqlQuery;
  }

  public function setPartitionId(Analytify_Google_Service_Datastore_PartitionId $partitionId)
  {
    $this->partitionId = $partitionId;
  }

  public function getPartitionId()
  {
    return $this->partitionId;
  }

  public function setQuery(Analytify_Google_Service_Datastore_Query $query)
  {
    $this->query = $query;
  }

  public function getQuery()
  {
    return $this->query;
  }

  public function setReadOptions(Analytify_Google_Service_Datastore_ReadOptions $readOptions)
  {
    $this->readOptions = $readOptions;
  }

  public function getReadOptions()
  {
    return $this->readOptions;
  }
}

class Analytify_Google_Service_Datastore_RunQueryResponse extends Analytify_Google_Model
{
  protected $batchType = 'Analytify_Google_Service_Datastore_QueryResultBatch';
  protected $batchDataType = '';
  protected $headerType = 'Analytify_Google_Service_Datastore_ResponseHeader';
  protected $headerDataType = '';

  public function setBatch(Analytify_Google_Service_Datastore_QueryResultBatch $batch)
  {
    $this->batch = $batch;
  }

  public function getBatch()
  {
    return $this->batch;
  }

  public function setHeader(Analytify_Google_Service_Datastore_ResponseHeader $header)
  {
    $this->header = $header;
  }

  public function getHeader()
  {
    return $this->header;
  }
}

class Analytify_Google_Service_Datastore_Value extends Analytify_Google_Collection
{
  public $blobKeyValue;
  public $blobValue;
  public $booleanValue;
  public $dateTimeValue;
  public $doubleValue;
  protected $entityValueType = 'Analytify_Google_Service_Datastore_Entity';
  protected $entityValueDataType = '';
  public $indexed;
  public $integerValue;
  protected $keyValueType = 'Analytify_Google_Service_Datastore_Key';
  protected $keyValueDataType = '';
  protected $listValueType = 'Analytify_Google_Service_Datastore_Value';
  protected $listValueDataType = 'array';
  public $meaning;
  public $stringValue;

  public function setBlobKeyValue($blobKeyValue)
  {
    $this->blobKeyValue = $blobKeyValue;
  }

  public function getBlobKeyValue()
  {
    return $this->blobKeyValue;
  }

  public function setBlobValue($blobValue)
  {
    $this->blobValue = $blobValue;
  }

  public function getBlobValue()
  {
    return $this->blobValue;
  }

  public function setBooleanValue($booleanValue)
  {
    $this->booleanValue = $booleanValue;
  }

  public function getBooleanValue()
  {
    return $this->booleanValue;
  }

  public function setDateTimeValue($dateTimeValue)
  {
    $this->dateTimeValue = $dateTimeValue;
  }

  public function getDateTimeValue()
  {
    return $this->dateTimeValue;
  }

  public function setDoubleValue($doubleValue)
  {
    $this->doubleValue = $doubleValue;
  }

  public function getDoubleValue()
  {
    return $this->doubleValue;
  }

  public function setEntityValue(Analytify_Google_Service_Datastore_Entity $entityValue)
  {
    $this->entityValue = $entityValue;
  }

  public function getEntityValue()
  {
    return $this->entityValue;
  }

  public function setIndexed($indexed)
  {
    $this->indexed = $indexed;
  }

  public function getIndexed()
  {
    return $this->indexed;
  }

  public function setIntegerValue($integerValue)
  {
    $this->integerValue = $integerValue;
  }

  public function getIntegerValue()
  {
    return $this->integerValue;
  }

  public function setKeyValue(Analytify_Google_Service_Datastore_Key $keyValue)
  {
    $this->keyValue = $keyValue;
  }

  public function getKeyValue()
  {
    return $this->keyValue;
  }

  public function setListValue($listValue)
  {
    $this->listValue = $listValue;
  }

  public function getListValue()
  {
    return $this->listValue;
  }

  public function setMeaning($meaning)
  {
    $this->meaning = $meaning;
  }

  public function getMeaning()
  {
    return $this->meaning;
  }

  public function setStringValue($stringValue)
  {
    $this->stringValue = $stringValue;
  }

  public function getStringValue()
  {
    return $this->stringValue;
  }
}
