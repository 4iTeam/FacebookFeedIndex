<?php
/**
 * Object Cache API
 *
 */

/**
 * Adds data to the cache, if the cache key doesn't already exist.
 *
 * @since 2.0.0
 *
 * @see QC_Object_Cache::add()
 *
 * @param int|string $key    The cache key to use for retrieval later.
 * @param mixed      $data   The data to add to the cache.
 * @param string     $group  Optional. The group to add the cache to. Enables the same key
 *                           to be used across groups. Default empty.
 * @param int        $expire Optional. When the cache data should expire, in seconds.
 *                           Default 0 (no expiration).
 * @return bool False if cache key and group already exist, true on success.
 */
function qc_cache_add( $key, $data, $group = '', $expire = 0 ) {
	return qc_cache_object()->add( $key, $data, $group, (int) $expire );
}



/**
 * Decrements numeric cache item's value.
 *
 * @since 3.3.0
 *
 * @see QC_Object_Cache::decr()
 *
 * @param int|string $key    The cache key to decrement.
 * @param int        $offset Optional. The amount by which to decrement the item's value. Default 1.
 * @param string     $group  Optional. The group the key is in. Default empty.
 * @return false|int False on failure, the item's new value on success.
 */
function qc_cache_decr( $key, $offset = 1, $group = '' ) {
	return qc_cache_object()->decr( $key, $offset, $group );
}

/**
 * Removes the cache contents matching key and group.
 *
 * @since 2.0.0
 *
 * @see QC_Object_Cache::delete()
 *
 * @param int|string $key   What the contents in the cache are called.
 * @param string     $group Optional. Where the cache contents are grouped. Default empty.
 * @return bool True on successful removal, false on failure.
 */
function qc_cache_delete( $key, $group = '' ) {
	return qc_cache_object()->delete($key, $group);
}

/**
 * Removes all cache items.
 *
 * @since 2.0.0
 *
 * @see QC_Object_Cache::flush()
 *
 * @return bool False on failure, true on success
 */
function qc_cache_flush() {
	return qc_cache_object()->flush();
}

/**
 * Retrieves the cache contents from the cache by key and group.
 *
 * @since 2.0.0
 *
 * @see QC_Object_Cache::get()
 *
 * @param int|string  $key    The key under which the cache contents are stored.
 * @param string      $group  Optional. Where the cache contents are grouped. Default empty.
 * @param bool        $force  Optional. Whether to force an update of the local cache from the persistent
 *                            cache. Default false.
 * @param bool        $found  Optional. Whether the key was found in the cache. Disambiguates a return of false,
 *                            a storable value. Passed by reference. Default null.
 * @return bool|mixed False on failure to retrieve contents or the cache
 *		              contents on success
 */
function qc_cache_get( $key, $group = '', $force = false, &$found = null ) {
	return qc_cache_object()->get( $key, $group, $force, $found );
}

/**
 * Increment numeric cache item's value
 *
 * @since 3.3.0
 *
 * @see QC_Object_Cache::incr()
 *
 * @param int|string $key    The key for the cache contents that should be incremented.
 * @param int        $offset Optional. The amount by which to increment the item's value. Default 1.
 * @param string     $group  Optional. The group the key is in. Default empty.
 * @return false|int False on failure, the item's new value on success.
 */
function qc_cache_incr( $key, $offset = 1, $group = '' ) {
	return qc_cache_object()->incr( $key, $offset, $group );
}

/**
 * Replaces the contents of the cache with new data.
 *
 * @since 2.0.0
 *
 * @see QC_Object_Cache::replace()
 *
 * @param int|string $key    The key for the cache data that should be replaced.
 * @param mixed      $data   The new data to store in the cache.
 * @param string     $group  Optional. The group for the cache data that should be replaced.
 *                           Default empty.
 * @param int        $expire Optional. When to expire the cache contents, in seconds.
 *                           Default 0 (no expiration).
 * @return bool False if original value does not exist, true if contents were replaced
 */
function qc_cache_replace( $key, $data, $group = '', $expire = 0 ) {
	return qc_cache_object()->replace( $key, $data, $group, (int) $expire );
}

/**
 * Saves the data to the cache.
 *
 * Differs from wp_cache_add() and wp_cache_replace() in that it will always write data.
 *
 * @since 2.0.0
 *
 * @see QC_Object_Cache::set()
 *
 * @param int|string $key    The cache key to use for retrieval later.
 * @param mixed      $data   The contents to store in the cache.
 * @param string     $group  Optional. Where to group the cache contents. Enables the same key
 *                           to be used across groups. Default empty.
 * @param int        $expire Optional. When to expire the cache contents, in seconds.
 *                           Default 0 (no expiration).
 * @return bool False on failure, true on success
 */
function qc_cache_set( $key, $data, $group = '', $expire = 0 ) {
	return qc_cache_object()->set( $key, $data, $group, (int) $expire );
}

/**
 * @return \App\Services\Cache\ObjectCache;
 */
function qc_cache_object(){
	return app(\App\Services\Cache\ObjectCache::class);
}