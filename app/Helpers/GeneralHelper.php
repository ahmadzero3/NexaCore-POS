<?php

use Illuminate\Database\Eloquent\Relations\Relation;

if (!function_exists('getMorphedModelName')) {
    /**
     * Get the morphed model name for a given class.
     *
     * @param string $class
     * @return string
     */
    function getMorphedModelName(string $class): string
    {
        return array_search($class, Relation::morphMap()) ?: class_basename($class);
    }
}

if (!function_exists('getAppVersion')) {
    /**
     * Get the current application version.
     *
     * @return string
     */
    function getAppVersion()
    {
        return env('APP_VERSION', '1.0.0'); // Default version if not set in .env
    }
}

if (!function_exists('getDatabaseMigrationAppVersion')) {
    /**
     * Get the current application version.
     *
     * @return string
     */
    function getDatabaseMigrationAppVersion()
    {
        // Get the application version from the database
        // get the last record of the version table
        $appVersion = \DB::table('versions')
            ->select('version')
            ->orderBy('id', 'desc')
            ->first();
            return $appVersion->version;

        return env('APP_VERSION', '1.0.0'); // Default version if not set in .env
    }
}

if (!function_exists('versionedAsset')) {
    /**
     * Get the current application version.
     *
     * @return string
     */
    function versionedAsset($link)
    {
        return global_asset($link).'?v='.getAppVersion(); // Default version if not set in .env
    }
}
