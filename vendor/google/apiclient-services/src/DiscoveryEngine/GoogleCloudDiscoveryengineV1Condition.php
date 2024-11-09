<?php
/*
 * Copyright 2014 Google Inc.
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

namespace Google\Service\DiscoveryEngine;

class GoogleCloudDiscoveryengineV1Condition extends \Google\Collection
{
  protected $collection_key = 'queryTerms';
  protected $activeTimeRangeType = GoogleCloudDiscoveryengineV1ConditionTimeRange::class;
  protected $activeTimeRangeDataType = 'array';
  /**
   * @var string
   */
  public $queryRegex;
  protected $queryTermsType = GoogleCloudDiscoveryengineV1ConditionQueryTerm::class;
  protected $queryTermsDataType = 'array';

  /**
   * @param GoogleCloudDiscoveryengineV1ConditionTimeRange[]
   */
  public function setActiveTimeRange($activeTimeRange)
  {
    $this->activeTimeRange = $activeTimeRange;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ConditionTimeRange[]
   */
  public function getActiveTimeRange()
  {
    return $this->activeTimeRange;
  }
  /**
   * @param string
   */
  public function setQueryRegex($queryRegex)
  {
    $this->queryRegex = $queryRegex;
  }
  /**
   * @return string
   */
  public function getQueryRegex()
  {
    return $this->queryRegex;
  }
  /**
   * @param GoogleCloudDiscoveryengineV1ConditionQueryTerm[]
   */
  public function setQueryTerms($queryTerms)
  {
    $this->queryTerms = $queryTerms;
  }
  /**
   * @return GoogleCloudDiscoveryengineV1ConditionQueryTerm[]
   */
  public function getQueryTerms()
  {
    return $this->queryTerms;
  }
}

// Adding a class alias for backwards compatibility with the previous class name.
class_alias(GoogleCloudDiscoveryengineV1Condition::class, 'Google_Service_DiscoveryEngine_GoogleCloudDiscoveryengineV1Condition');
