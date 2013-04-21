/**
 * CLASS
 * 		BasicStorageProvider
 * DESCRIPTION
 * 		Provides bare bones disk storage functionality. Not to be used for a
 * 		significant ammount of data, nor for content sensitive information.
 * USAGE
 * 		var basicStorageProvider = new BasicStorageProvider();
 * 		var value = basicStorageProvider.getItem('John Doe');
 * @class
 * @public
 */
function BasicStorageProvider() {
	
	/**
	 * Possibly returns the item stored under the given key, should there be 
	 * one.
	 * @method
	 * @public
	 * @param key { String }
	 * 		The key of the item to retrieve.
	 * @return { Object }
	 * 		The item, or null if it cannot be found.
	 * 
	 */
	this.getItem = function (key) {
		return cache.lookUp(key);
	}
	 
	/**
	 * Sets or overwrites an item using the given key.
	 * @method
	 * @public
	 * @param key { String }
	 * 		The key of the item to set/overwrite.
	 * @param value { Object }
	 * 		The new value to set/overwrite.
	 */
	this.setItem = function (key, value) {
		cache.add(key, value);
	}
	  
	/**
	 * Effectivelly deletes the item stored under the given key.
	 * @method
	 * @public
	 * @param key { String }
	 * 		The key of the item to delete.
	 */
	this.unsetItem = function (key) {
		cache.remove(key);
	}
	
	/**
	 * The Cache instance this class uses. 
	 */
	var cache;
	
	/**
	 * Custom initialization for class BasicStorageProvider.
	 * @method
	 * @private
	 */
	function init() {
		var app = air.NativeApplication.nativeApplication;
		
		// @instantiate the cache
		var appID = app.applicationID;
		var uid = appID.replace(/[\W_\.]/g, '-'); 
		cache = new Cache(uid);
		
		// @subscribe to 'exiting' event in order to flush cache to disk
		var exitHandler = function() {
			app.removeEventListener(air.Event.EXITING,
				exitHandler);
			cache.flush();
		}
		app.addEventListener(air.Event.EXITING, exitHandler);
	}

	/**
	 * CLASS
	 * 		Cache
	 * DESCRIPTION
	 * 		Provides dedicated, basic caching functionality.
	 * USAGE
	 * 		N/A (Internal use only)
	 * @class
	 * @private
	 * @param uid { String }
	 * 		The unique id of this cache.

	 */
	function Cache (uid) {
		
		/**
		 * Looks up and possibly returns the object cached under the given key.
		 * @method
		 * @public
		 * @param key { String }
		 * 		A key to look up in the cache.
		 * @return { Object }
		 * 		The object cached under the given key, or null if there is no 
		 * 		such key.
		 */
		this.lookUp = function(key) {
			return (key in _cache)? _cache[key] : null; 
		}

		/**
		 * Caches the given value under the given key.
		 * @method
		 * @public
		 * @param key { String }
		 * 		A key to identify the cached value. Keys are uniques, so that 
		 * 		existing values are overridden.
		 * @param value { String }
		 * 		The value to cache.
		 */
		this.add = function (key, value) {
			_cache[key] = value;
		}

		/**
		 * Clears a previously cached value
		 * @method
		 * @public
		 * @param key { String }
		 * 		A key to identify the cached value that is to be cleared.
		 */
		this.remove = function (key) {
			_cache[key] = null;
			delete _cache[key];
		}

		/**
		 * Flushes cached values to the permanent storage media and empties the
		 * cache.
		 * @method
		 * @public
		 */
		this.flush = function () {
			var content = jsonSerializer.serialize(_cache);
			if(content) {
				var fileName = makeFileName();
				var success = basicDiskHandler.writeFile(fileName, content);
				if(success) {
					_cache = {};
				};
			} else {
				air.trace("Error: could not save cache to disk");
			}
		}

		/**
		 * Empties the cache, discarding all existing entries.
		 * @method
		 * @public
		 */
		this.clear = function () {
			_cache = {};
		}

		/**
		 * The unique id of this cache.
		 * @field
		 * @private
		 */
		var cacheUID = uid;

		/**
		 * The internal representation of the cache.
		 * @field
		 * @private
		 */
		var _cache = {};

		/**
		 * The JSONSerializer instance this class uses.
		 * @field
		 * @private 
		 */
		var jsonSerializer = new JSONSerializer();

		/**
		 * The BasicDiskHandler instance this class uses.
		 * @field
		 * @private 
		 */
		var basicDiskHandler = new BasicDiskHandler();

		/**
		 * Custom initialization for class Cache.
		 * @method
		 * @private
		 */
		function init() {
			var fileName = makeFileName();
			
			// @check for and load content previously flushed on disk.
			var fileContent = basicDiskHandler.readFile(fileName);
			if(fileContent) {
				var content = jsonSerializer.deserialize(fileContent);
				if(content) {_cache = content};
			}
		}

		/**
		 * Provides a file name.
		 * @method
		 * @private
		 * @return { String }
		 * 		The file name to use.
		 */
		function makeFileName() {
			return cacheUID+ '.cache';
		}



		/**
		 * CLASS
		 * 		JSONSerializer
		 * DESCRIPTION
		 * 		Provides dedicated, basic two-way JSON serialization 
		 * 		functionality. 
		 * 		Note:
		 * 		Particularily, this serializer will only accept strings, 
		 * 		numbers, arrays and booleans as end-point values.
		 * USAGE
		 * 		N/A (Internal use only)
		 * @class
		 * @private
		 */
		function JSONSerializer() {
			
			/**
			 * Creates a string reprezentation of the given object.
			 * @method
			 * @public
			 * @param object { Object }
			 * 		An object to serialize. See above 'note'.
			 * @return { String }
			 * 		A string representing the given object as JSON, or null if 
			 * 		an error occured while serializing.
			 */
			this.serialize = function(object) {
				var ret = null;
				if(object) {
					try {
						ret = strignify(object);
					} catch(e) {
						air.trace(e.message);
					}
				}
				return ret;
			}
			
			/**
			 * Creates an object reprezentation of the given string.
			 * @method
			 * @public
			 * @param string { String }
			 * 		A string to deserialize. See above 'note'.
			 * @return { Object }
			 * 		An object, provided the given string is valid for safe 
			 * 		evaluation; null otherwise.
			 */
			this.deserialize = function(string) {
				return string? safeEval(string) : null;
			}
			
			/**
			 * Actually performs de-serialization.
			 * Note:
			 * Will fail silently if the string to be deserialized describes
			 * an object with non-final properties -- i.e., '{value: 1+1}' will 
			 * fail.
			 * @method
			 * @private
			 * @param string { String }
			 * 		The string to be parsed into an object.
			 * @return { Object }
			 * 		The parsed object, or null upon failure.
			 */
			function safeEval (string) {
				var ret = null;
				try {
					ret = eval('(' + string + ')');
				}
				catch(e) {air.trace(e.message)};
				return ret;
			}
			
			/**
			 * Actually performs the serialization.
			 * Note:
			 * Will possibly throw security exceptions if function calls pass
			 * the type filter. Use a try/catch block to wrap the call to 
			 * strignify().
			 * @method
			 * @private
			 * @param obj { Object }
			 * 		The object to strignify. Function types will be silently 
			 * 		set to undefined.
			 * @return { String }
			 * 		A JSON version of the given object, preserving all but 
			 * 		function type members.
			 */
			function strignify(obj) {
				var ret;
				switch (typeof obj) {
					case 'string':
						ret = '"' +obj.replace(/\"/g, '\\"')+ '"';
						break;
					case 'number':
						ret = obj.toString();
						break;
					case 'boolean':
						ret = obj? 'true': 'false';
						break;
					case 'object':
						if(obj === null) {ret = "null"}
						else if(obj.length && obj.join) {
							ret = "[" + obj.join(',') + "]";
						} else {
							ret = "{";
							for(var key in obj) {
								ret += key + ":" + strignify(obj[key]) + ",";
							}
							ret += "}";
						}
						break;
					case 'function':
					case 'undefined':
					default:
						ret = "undefined";
						break;
				}
				return ret;
			}
		}



		/**
		 * CLASS
		 * 		BasicDiskHandler
		 * DESCRIPTION
		 * 		Provides basic CRUD file operations.
		 * 		Note:
		 * 		Created files allways live under [documents directory]/bdh/.
		 * 		Note:
		 * 		It is NOT safe to use this class for writing context-sensitive
		 * 		informations to disk (such as passwords).
		 * USAGE
		 * 		N/A (Internal use only)
		 * @class
		 * @private
		 */
		function BasicDiskHandler() {

			/**
			 * Reads the file with the given name, provided that such a file 
			 * exists.
			 * @method
			 * @public
			 * @param fileName { String }
			 * 		The name of the file to read.
			 * @return { String }
			 * 		The content of the given file, or null if no content can be 
			 * 		retrieved.
			 */
			this.readFile = function(fileName) {
				var root = air.File.documentsDirectory;
				var fs = null;
				var fileCnt = null;
				try {
					var file = root.resolvePath("bdh/" +fileName);
					if (file && file.exists) {
						fs = new air.FileStream();
						fs.open(file, 'read');
						fileCnt = fs.readUTFBytes(fs.bytesAvailable);
						fs.close();
					}
				} catch(e) {
					air.trace(e.message);
					if(fs != null) {fs.close()};
				}
				return fileCnt;				
			}

			/**
			 * Trims the given file to 0 length, then writes the given content 
			 * inside it.
			 * @method
			 * @public
			 * @param fileName { String }
			 * 		The name of the file to write content in.
			 * @param content { String }
			 * 		The new content that is to be written.
			 * @return { Boolean }
			 * 		True if writing to the file succeeded, false otherwise.
			 */
			this.writeFile = function(fileName, content) {
				var root = air.File.documentsDirectory;
				var fs = null;
				var success = false;
				try {
					var file = root.resolvePath("bdh/" +fileName);
					fs = new air.FileStream();
					fs.open(file, 'write');
					fs.writeUTFBytes(content);
					fs.close();
					success = true;
				} catch (e) {
					air.trace(e.message);
					if(fs != null) {fs.close()};
				}
				return success;
			}

			/**
			 * Erases the file with the given name, provided that it exists.
			 * @method
			 * @public
			 * @param fileName { String }
			 * 		The name of the file to be erased.
			 * @return { Boolean } 
			 * 		True if the given file has been successfully deleted, false
			 * 		otherwise.
			 */
			this.destroyFile = function(fileName) {
				var root = air.File.documentsDirectory;
				var success = false;
				try {
					var file = root.resolvePath("bdh/" +fileName);
					file.deleteFile();
					success = true;
				} catch(e) {
					air.trace(e.message);
				}
				return success;
			}
		}
		
		// @run initialization code for class Cache
		init();
	}
	
	// @run initialization code for class BasicStorageProvider
	init();
}

