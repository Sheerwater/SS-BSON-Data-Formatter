<?php

class BSONDataFormatter extends JSONDataFormatter
{
    private function checkForBson()
    {
        if (!function_exists('bson_encode') or !function_exists('bson_decode')) {
            throw new Exception('Please install a library such as MongoDB to enable PHP BSON functions.');
        }
    }

    protected $outputContentType = 'application/bson';

    public function supportedExtensions()
    {
        return [
            'bson'
        ];
    }

    public function supportedMimeTypes()
    {
        return [
            'application/bson'
        ];
    }

    /**
     * Generate a BSON representation of the given {@link DataObject}.
     *
     * @param \DataObject|\DataObjectInterface $obj       The object
     * @param string[]                         $fields    If supplied, only fields in the list will be returned
     * @param mixed                            $relations Not used
     *
     * @return string BSON
     */
    public function convertDataObject(DataObjectInterface $obj, $fields = null, $relations = null)
    {
        $this->checkForBson();

        /** @noinspection PhpUndefinedFunctionInspection */

        return bson_encode($this->convertDataObjectToJSONObject($obj, $fields, $relations));
    }

    /**
     * Generate a BSON representation of the given {@link SS_List}.
     *
     * @param SS_List $set
     * @param null    $fields
     *
     * @return string BSON
     */
    public function convertDataObjectSet(SS_List $set, $fields = null)
    {
        $items = [];
        foreach ($set as $do) {
            /** @var DataObject $do */
            if (!$do->canView()) continue;
            $items[] = $this->convertDataObjectToJSONObject($do, $fields);
        }

        $serobj = ArrayData::array_to_object([
            "totalSize" => (is_numeric($this->totalSize)) ? $this->totalSize : null,
            "items"     => $items
        ]);

        /** @noinspection PhpUndefinedFunctionInspection */

        return bson_enqcode($serobj);
    }

    public function convertStringToArray($strData)
    {
        /** @noinspection PhpUndefinedFunctionInspection */
        return bson_decode($strData);
    }
}
