<?php

use zcrmsdk\crm\crud\ZCRMRecord;
use zcrmsdk\crm\setup\restclient\ZCRMRestClient;
use zcrmsdk\crm\exception\ZCRMException;

class API
{

    public function searchRecordsByCriteria($module_name, $criteria)
    {
        $moduleIns = ZCRMRestClient::getInstance()->getModuleInstance($module_name);
        $records = null;
        try {
            $response = $moduleIns->searchRecordsByCriteria($criteria);
            $records = $response->getData();
        } catch (ZCRMException $ex) {
            echo $ex->getMessage();
            echo "<br/>";
            echo $ex->getExceptionCode();
            echo "<br/>";
            echo $ex->getFile();
            echo "<br/>";
        }
        return $records;
    }

    public function createRecord($module_name, Array $record_model)
    {
        $moduleIns = ZCRMRestClient::getInstance()->getModuleInstance($module_name);
        $records = array();
        $record = ZCRMRecord::getInstance($module_name, null);
        foreach ($record_model as $propertie => $propertie_value) {
            if ($propertie_value != null) {
                $record->setFieldValue($propertie, $propertie_value);
            }
        }
        array_push($records, $record);
        $responseIn = $moduleIns->createRecords($records);
        foreach ($responseIn->getEntityResponses() as $responseIns) {
            echo "HTTP Status Code:" . $responseIn->getHttpStatusCode();
            echo "<br/>";
            echo "Status:" . $responseIns->getStatus();
            echo "<br/>";
            echo "Message:" . $responseIns->getMessage();
            echo "<br/>";
            echo "Code:" . $responseIns->getCode();
            echo "<br/>";
            echo "Details:" . json_encode($responseIns->getDetails());
            echo "<br/>";
            $Details = json_decode(json_encode($responseIns->getDetails()), true);
            $result_id = $Details['id'];
        }
        return $result_id;
    }

    public function getRecord($module_name, $record_id)
    {
        $moduleIns = ZCRMRestClient::getInstance()->getModuleInstance($module_name);
        $record = null;
        try {
            $response = $moduleIns->getRecord($record_id);
            $record = $response->getData();
        } catch (ZCRMException $ex) {
            echo $ex->getMessage();
            echo "<br/>";
            echo $ex->getExceptionCode();
            echo "<br/>";
            echo $ex->getFile();
            echo "<br/>";
        }
        return $record;
    }

    public function updateRecord($module_name, Array $record_model, $record_id)
    {
        $record = ZCRMRestClient::getInstance()->getRecordInstance($module_name, $record_id);
        foreach ($record_model as $propertie => $propertie_value) {
            if ($propertie_value != null) {
                $record->setFieldValue($propertie, $propertie_value);
            }
        }
        $responseIns = $record->update();
        echo "HTTP Status Code:" . $responseIns->getHttpStatusCode();
        echo "<br/>";
        echo "Status:" . $responseIns->getStatus();
        echo "<br/>";
        echo "Message:" . $responseIns->getMessage();
        echo "<br/>";
        echo "Code:" . $responseIns->getCode();
        echo "<br/>";
        echo "Details:" . json_encode($responseIns->getDetails());
    }
}
