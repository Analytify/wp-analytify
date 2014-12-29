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
 * Service definition for Dfareporting (v1.3).
 *
 * <p>
 * Lets you create, run and download reports.
 * </p>
 *
 * <p>
 * For more information about this service, see the API
 * <a href="https://developers.google.com/doubleclick-advertisers/reporting/" target="_blank">Documentation</a>
 * </p>
 *
 * @author Google, Inc.
 */
class Analytify_Google_Service_Dfareporting extends Analytify_Google_Service
{
  /** View and manage DoubleClick for Advertisers reports. */
  const DFAREPORTING = "https://www.googleapis.com/auth/dfareporting";

  public $dimensionValues;
  public $files;
  public $reports;
  public $reports_compatibleFields;
  public $reports_files;
  public $userProfiles;
  

  /**
   * Constructs the internal representation of the Dfareporting service.
   *
   * @param Analytify_Google_Client $client
   */
  public function __construct(Analytify_Google_Client $client)
  {
    parent::__construct($client);
    $this->servicePath = 'dfareporting/v1.3/';
    $this->version = 'v1.3';
    $this->serviceName = 'dfareporting';

    $this->dimensionValues = new Analytify_Google_Service_Dfareporting_DimensionValues_Resource(
        $this,
        $this->serviceName,
        'dimensionValues',
        array(
          'methods' => array(
            'query' => array(
              'path' => 'userprofiles/{profileId}/dimensionvalues/query',
              'httpMethod' => 'POST',
              'parameters' => array(
                'profileId' => array(
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
    $this->files = new Analytify_Google_Service_Dfareporting_Files_Resource(
        $this,
        $this->serviceName,
        'files',
        array(
          'methods' => array(
            'get' => array(
              'path' => 'reports/{reportId}/files/{fileId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'reportId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'fileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => 'userprofiles/{profileId}/files',
              'httpMethod' => 'GET',
              'parameters' => array(
                'profileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'sortField' => array(
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
                'sortOrder' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'scope' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
              ),
            ),
          )
        )
    );
    $this->reports = new Analytify_Google_Service_Dfareporting_Reports_Resource(
        $this,
        $this->serviceName,
        'reports',
        array(
          'methods' => array(
            'delete' => array(
              'path' => 'userprofiles/{profileId}/reports/{reportId}',
              'httpMethod' => 'DELETE',
              'parameters' => array(
                'profileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'reportId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'get' => array(
              'path' => 'userprofiles/{profileId}/reports/{reportId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'profileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'reportId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'insert' => array(
              'path' => 'userprofiles/{profileId}/reports',
              'httpMethod' => 'POST',
              'parameters' => array(
                'profileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => 'userprofiles/{profileId}/reports',
              'httpMethod' => 'GET',
              'parameters' => array(
                'profileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'sortField' => array(
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
                'sortOrder' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
                'scope' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
              ),
            ),'patch' => array(
              'path' => 'userprofiles/{profileId}/reports/{reportId}',
              'httpMethod' => 'PATCH',
              'parameters' => array(
                'profileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'reportId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'run' => array(
              'path' => 'userprofiles/{profileId}/reports/{reportId}/run',
              'httpMethod' => 'POST',
              'parameters' => array(
                'profileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'reportId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'synchronous' => array(
                  'location' => 'query',
                  'type' => 'boolean',
                ),
              ),
            ),'update' => array(
              'path' => 'userprofiles/{profileId}/reports/{reportId}',
              'httpMethod' => 'PUT',
              'parameters' => array(
                'profileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'reportId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),
          )
        )
    );
    $this->reports_compatibleFields = new Analytify_Google_Service_Dfareporting_ReportsCompatibleFields_Resource(
        $this,
        $this->serviceName,
        'compatibleFields',
        array(
          'methods' => array(
            'query' => array(
              'path' => 'userprofiles/{profileId}/reports/compatiblefields/query',
              'httpMethod' => 'POST',
              'parameters' => array(
                'profileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),
          )
        )
    );
    $this->reports_files = new Analytify_Google_Service_Dfareporting_ReportsFiles_Resource(
        $this,
        $this->serviceName,
        'files',
        array(
          'methods' => array(
            'get' => array(
              'path' => 'userprofiles/{profileId}/reports/{reportId}/files/{fileId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'profileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'reportId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'fileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => 'userprofiles/{profileId}/reports/{reportId}/files',
              'httpMethod' => 'GET',
              'parameters' => array(
                'profileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'reportId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
                'sortField' => array(
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
                'sortOrder' => array(
                  'location' => 'query',
                  'type' => 'string',
                ),
              ),
            ),
          )
        )
    );
    $this->userProfiles = new Analytify_Google_Service_Dfareporting_UserProfiles_Resource(
        $this,
        $this->serviceName,
        'userProfiles',
        array(
          'methods' => array(
            'get' => array(
              'path' => 'userprofiles/{profileId}',
              'httpMethod' => 'GET',
              'parameters' => array(
                'profileId' => array(
                  'location' => 'path',
                  'type' => 'string',
                  'required' => true,
                ),
              ),
            ),'list' => array(
              'path' => 'userprofiles',
              'httpMethod' => 'GET',
              'parameters' => array(),
            ),
          )
        )
    );
  }
}


/**
 * The "dimensionValues" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dfareportingService = new Analytify_Google_Service_Dfareporting(...);
 *   $dimensionValues = $dfareportingService->dimensionValues;
 *  </code>
 */
class Analytify_Google_Service_Dfareporting_DimensionValues_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Retrieves list of report dimension values for a list of filters.
   * (dimensionValues.query)
   *
   * @param string $profileId
   * The DFA user profile ID.
   * @param Analytify_Google_DimensionValueRequest $postBody
   * @param array $optParams Optional parameters.
   *
   * @opt_param string pageToken
   * The value of the nextToken from the previous result page.
   * @opt_param int maxResults
   * Maximum number of results to return.
   * @return Analytify_Google_Service_Dfareporting_DimensionValueList
   */
  public function query($profileId, Analytify_Google_Service_Dfareporting_DimensionValueRequest $postBody, $optParams = array())
  {
    $params = array('profileId' => $profileId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('query', array($params), "Analytify_Google_Service_Dfareporting_DimensionValueList");
  }
}

/**
 * The "files" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dfareportingService = new Analytify_Google_Service_Dfareporting(...);
 *   $files = $dfareportingService->files;
 *  </code>
 */
class Analytify_Google_Service_Dfareporting_Files_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Retrieves a report file by its report ID and file ID. (files.get)
   *
   * @param string $reportId
   * The ID of the report.
   * @param string $fileId
   * The ID of the report file.
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dfareporting_DfareportingFile
   */
  public function get($reportId, $fileId, $optParams = array())
  {
    $params = array('reportId' => $reportId, 'fileId' => $fileId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Analytify_Google_Service_Dfareporting_DfareportingFile");
  }
  /**
   * Lists files for a user profile. (files.listFiles)
   *
   * @param string $profileId
   * The DFA profile ID.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string sortField
   * The field by which to sort the list.
   * @opt_param int maxResults
   * Maximum number of results to return.
   * @opt_param string pageToken
   * The value of the nextToken from the previous result page.
   * @opt_param string sortOrder
   * Order of sorted results, default is 'DESCENDING'.
   * @opt_param string scope
   * The scope that defines which results are returned, default is 'MINE'.
   * @return Analytify_Google_Service_Dfareporting_FileList
   */
  public function listFiles($profileId, $optParams = array())
  {
    $params = array('profileId' => $profileId);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Analytify_Google_Service_Dfareporting_FileList");
  }
}

/**
 * The "reports" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dfareportingService = new Analytify_Google_Service_Dfareporting(...);
 *   $reports = $dfareportingService->reports;
 *  </code>
 */
class Analytify_Google_Service_Dfareporting_Reports_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Deletes a report by its ID. (reports.delete)
   *
   * @param string $profileId
   * The DFA user profile ID.
   * @param string $reportId
   * The ID of the report.
   * @param array $optParams Optional parameters.
   */
  public function delete($profileId, $reportId, $optParams = array())
  {
    $params = array('profileId' => $profileId, 'reportId' => $reportId);
    $params = array_merge($params, $optParams);
    return $this->call('delete', array($params));
  }
  /**
   * Retrieves a report by its ID. (reports.get)
   *
   * @param string $profileId
   * The DFA user profile ID.
   * @param string $reportId
   * The ID of the report.
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dfareporting_Report
   */
  public function get($profileId, $reportId, $optParams = array())
  {
    $params = array('profileId' => $profileId, 'reportId' => $reportId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Analytify_Google_Service_Dfareporting_Report");
  }
  /**
   * Creates a report. (reports.insert)
   *
   * @param string $profileId
   * The DFA user profile ID.
   * @param Analytify_Google_Report $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dfareporting_Report
   */
  public function insert($profileId, Analytify_Google_Service_Dfareporting_Report $postBody, $optParams = array())
  {
    $params = array('profileId' => $profileId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('insert', array($params), "Analytify_Google_Service_Dfareporting_Report");
  }
  /**
   * Retrieves list of reports. (reports.listReports)
   *
   * @param string $profileId
   * The DFA user profile ID.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string sortField
   * The field by which to sort the list.
   * @opt_param int maxResults
   * Maximum number of results to return.
   * @opt_param string pageToken
   * The value of the nextToken from the previous result page.
   * @opt_param string sortOrder
   * Order of sorted results, default is 'DESCENDING'.
   * @opt_param string scope
   * The scope that defines which results are returned, default is 'MINE'.
   * @return Analytify_Google_Service_Dfareporting_ReportList
   */
  public function listReports($profileId, $optParams = array())
  {
    $params = array('profileId' => $profileId);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Analytify_Google_Service_Dfareporting_ReportList");
  }
  /**
   * Updates a report. This method supports patch semantics. (reports.patch)
   *
   * @param string $profileId
   * The DFA user profile ID.
   * @param string $reportId
   * The ID of the report.
   * @param Analytify_Google_Report $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dfareporting_Report
   */
  public function patch($profileId, $reportId, Analytify_Google_Service_Dfareporting_Report $postBody, $optParams = array())
  {
    $params = array('profileId' => $profileId, 'reportId' => $reportId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('patch', array($params), "Analytify_Google_Service_Dfareporting_Report");
  }
  /**
   * Runs a report. (reports.run)
   *
   * @param string $profileId
   * The DFA profile ID.
   * @param string $reportId
   * The ID of the report.
   * @param array $optParams Optional parameters.
   *
   * @opt_param bool synchronous
   * If set and true, tries to run the report synchronously.
   * @return Analytify_Google_Service_Dfareporting_DfareportingFile
   */
  public function run($profileId, $reportId, $optParams = array())
  {
    $params = array('profileId' => $profileId, 'reportId' => $reportId);
    $params = array_merge($params, $optParams);
    return $this->call('run', array($params), "Analytify_Google_Service_Dfareporting_DfareportingFile");
  }
  /**
   * Updates a report. (reports.update)
   *
   * @param string $profileId
   * The DFA user profile ID.
   * @param string $reportId
   * The ID of the report.
   * @param Analytify_Google_Report $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dfareporting_Report
   */
  public function update($profileId, $reportId, Analytify_Google_Service_Dfareporting_Report $postBody, $optParams = array())
  {
    $params = array('profileId' => $profileId, 'reportId' => $reportId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('update', array($params), "Analytify_Google_Service_Dfareporting_Report");
  }
}

/**
 * The "compatibleFields" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dfareportingService = new Analytify_Google_Service_Dfareporting(...);
 *   $compatibleFields = $dfareportingService->compatibleFields;
 *  </code>
 */
class Analytify_Google_Service_Dfareporting_ReportsCompatibleFields_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Returns the fields that are compatible to be selected in the respective
   * sections of a report criteria, given the fields already selected in the input
   * report and user permissions. (compatibleFields.query)
   *
   * @param string $profileId
   * The DFA user profile ID.
   * @param Analytify_Google_Report $postBody
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dfareporting_CompatibleFields
   */
  public function query($profileId, Analytify_Google_Service_Dfareporting_Report $postBody, $optParams = array())
  {
    $params = array('profileId' => $profileId, 'postBody' => $postBody);
    $params = array_merge($params, $optParams);
    return $this->call('query', array($params), "Analytify_Google_Service_Dfareporting_CompatibleFields");
  }
}
/**
 * The "files" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dfareportingService = new Analytify_Google_Service_Dfareporting(...);
 *   $files = $dfareportingService->files;
 *  </code>
 */
class Analytify_Google_Service_Dfareporting_ReportsFiles_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Retrieves a report file. (files.get)
   *
   * @param string $profileId
   * The DFA profile ID.
   * @param string $reportId
   * The ID of the report.
   * @param string $fileId
   * The ID of the report file.
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dfareporting_DfareportingFile
   */
  public function get($profileId, $reportId, $fileId, $optParams = array())
  {
    $params = array('profileId' => $profileId, 'reportId' => $reportId, 'fileId' => $fileId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Analytify_Google_Service_Dfareporting_DfareportingFile");
  }
  /**
   * Lists files for a report. (files.listReportsFiles)
   *
   * @param string $profileId
   * The DFA profile ID.
   * @param string $reportId
   * The ID of the parent report.
   * @param array $optParams Optional parameters.
   *
   * @opt_param string sortField
   * The field by which to sort the list.
   * @opt_param int maxResults
   * Maximum number of results to return.
   * @opt_param string pageToken
   * The value of the nextToken from the previous result page.
   * @opt_param string sortOrder
   * Order of sorted results, default is 'DESCENDING'.
   * @return Analytify_Google_Service_Dfareporting_FileList
   */
  public function listReportsFiles($profileId, $reportId, $optParams = array())
  {
    $params = array('profileId' => $profileId, 'reportId' => $reportId);
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Analytify_Google_Service_Dfareporting_FileList");
  }
}

/**
 * The "userProfiles" collection of methods.
 * Typical usage is:
 *  <code>
 *   $dfareportingService = new Analytify_Google_Service_Dfareporting(...);
 *   $userProfiles = $dfareportingService->userProfiles;
 *  </code>
 */
class Analytify_Google_Service_Dfareporting_UserProfiles_Resource extends Analytify_Google_Service_Resource
{

  /**
   * Gets one user profile by ID. (userProfiles.get)
   *
   * @param string $profileId
   * The user profile ID.
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dfareporting_UserProfile
   */
  public function get($profileId, $optParams = array())
  {
    $params = array('profileId' => $profileId);
    $params = array_merge($params, $optParams);
    return $this->call('get', array($params), "Analytify_Google_Service_Dfareporting_UserProfile");
  }
  /**
   * Retrieves list of user profiles for a user. (userProfiles.listUserProfiles)
   *
   * @param array $optParams Optional parameters.
   * @return Analytify_Google_Service_Dfareporting_UserProfileList
   */
  public function listUserProfiles($optParams = array())
  {
    $params = array();
    $params = array_merge($params, $optParams);
    return $this->call('list', array($params), "Analytify_Google_Service_Dfareporting_UserProfileList");
  }
}




class Analytify_Google_Service_Dfareporting_Activities extends Analytify_Google_Collection
{
  protected $filtersType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $filtersDataType = 'array';
  public $kind;
  public $metricNames;

  public function setFilters($filters)
  {
    $this->filters = $filters;
  }

  public function getFilters()
  {
    return $this->filters;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setMetricNames($metricNames)
  {
    $this->metricNames = $metricNames;
  }

  public function getMetricNames()
  {
    return $this->metricNames;
  }
}

class Analytify_Google_Service_Dfareporting_CompatibleFields extends Analytify_Google_Model
{
  protected $crossDimensionReachReportCompatibleFieldsType = 'Analytify_Google_Service_Dfareporting_CrossDimensionReachReportCompatibleFields';
  protected $crossDimensionReachReportCompatibleFieldsDataType = '';
  protected $floodlightReportCompatibleFieldsType = 'Analytify_Google_Service_Dfareporting_FloodlightReportCompatibleFields';
  protected $floodlightReportCompatibleFieldsDataType = '';
  public $kind;
  protected $pathToConversionReportCompatibleFieldsType = 'Analytify_Google_Service_Dfareporting_PathToConversionReportCompatibleFields';
  protected $pathToConversionReportCompatibleFieldsDataType = '';
  protected $reachReportCompatibleFieldsType = 'Analytify_Google_Service_Dfareporting_ReachReportCompatibleFields';
  protected $reachReportCompatibleFieldsDataType = '';
  protected $reportCompatibleFieldsType = 'Analytify_Google_Service_Dfareporting_ReportCompatibleFields';
  protected $reportCompatibleFieldsDataType = '';

  public function setCrossDimensionReachReportCompatibleFields(Analytify_Google_Service_Dfareporting_CrossDimensionReachReportCompatibleFields $crossDimensionReachReportCompatibleFields)
  {
    $this->crossDimensionReachReportCompatibleFields = $crossDimensionReachReportCompatibleFields;
  }

  public function getCrossDimensionReachReportCompatibleFields()
  {
    return $this->crossDimensionReachReportCompatibleFields;
  }

  public function setFloodlightReportCompatibleFields(Analytify_Google_Service_Dfareporting_FloodlightReportCompatibleFields $floodlightReportCompatibleFields)
  {
    $this->floodlightReportCompatibleFields = $floodlightReportCompatibleFields;
  }

  public function getFloodlightReportCompatibleFields()
  {
    return $this->floodlightReportCompatibleFields;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setPathToConversionReportCompatibleFields(Analytify_Google_Service_Dfareporting_PathToConversionReportCompatibleFields $pathToConversionReportCompatibleFields)
  {
    $this->pathToConversionReportCompatibleFields = $pathToConversionReportCompatibleFields;
  }

  public function getPathToConversionReportCompatibleFields()
  {
    return $this->pathToConversionReportCompatibleFields;
  }

  public function setReachReportCompatibleFields(Analytify_Google_Service_Dfareporting_ReachReportCompatibleFields $reachReportCompatibleFields)
  {
    $this->reachReportCompatibleFields = $reachReportCompatibleFields;
  }

  public function getReachReportCompatibleFields()
  {
    return $this->reachReportCompatibleFields;
  }

  public function setReportCompatibleFields(Analytify_Google_Service_Dfareporting_ReportCompatibleFields $reportCompatibleFields)
  {
    $this->reportCompatibleFields = $reportCompatibleFields;
  }

  public function getReportCompatibleFields()
  {
    return $this->reportCompatibleFields;
  }
}

class Analytify_Google_Service_Dfareporting_CrossDimensionReachReportCompatibleFields extends Analytify_Google_Collection
{
  protected $breakdownType = 'Analytify_Google_Service_Dfareporting_Dimension';
  protected $breakdownDataType = 'array';
  protected $dimensionFiltersType = 'Analytify_Google_Service_Dfareporting_Dimension';
  protected $dimensionFiltersDataType = 'array';
  public $kind;
  protected $metricsType = 'Analytify_Google_Service_Dfareporting_Metric';
  protected $metricsDataType = 'array';
  protected $overlapMetricsType = 'Analytify_Google_Service_Dfareporting_Metric';
  protected $overlapMetricsDataType = 'array';

  public function setBreakdown($breakdown)
  {
    $this->breakdown = $breakdown;
  }

  public function getBreakdown()
  {
    return $this->breakdown;
  }

  public function setDimensionFilters($dimensionFilters)
  {
    $this->dimensionFilters = $dimensionFilters;
  }

  public function getDimensionFilters()
  {
    return $this->dimensionFilters;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }

  public function getMetrics()
  {
    return $this->metrics;
  }

  public function setOverlapMetrics($overlapMetrics)
  {
    $this->overlapMetrics = $overlapMetrics;
  }

  public function getOverlapMetrics()
  {
    return $this->overlapMetrics;
  }
}

class Analytify_Google_Service_Dfareporting_CustomRichMediaEvents extends Analytify_Google_Collection
{
  protected $filteredEventIdsType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $filteredEventIdsDataType = 'array';
  public $kind;

  public function setFilteredEventIds($filteredEventIds)
  {
    $this->filteredEventIds = $filteredEventIds;
  }

  public function getFilteredEventIds()
  {
    return $this->filteredEventIds;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }
}

class Analytify_Google_Service_Dfareporting_DateRange extends Analytify_Google_Model
{
  public $endDate;
  public $kind;
  public $relativeDateRange;
  public $startDate;

  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }

  public function getEndDate()
  {
    return $this->endDate;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setRelativeDateRange($relativeDateRange)
  {
    $this->relativeDateRange = $relativeDateRange;
  }

  public function getRelativeDateRange()
  {
    return $this->relativeDateRange;
  }

  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }

  public function getStartDate()
  {
    return $this->startDate;
  }
}

class Analytify_Google_Service_Dfareporting_DfareportingFile extends Analytify_Google_Model
{
  protected $dateRangeType = 'Analytify_Google_Service_Dfareporting_DateRange';
  protected $dateRangeDataType = '';
  public $etag;
  public $fileName;
  public $format;
  public $id;
  public $kind;
  public $lastModifiedTime;
  public $reportId;
  public $status;
  protected $urlsType = 'Analytify_Google_Service_Dfareporting_DfareportingFileUrls';
  protected $urlsDataType = '';

  public function setDateRange(Analytify_Google_Service_Dfareporting_DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }

  public function getDateRange()
  {
    return $this->dateRange;
  }

  public function setEtag($etag)
  {
    $this->etag = $etag;
  }

  public function getEtag()
  {
    return $this->etag;
  }

  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
  }

  public function getFileName()
  {
    return $this->fileName;
  }

  public function setFormat($format)
  {
    $this->format = $format;
  }

  public function getFormat()
  {
    return $this->format;
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

  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }

  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }

  public function setReportId($reportId)
  {
    $this->reportId = $reportId;
  }

  public function getReportId()
  {
    return $this->reportId;
  }

  public function setStatus($status)
  {
    $this->status = $status;
  }

  public function getStatus()
  {
    return $this->status;
  }

  public function setUrls(Analytify_Google_Service_Dfareporting_DfareportingFileUrls $urls)
  {
    $this->urls = $urls;
  }

  public function getUrls()
  {
    return $this->urls;
  }
}

class Analytify_Google_Service_Dfareporting_DfareportingFileUrls extends Analytify_Google_Model
{
  public $apiUrl;
  public $browserUrl;

  public function setApiUrl($apiUrl)
  {
    $this->apiUrl = $apiUrl;
  }

  public function getApiUrl()
  {
    return $this->apiUrl;
  }

  public function setBrowserUrl($browserUrl)
  {
    $this->browserUrl = $browserUrl;
  }

  public function getBrowserUrl()
  {
    return $this->browserUrl;
  }
}

class Analytify_Google_Service_Dfareporting_Dimension extends Analytify_Google_Model
{
  public $kind;
  public $name;

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

class Analytify_Google_Service_Dfareporting_DimensionFilter extends Analytify_Google_Model
{
  public $dimensionName;
  public $kind;
  public $value;

  public function setDimensionName($dimensionName)
  {
    $this->dimensionName = $dimensionName;
  }

  public function getDimensionName()
  {
    return $this->dimensionName;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setValue($value)
  {
    $this->value = $value;
  }

  public function getValue()
  {
    return $this->value;
  }
}

class Analytify_Google_Service_Dfareporting_DimensionValue extends Analytify_Google_Model
{
  public $dimensionName;
  public $etag;
  public $id;
  public $kind;
  public $matchType;
  public $value;

  public function setDimensionName($dimensionName)
  {
    $this->dimensionName = $dimensionName;
  }

  public function getDimensionName()
  {
    return $this->dimensionName;
  }

  public function setEtag($etag)
  {
    $this->etag = $etag;
  }

  public function getEtag()
  {
    return $this->etag;
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

  public function setMatchType($matchType)
  {
    $this->matchType = $matchType;
  }

  public function getMatchType()
  {
    return $this->matchType;
  }

  public function setValue($value)
  {
    $this->value = $value;
  }

  public function getValue()
  {
    return $this->value;
  }
}

class Analytify_Google_Service_Dfareporting_DimensionValueList extends Analytify_Google_Collection
{
  public $etag;
  protected $itemsType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $itemsDataType = 'array';
  public $kind;
  public $nextPageToken;

  public function setEtag($etag)
  {
    $this->etag = $etag;
  }

  public function getEtag()
  {
    return $this->etag;
  }

  public function setItems($items)
  {
    $this->items = $items;
  }

  public function getItems()
  {
    return $this->items;
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

class Analytify_Google_Service_Dfareporting_DimensionValueRequest extends Analytify_Google_Collection
{
  public $dimensionName;
  public $endDate;
  protected $filtersType = 'Analytify_Google_Service_Dfareporting_DimensionFilter';
  protected $filtersDataType = 'array';
  public $kind;
  public $startDate;

  public function setDimensionName($dimensionName)
  {
    $this->dimensionName = $dimensionName;
  }

  public function getDimensionName()
  {
    return $this->dimensionName;
  }

  public function setEndDate($endDate)
  {
    $this->endDate = $endDate;
  }

  public function getEndDate()
  {
    return $this->endDate;
  }

  public function setFilters($filters)
  {
    $this->filters = $filters;
  }

  public function getFilters()
  {
    return $this->filters;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }

  public function getStartDate()
  {
    return $this->startDate;
  }
}

class Analytify_Google_Service_Dfareporting_FileList extends Analytify_Google_Collection
{
  public $etag;
  protected $itemsType = 'Analytify_Google_Service_Dfareporting_DfareportingFile';
  protected $itemsDataType = 'array';
  public $kind;
  public $nextPageToken;

  public function setEtag($etag)
  {
    $this->etag = $etag;
  }

  public function getEtag()
  {
    return $this->etag;
  }

  public function setItems($items)
  {
    $this->items = $items;
  }

  public function getItems()
  {
    return $this->items;
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

class Analytify_Google_Service_Dfareporting_FloodlightReportCompatibleFields extends Analytify_Google_Collection
{
  protected $dimensionFiltersType = 'Analytify_Google_Service_Dfareporting_Dimension';
  protected $dimensionFiltersDataType = 'array';
  protected $dimensionsType = 'Analytify_Google_Service_Dfareporting_Dimension';
  protected $dimensionsDataType = 'array';
  public $kind;
  protected $metricsType = 'Analytify_Google_Service_Dfareporting_Metric';
  protected $metricsDataType = 'array';

  public function setDimensionFilters($dimensionFilters)
  {
    $this->dimensionFilters = $dimensionFilters;
  }

  public function getDimensionFilters()
  {
    return $this->dimensionFilters;
  }

  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }

  public function getDimensions()
  {
    return $this->dimensions;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }

  public function getMetrics()
  {
    return $this->metrics;
  }
}

class Analytify_Google_Service_Dfareporting_Metric extends Analytify_Google_Model
{
  public $kind;
  public $name;

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

class Analytify_Google_Service_Dfareporting_PathToConversionReportCompatibleFields extends Analytify_Google_Collection
{
  protected $conversionDimensionsType = 'Analytify_Google_Service_Dfareporting_Dimension';
  protected $conversionDimensionsDataType = 'array';
  protected $customFloodlightVariablesType = 'Analytify_Google_Service_Dfareporting_Dimension';
  protected $customFloodlightVariablesDataType = 'array';
  public $kind;
  protected $metricsType = 'Analytify_Google_Service_Dfareporting_Metric';
  protected $metricsDataType = 'array';
  protected $perInteractionDimensionsType = 'Analytify_Google_Service_Dfareporting_Dimension';
  protected $perInteractionDimensionsDataType = 'array';

  public function setConversionDimensions($conversionDimensions)
  {
    $this->conversionDimensions = $conversionDimensions;
  }

  public function getConversionDimensions()
  {
    return $this->conversionDimensions;
  }

  public function setCustomFloodlightVariables($customFloodlightVariables)
  {
    $this->customFloodlightVariables = $customFloodlightVariables;
  }

  public function getCustomFloodlightVariables()
  {
    return $this->customFloodlightVariables;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }

  public function getMetrics()
  {
    return $this->metrics;
  }

  public function setPerInteractionDimensions($perInteractionDimensions)
  {
    $this->perInteractionDimensions = $perInteractionDimensions;
  }

  public function getPerInteractionDimensions()
  {
    return $this->perInteractionDimensions;
  }
}

class Analytify_Google_Service_Dfareporting_ReachReportCompatibleFields extends Analytify_Google_Collection
{
  protected $dimensionFiltersType = 'Analytify_Google_Service_Dfareporting_Dimension';
  protected $dimensionFiltersDataType = 'array';
  protected $dimensionsType = 'Analytify_Google_Service_Dfareporting_Dimension';
  protected $dimensionsDataType = 'array';
  public $kind;
  protected $metricsType = 'Analytify_Google_Service_Dfareporting_Metric';
  protected $metricsDataType = 'array';
  protected $pivotedActivityMetricsType = 'Analytify_Google_Service_Dfareporting_Metric';
  protected $pivotedActivityMetricsDataType = 'array';
  protected $reachByFrequencyMetricsType = 'Analytify_Google_Service_Dfareporting_Metric';
  protected $reachByFrequencyMetricsDataType = 'array';

  public function setDimensionFilters($dimensionFilters)
  {
    $this->dimensionFilters = $dimensionFilters;
  }

  public function getDimensionFilters()
  {
    return $this->dimensionFilters;
  }

  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }

  public function getDimensions()
  {
    return $this->dimensions;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }

  public function getMetrics()
  {
    return $this->metrics;
  }

  public function setPivotedActivityMetrics($pivotedActivityMetrics)
  {
    $this->pivotedActivityMetrics = $pivotedActivityMetrics;
  }

  public function getPivotedActivityMetrics()
  {
    return $this->pivotedActivityMetrics;
  }

  public function setReachByFrequencyMetrics($reachByFrequencyMetrics)
  {
    $this->reachByFrequencyMetrics = $reachByFrequencyMetrics;
  }

  public function getReachByFrequencyMetrics()
  {
    return $this->reachByFrequencyMetrics;
  }
}

class Analytify_Google_Service_Dfareporting_Recipient extends Analytify_Google_Model
{
  public $deliveryType;
  public $email;
  public $kind;

  public function setDeliveryType($deliveryType)
  {
    $this->deliveryType = $deliveryType;
  }

  public function getDeliveryType()
  {
    return $this->deliveryType;
  }

  public function setEmail($email)
  {
    $this->email = $email;
  }

  public function getEmail()
  {
    return $this->email;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }
}

class Analytify_Google_Service_Dfareporting_Report extends Analytify_Google_Model
{
  public $accountId;
  protected $activeGrpCriteriaType = 'Analytify_Google_Service_Dfareporting_ReportActiveGrpCriteria';
  protected $activeGrpCriteriaDataType = '';
  protected $criteriaType = 'Analytify_Google_Service_Dfareporting_ReportCriteria';
  protected $criteriaDataType = '';
  protected $crossDimensionReachCriteriaType = 'Analytify_Google_Service_Dfareporting_ReportCrossDimensionReachCriteria';
  protected $crossDimensionReachCriteriaDataType = '';
  protected $deliveryType = 'Analytify_Google_Service_Dfareporting_ReportDelivery';
  protected $deliveryDataType = '';
  public $etag;
  public $fileName;
  protected $floodlightCriteriaType = 'Analytify_Google_Service_Dfareporting_ReportFloodlightCriteria';
  protected $floodlightCriteriaDataType = '';
  public $format;
  public $id;
  public $kind;
  public $lastModifiedTime;
  public $name;
  public $ownerProfileId;
  protected $pathToConversionCriteriaType = 'Analytify_Google_Service_Dfareporting_ReportPathToConversionCriteria';
  protected $pathToConversionCriteriaDataType = '';
  protected $reachCriteriaType = 'Analytify_Google_Service_Dfareporting_ReportReachCriteria';
  protected $reachCriteriaDataType = '';
  protected $scheduleType = 'Analytify_Google_Service_Dfareporting_ReportSchedule';
  protected $scheduleDataType = '';
  public $subAccountId;
  public $type;

  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }

  public function getAccountId()
  {
    return $this->accountId;
  }

  public function setActiveGrpCriteria(Analytify_Google_Service_Dfareporting_ReportActiveGrpCriteria $activeGrpCriteria)
  {
    $this->activeGrpCriteria = $activeGrpCriteria;
  }

  public function getActiveGrpCriteria()
  {
    return $this->activeGrpCriteria;
  }

  public function setCriteria(Analytify_Google_Service_Dfareporting_ReportCriteria $criteria)
  {
    $this->criteria = $criteria;
  }

  public function getCriteria()
  {
    return $this->criteria;
  }

  public function setCrossDimensionReachCriteria(Analytify_Google_Service_Dfareporting_ReportCrossDimensionReachCriteria $crossDimensionReachCriteria)
  {
    $this->crossDimensionReachCriteria = $crossDimensionReachCriteria;
  }

  public function getCrossDimensionReachCriteria()
  {
    return $this->crossDimensionReachCriteria;
  }

  public function setDelivery(Analytify_Google_Service_Dfareporting_ReportDelivery $delivery)
  {
    $this->delivery = $delivery;
  }

  public function getDelivery()
  {
    return $this->delivery;
  }

  public function setEtag($etag)
  {
    $this->etag = $etag;
  }

  public function getEtag()
  {
    return $this->etag;
  }

  public function setFileName($fileName)
  {
    $this->fileName = $fileName;
  }

  public function getFileName()
  {
    return $this->fileName;
  }

  public function setFloodlightCriteria(Analytify_Google_Service_Dfareporting_ReportFloodlightCriteria $floodlightCriteria)
  {
    $this->floodlightCriteria = $floodlightCriteria;
  }

  public function getFloodlightCriteria()
  {
    return $this->floodlightCriteria;
  }

  public function setFormat($format)
  {
    $this->format = $format;
  }

  public function getFormat()
  {
    return $this->format;
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

  public function setLastModifiedTime($lastModifiedTime)
  {
    $this->lastModifiedTime = $lastModifiedTime;
  }

  public function getLastModifiedTime()
  {
    return $this->lastModifiedTime;
  }

  public function setName($name)
  {
    $this->name = $name;
  }

  public function getName()
  {
    return $this->name;
  }

  public function setOwnerProfileId($ownerProfileId)
  {
    $this->ownerProfileId = $ownerProfileId;
  }

  public function getOwnerProfileId()
  {
    return $this->ownerProfileId;
  }

  public function setPathToConversionCriteria(Analytify_Google_Service_Dfareporting_ReportPathToConversionCriteria $pathToConversionCriteria)
  {
    $this->pathToConversionCriteria = $pathToConversionCriteria;
  }

  public function getPathToConversionCriteria()
  {
    return $this->pathToConversionCriteria;
  }

  public function setReachCriteria(Analytify_Google_Service_Dfareporting_ReportReachCriteria $reachCriteria)
  {
    $this->reachCriteria = $reachCriteria;
  }

  public function getReachCriteria()
  {
    return $this->reachCriteria;
  }

  public function setSchedule(Analytify_Google_Service_Dfareporting_ReportSchedule $schedule)
  {
    $this->schedule = $schedule;
  }

  public function getSchedule()
  {
    return $this->schedule;
  }

  public function setSubAccountId($subAccountId)
  {
    $this->subAccountId = $subAccountId;
  }

  public function getSubAccountId()
  {
    return $this->subAccountId;
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

class Analytify_Google_Service_Dfareporting_ReportActiveGrpCriteria extends Analytify_Google_Collection
{
  protected $dateRangeType = 'Analytify_Google_Service_Dfareporting_DateRange';
  protected $dateRangeDataType = '';
  protected $dimensionFiltersType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $dimensionFiltersDataType = 'array';
  protected $dimensionsType = 'Analytify_Google_Service_Dfareporting_SortedDimension';
  protected $dimensionsDataType = 'array';
  public $metricNames;

  public function setDateRange(Analytify_Google_Service_Dfareporting_DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }

  public function getDateRange()
  {
    return $this->dateRange;
  }

  public function setDimensionFilters($dimensionFilters)
  {
    $this->dimensionFilters = $dimensionFilters;
  }

  public function getDimensionFilters()
  {
    return $this->dimensionFilters;
  }

  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }

  public function getDimensions()
  {
    return $this->dimensions;
  }

  public function setMetricNames($metricNames)
  {
    $this->metricNames = $metricNames;
  }

  public function getMetricNames()
  {
    return $this->metricNames;
  }
}

class Analytify_Google_Service_Dfareporting_ReportCompatibleFields extends Analytify_Google_Collection
{
  protected $dimensionFiltersType = 'Analytify_Google_Service_Dfareporting_Dimension';
  protected $dimensionFiltersDataType = 'array';
  protected $dimensionsType = 'Analytify_Google_Service_Dfareporting_Dimension';
  protected $dimensionsDataType = 'array';
  public $kind;
  protected $metricsType = 'Analytify_Google_Service_Dfareporting_Metric';
  protected $metricsDataType = 'array';
  protected $pivotedActivityMetricsType = 'Analytify_Google_Service_Dfareporting_Metric';
  protected $pivotedActivityMetricsDataType = 'array';

  public function setDimensionFilters($dimensionFilters)
  {
    $this->dimensionFilters = $dimensionFilters;
  }

  public function getDimensionFilters()
  {
    return $this->dimensionFilters;
  }

  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }

  public function getDimensions()
  {
    return $this->dimensions;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setMetrics($metrics)
  {
    $this->metrics = $metrics;
  }

  public function getMetrics()
  {
    return $this->metrics;
  }

  public function setPivotedActivityMetrics($pivotedActivityMetrics)
  {
    $this->pivotedActivityMetrics = $pivotedActivityMetrics;
  }

  public function getPivotedActivityMetrics()
  {
    return $this->pivotedActivityMetrics;
  }
}

class Analytify_Google_Service_Dfareporting_ReportCriteria extends Analytify_Google_Collection
{
  protected $activitiesType = 'Analytify_Google_Service_Dfareporting_Activities';
  protected $activitiesDataType = '';
  protected $customRichMediaEventsType = 'Analytify_Google_Service_Dfareporting_CustomRichMediaEvents';
  protected $customRichMediaEventsDataType = '';
  protected $dateRangeType = 'Analytify_Google_Service_Dfareporting_DateRange';
  protected $dateRangeDataType = '';
  protected $dimensionFiltersType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $dimensionFiltersDataType = 'array';
  protected $dimensionsType = 'Analytify_Google_Service_Dfareporting_SortedDimension';
  protected $dimensionsDataType = 'array';
  public $metricNames;

  public function setActivities(Analytify_Google_Service_Dfareporting_Activities $activities)
  {
    $this->activities = $activities;
  }

  public function getActivities()
  {
    return $this->activities;
  }

  public function setCustomRichMediaEvents(Analytify_Google_Service_Dfareporting_CustomRichMediaEvents $customRichMediaEvents)
  {
    $this->customRichMediaEvents = $customRichMediaEvents;
  }

  public function getCustomRichMediaEvents()
  {
    return $this->customRichMediaEvents;
  }

  public function setDateRange(Analytify_Google_Service_Dfareporting_DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }

  public function getDateRange()
  {
    return $this->dateRange;
  }

  public function setDimensionFilters($dimensionFilters)
  {
    $this->dimensionFilters = $dimensionFilters;
  }

  public function getDimensionFilters()
  {
    return $this->dimensionFilters;
  }

  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }

  public function getDimensions()
  {
    return $this->dimensions;
  }

  public function setMetricNames($metricNames)
  {
    $this->metricNames = $metricNames;
  }

  public function getMetricNames()
  {
    return $this->metricNames;
  }
}

class Analytify_Google_Service_Dfareporting_ReportCrossDimensionReachCriteria extends Analytify_Google_Collection
{
  protected $breakdownType = 'Analytify_Google_Service_Dfareporting_SortedDimension';
  protected $breakdownDataType = 'array';
  protected $dateRangeType = 'Analytify_Google_Service_Dfareporting_DateRange';
  protected $dateRangeDataType = '';
  public $dimension;
  protected $dimensionFiltersType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $dimensionFiltersDataType = 'array';
  public $metricNames;
  public $overlapMetricNames;
  public $pivoted;

  public function setBreakdown($breakdown)
  {
    $this->breakdown = $breakdown;
  }

  public function getBreakdown()
  {
    return $this->breakdown;
  }

  public function setDateRange(Analytify_Google_Service_Dfareporting_DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }

  public function getDateRange()
  {
    return $this->dateRange;
  }

  public function setDimension($dimension)
  {
    $this->dimension = $dimension;
  }

  public function getDimension()
  {
    return $this->dimension;
  }

  public function setDimensionFilters($dimensionFilters)
  {
    $this->dimensionFilters = $dimensionFilters;
  }

  public function getDimensionFilters()
  {
    return $this->dimensionFilters;
  }

  public function setMetricNames($metricNames)
  {
    $this->metricNames = $metricNames;
  }

  public function getMetricNames()
  {
    return $this->metricNames;
  }

  public function setOverlapMetricNames($overlapMetricNames)
  {
    $this->overlapMetricNames = $overlapMetricNames;
  }

  public function getOverlapMetricNames()
  {
    return $this->overlapMetricNames;
  }

  public function setPivoted($pivoted)
  {
    $this->pivoted = $pivoted;
  }

  public function getPivoted()
  {
    return $this->pivoted;
  }
}

class Analytify_Google_Service_Dfareporting_ReportDelivery extends Analytify_Google_Collection
{
  public $emailOwner;
  public $emailOwnerDeliveryType;
  public $message;
  protected $recipientsType = 'Analytify_Google_Service_Dfareporting_Recipient';
  protected $recipientsDataType = 'array';

  public function setEmailOwner($emailOwner)
  {
    $this->emailOwner = $emailOwner;
  }

  public function getEmailOwner()
  {
    return $this->emailOwner;
  }

  public function setEmailOwnerDeliveryType($emailOwnerDeliveryType)
  {
    $this->emailOwnerDeliveryType = $emailOwnerDeliveryType;
  }

  public function getEmailOwnerDeliveryType()
  {
    return $this->emailOwnerDeliveryType;
  }

  public function setMessage($message)
  {
    $this->message = $message;
  }

  public function getMessage()
  {
    return $this->message;
  }

  public function setRecipients($recipients)
  {
    $this->recipients = $recipients;
  }

  public function getRecipients()
  {
    return $this->recipients;
  }
}

class Analytify_Google_Service_Dfareporting_ReportFloodlightCriteria extends Analytify_Google_Collection
{
  protected $customRichMediaEventsType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $customRichMediaEventsDataType = 'array';
  protected $dateRangeType = 'Analytify_Google_Service_Dfareporting_DateRange';
  protected $dateRangeDataType = '';
  protected $dimensionFiltersType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $dimensionFiltersDataType = 'array';
  protected $dimensionsType = 'Analytify_Google_Service_Dfareporting_SortedDimension';
  protected $dimensionsDataType = 'array';
  protected $floodlightConfigIdType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $floodlightConfigIdDataType = '';
  public $metricNames;
  protected $reportPropertiesType = 'Analytify_Google_Service_Dfareporting_ReportFloodlightCriteriaReportProperties';
  protected $reportPropertiesDataType = '';

  public function setCustomRichMediaEvents($customRichMediaEvents)
  {
    $this->customRichMediaEvents = $customRichMediaEvents;
  }

  public function getCustomRichMediaEvents()
  {
    return $this->customRichMediaEvents;
  }

  public function setDateRange(Analytify_Google_Service_Dfareporting_DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }

  public function getDateRange()
  {
    return $this->dateRange;
  }

  public function setDimensionFilters($dimensionFilters)
  {
    $this->dimensionFilters = $dimensionFilters;
  }

  public function getDimensionFilters()
  {
    return $this->dimensionFilters;
  }

  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }

  public function getDimensions()
  {
    return $this->dimensions;
  }

  public function setFloodlightConfigId(Analytify_Google_Service_Dfareporting_DimensionValue $floodlightConfigId)
  {
    $this->floodlightConfigId = $floodlightConfigId;
  }

  public function getFloodlightConfigId()
  {
    return $this->floodlightConfigId;
  }

  public function setMetricNames($metricNames)
  {
    $this->metricNames = $metricNames;
  }

  public function getMetricNames()
  {
    return $this->metricNames;
  }

  public function setReportProperties(Analytify_Google_Service_Dfareporting_ReportFloodlightCriteriaReportProperties $reportProperties)
  {
    $this->reportProperties = $reportProperties;
  }

  public function getReportProperties()
  {
    return $this->reportProperties;
  }
}

class Analytify_Google_Service_Dfareporting_ReportFloodlightCriteriaReportProperties extends Analytify_Google_Model
{
  public $includeAttributedIPConversions;
  public $includeUnattributedCookieConversions;
  public $includeUnattributedIPConversions;

  public function setIncludeAttributedIPConversions($includeAttributedIPConversions)
  {
    $this->includeAttributedIPConversions = $includeAttributedIPConversions;
  }

  public function getIncludeAttributedIPConversions()
  {
    return $this->includeAttributedIPConversions;
  }

  public function setIncludeUnattributedCookieConversions($includeUnattributedCookieConversions)
  {
    $this->includeUnattributedCookieConversions = $includeUnattributedCookieConversions;
  }

  public function getIncludeUnattributedCookieConversions()
  {
    return $this->includeUnattributedCookieConversions;
  }

  public function setIncludeUnattributedIPConversions($includeUnattributedIPConversions)
  {
    $this->includeUnattributedIPConversions = $includeUnattributedIPConversions;
  }

  public function getIncludeUnattributedIPConversions()
  {
    return $this->includeUnattributedIPConversions;
  }
}

class Analytify_Google_Service_Dfareporting_ReportList extends Analytify_Google_Collection
{
  public $etag;
  protected $itemsType = 'Analytify_Google_Service_Dfareporting_Report';
  protected $itemsDataType = 'array';
  public $kind;
  public $nextPageToken;

  public function setEtag($etag)
  {
    $this->etag = $etag;
  }

  public function getEtag()
  {
    return $this->etag;
  }

  public function setItems($items)
  {
    $this->items = $items;
  }

  public function getItems()
  {
    return $this->items;
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

class Analytify_Google_Service_Dfareporting_ReportPathToConversionCriteria extends Analytify_Google_Collection
{
  protected $activityFiltersType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $activityFiltersDataType = 'array';
  protected $conversionDimensionsType = 'Analytify_Google_Service_Dfareporting_SortedDimension';
  protected $conversionDimensionsDataType = 'array';
  protected $customFloodlightVariablesType = 'Analytify_Google_Service_Dfareporting_SortedDimension';
  protected $customFloodlightVariablesDataType = 'array';
  protected $customRichMediaEventsType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $customRichMediaEventsDataType = 'array';
  protected $dateRangeType = 'Analytify_Google_Service_Dfareporting_DateRange';
  protected $dateRangeDataType = '';
  protected $floodlightConfigIdType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $floodlightConfigIdDataType = '';
  public $metricNames;
  protected $perInteractionDimensionsType = 'Analytify_Google_Service_Dfareporting_SortedDimension';
  protected $perInteractionDimensionsDataType = 'array';
  protected $reportPropertiesType = 'Analytify_Google_Service_Dfareporting_ReportPathToConversionCriteriaReportProperties';
  protected $reportPropertiesDataType = '';

  public function setActivityFilters($activityFilters)
  {
    $this->activityFilters = $activityFilters;
  }

  public function getActivityFilters()
  {
    return $this->activityFilters;
  }

  public function setConversionDimensions($conversionDimensions)
  {
    $this->conversionDimensions = $conversionDimensions;
  }

  public function getConversionDimensions()
  {
    return $this->conversionDimensions;
  }

  public function setCustomFloodlightVariables($customFloodlightVariables)
  {
    $this->customFloodlightVariables = $customFloodlightVariables;
  }

  public function getCustomFloodlightVariables()
  {
    return $this->customFloodlightVariables;
  }

  public function setCustomRichMediaEvents($customRichMediaEvents)
  {
    $this->customRichMediaEvents = $customRichMediaEvents;
  }

  public function getCustomRichMediaEvents()
  {
    return $this->customRichMediaEvents;
  }

  public function setDateRange(Analytify_Google_Service_Dfareporting_DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }

  public function getDateRange()
  {
    return $this->dateRange;
  }

  public function setFloodlightConfigId(Analytify_Google_Service_Dfareporting_DimensionValue $floodlightConfigId)
  {
    $this->floodlightConfigId = $floodlightConfigId;
  }

  public function getFloodlightConfigId()
  {
    return $this->floodlightConfigId;
  }

  public function setMetricNames($metricNames)
  {
    $this->metricNames = $metricNames;
  }

  public function getMetricNames()
  {
    return $this->metricNames;
  }

  public function setPerInteractionDimensions($perInteractionDimensions)
  {
    $this->perInteractionDimensions = $perInteractionDimensions;
  }

  public function getPerInteractionDimensions()
  {
    return $this->perInteractionDimensions;
  }

  public function setReportProperties(Analytify_Google_Service_Dfareporting_ReportPathToConversionCriteriaReportProperties $reportProperties)
  {
    $this->reportProperties = $reportProperties;
  }

  public function getReportProperties()
  {
    return $this->reportProperties;
  }
}

class Analytify_Google_Service_Dfareporting_ReportPathToConversionCriteriaReportProperties extends Analytify_Google_Model
{
  public $clicksLookbackWindow;
  public $impressionsLookbackWindow;
  public $includeAttributedIPConversions;
  public $includeUnattributedCookieConversions;
  public $includeUnattributedIPConversions;
  public $maximumClickInteractions;
  public $maximumImpressionInteractions;
  public $maximumInteractionGap;
  public $pivotOnInteractionPath;

  public function setClicksLookbackWindow($clicksLookbackWindow)
  {
    $this->clicksLookbackWindow = $clicksLookbackWindow;
  }

  public function getClicksLookbackWindow()
  {
    return $this->clicksLookbackWindow;
  }

  public function setImpressionsLookbackWindow($impressionsLookbackWindow)
  {
    $this->impressionsLookbackWindow = $impressionsLookbackWindow;
  }

  public function getImpressionsLookbackWindow()
  {
    return $this->impressionsLookbackWindow;
  }

  public function setIncludeAttributedIPConversions($includeAttributedIPConversions)
  {
    $this->includeAttributedIPConversions = $includeAttributedIPConversions;
  }

  public function getIncludeAttributedIPConversions()
  {
    return $this->includeAttributedIPConversions;
  }

  public function setIncludeUnattributedCookieConversions($includeUnattributedCookieConversions)
  {
    $this->includeUnattributedCookieConversions = $includeUnattributedCookieConversions;
  }

  public function getIncludeUnattributedCookieConversions()
  {
    return $this->includeUnattributedCookieConversions;
  }

  public function setIncludeUnattributedIPConversions($includeUnattributedIPConversions)
  {
    $this->includeUnattributedIPConversions = $includeUnattributedIPConversions;
  }

  public function getIncludeUnattributedIPConversions()
  {
    return $this->includeUnattributedIPConversions;
  }

  public function setMaximumClickInteractions($maximumClickInteractions)
  {
    $this->maximumClickInteractions = $maximumClickInteractions;
  }

  public function getMaximumClickInteractions()
  {
    return $this->maximumClickInteractions;
  }

  public function setMaximumImpressionInteractions($maximumImpressionInteractions)
  {
    $this->maximumImpressionInteractions = $maximumImpressionInteractions;
  }

  public function getMaximumImpressionInteractions()
  {
    return $this->maximumImpressionInteractions;
  }

  public function setMaximumInteractionGap($maximumInteractionGap)
  {
    $this->maximumInteractionGap = $maximumInteractionGap;
  }

  public function getMaximumInteractionGap()
  {
    return $this->maximumInteractionGap;
  }

  public function setPivotOnInteractionPath($pivotOnInteractionPath)
  {
    $this->pivotOnInteractionPath = $pivotOnInteractionPath;
  }

  public function getPivotOnInteractionPath()
  {
    return $this->pivotOnInteractionPath;
  }
}

class Analytify_Google_Service_Dfareporting_ReportReachCriteria extends Analytify_Google_Collection
{
  protected $activitiesType = 'Analytify_Google_Service_Dfareporting_Activities';
  protected $activitiesDataType = '';
  protected $customRichMediaEventsType = 'Analytify_Google_Service_Dfareporting_CustomRichMediaEvents';
  protected $customRichMediaEventsDataType = '';
  protected $dateRangeType = 'Analytify_Google_Service_Dfareporting_DateRange';
  protected $dateRangeDataType = '';
  protected $dimensionFiltersType = 'Analytify_Google_Service_Dfareporting_DimensionValue';
  protected $dimensionFiltersDataType = 'array';
  protected $dimensionsType = 'Analytify_Google_Service_Dfareporting_SortedDimension';
  protected $dimensionsDataType = 'array';
  public $metricNames;
  public $reachByFrequencyMetricNames;

  public function setActivities(Analytify_Google_Service_Dfareporting_Activities $activities)
  {
    $this->activities = $activities;
  }

  public function getActivities()
  {
    return $this->activities;
  }

  public function setCustomRichMediaEvents(Analytify_Google_Service_Dfareporting_CustomRichMediaEvents $customRichMediaEvents)
  {
    $this->customRichMediaEvents = $customRichMediaEvents;
  }

  public function getCustomRichMediaEvents()
  {
    return $this->customRichMediaEvents;
  }

  public function setDateRange(Analytify_Google_Service_Dfareporting_DateRange $dateRange)
  {
    $this->dateRange = $dateRange;
  }

  public function getDateRange()
  {
    return $this->dateRange;
  }

  public function setDimensionFilters($dimensionFilters)
  {
    $this->dimensionFilters = $dimensionFilters;
  }

  public function getDimensionFilters()
  {
    return $this->dimensionFilters;
  }

  public function setDimensions($dimensions)
  {
    $this->dimensions = $dimensions;
  }

  public function getDimensions()
  {
    return $this->dimensions;
  }

  public function setMetricNames($metricNames)
  {
    $this->metricNames = $metricNames;
  }

  public function getMetricNames()
  {
    return $this->metricNames;
  }

  public function setReachByFrequencyMetricNames($reachByFrequencyMetricNames)
  {
    $this->reachByFrequencyMetricNames = $reachByFrequencyMetricNames;
  }

  public function getReachByFrequencyMetricNames()
  {
    return $this->reachByFrequencyMetricNames;
  }
}

class Analytify_Google_Service_Dfareporting_ReportSchedule extends Analytify_Google_Collection
{
  public $active;
  public $every;
  public $expirationDate;
  public $repeats;
  public $repeatsOnWeekDays;
  public $runsOnDayOfMonth;
  public $startDate;

  public function setActive($active)
  {
    $this->active = $active;
  }

  public function getActive()
  {
    return $this->active;
  }

  public function setEvery($every)
  {
    $this->every = $every;
  }

  public function getEvery()
  {
    return $this->every;
  }

  public function setExpirationDate($expirationDate)
  {
    $this->expirationDate = $expirationDate;
  }

  public function getExpirationDate()
  {
    return $this->expirationDate;
  }

  public function setRepeats($repeats)
  {
    $this->repeats = $repeats;
  }

  public function getRepeats()
  {
    return $this->repeats;
  }

  public function setRepeatsOnWeekDays($repeatsOnWeekDays)
  {
    $this->repeatsOnWeekDays = $repeatsOnWeekDays;
  }

  public function getRepeatsOnWeekDays()
  {
    return $this->repeatsOnWeekDays;
  }

  public function setRunsOnDayOfMonth($runsOnDayOfMonth)
  {
    $this->runsOnDayOfMonth = $runsOnDayOfMonth;
  }

  public function getRunsOnDayOfMonth()
  {
    return $this->runsOnDayOfMonth;
  }

  public function setStartDate($startDate)
  {
    $this->startDate = $startDate;
  }

  public function getStartDate()
  {
    return $this->startDate;
  }
}

class Analytify_Google_Service_Dfareporting_SortedDimension extends Analytify_Google_Model
{
  public $kind;
  public $name;
  public $sortOrder;

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

  public function setSortOrder($sortOrder)
  {
    $this->sortOrder = $sortOrder;
  }

  public function getSortOrder()
  {
    return $this->sortOrder;
  }
}

class Analytify_Google_Service_Dfareporting_UserProfile extends Analytify_Google_Model
{
  public $accountId;
  public $accountName;
  public $etag;
  public $kind;
  public $profileId;
  public $subAccountId;
  public $subAccountName;
  public $userName;

  public function setAccountId($accountId)
  {
    $this->accountId = $accountId;
  }

  public function getAccountId()
  {
    return $this->accountId;
  }

  public function setAccountName($accountName)
  {
    $this->accountName = $accountName;
  }

  public function getAccountName()
  {
    return $this->accountName;
  }

  public function setEtag($etag)
  {
    $this->etag = $etag;
  }

  public function getEtag()
  {
    return $this->etag;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }

  public function setProfileId($profileId)
  {
    $this->profileId = $profileId;
  }

  public function getProfileId()
  {
    return $this->profileId;
  }

  public function setSubAccountId($subAccountId)
  {
    $this->subAccountId = $subAccountId;
  }

  public function getSubAccountId()
  {
    return $this->subAccountId;
  }

  public function setSubAccountName($subAccountName)
  {
    $this->subAccountName = $subAccountName;
  }

  public function getSubAccountName()
  {
    return $this->subAccountName;
  }

  public function setUserName($userName)
  {
    $this->userName = $userName;
  }

  public function getUserName()
  {
    return $this->userName;
  }
}

class Analytify_Google_Service_Dfareporting_UserProfileList extends Analytify_Google_Collection
{
  public $etag;
  protected $itemsType = 'Analytify_Google_Service_Dfareporting_UserProfile';
  protected $itemsDataType = 'array';
  public $kind;

  public function setEtag($etag)
  {
    $this->etag = $etag;
  }

  public function getEtag()
  {
    return $this->etag;
  }

  public function setItems($items)
  {
    $this->items = $items;
  }

  public function getItems()
  {
    return $this->items;
  }

  public function setKind($kind)
  {
    $this->kind = $kind;
  }

  public function getKind()
  {
    return $this->kind;
  }
}
