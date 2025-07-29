<?php

if (!function_exists('getOnlySelectedUnits')) {
    function getOnlySelectedUnits($collection, $baseUnitId, $secondaryUnitId){
        if($baseUnitId != $secondaryUnitId){
            $collection = $collection->whereIn('id', [$baseUnitId, $secondaryUnitId]);
        }
        return $collection;
     }
}
