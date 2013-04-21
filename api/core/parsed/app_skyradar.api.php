<?php
$application = array (
  'fusebox' => 
  array (
    'isFullyLoaded' => true,
    'circuits' => 
    array (
      'lib' => 
      array (
        'path' => '../apps/libr/',
        'parent' => '',
        'rootpath' => '../../site/',
        'xml' => 
        array (
          'xmlChildren' => 
          array (
            0 => 
            array (
              'xmlName' => 'circuit',
              'xmlAttributes' => 
              array (
                'access' => 'internal',
              ),
              'xmlValue' => '',
            ),
          ),
        ),
        'timestamp' => 1366518291,
        'circuitTrace' => 
        array (
          0 => 'lib',
        ),
        'access' => 'internal',
        'permissions' => '',
        'fuseactions' => 
        array (
          'function' => 
          array (
            'xml' => 
            array (
              'xmlName' => 'fuseaction',
              'xmlAttributes' => 
              array (
                'name' => 'function',
              ),
              'xmlValue' => '',
              'xmlChildren' => 
              array (
                0 => 
                array (
                  'xmlName' => 'include',
                  'xmlAttributes' => 
                  array (
                    'template' => 'actFunction',
                  ),
                  'xmlValue' => '',
                  'xmlChildren' => 
                  array (
                  ),
                ),
                1 => 
                array (
                  'xmlName' => 'include',
                  'xmlAttributes' => 
                  array (
                    'template' => 'Array2XML',
                  ),
                  'xmlValue' => '',
                  'xmlChildren' => 
                  array (
                  ),
                ),
                2 => 
                array (
                  'xmlName' => 'include',
                  'xmlAttributes' => 
                  array (
                    'template' => 'actNetwork',
                  ),
                  'xmlValue' => '',
                  'xmlChildren' => 
                  array (
                  ),
                ),
              ),
            ),
            'access' => 'internal',
            'permissions' => '',
          ),
          'adodb' => 
          array (
            'xml' => 
            array (
              'xmlName' => 'fuseaction',
              'xmlAttributes' => 
              array (
                'name' => 'adodb',
              ),
              'xmlValue' => '',
              'xmlChildren' => 
              array (
              ),
            ),
            'access' => 'internal',
            'permissions' => '',
          ),
          'rssreader' => 
          array (
            'xml' => 
            array (
              'xmlName' => 'fuseaction',
              'xmlAttributes' => 
              array (
                'name' => 'rssreader',
              ),
              'xmlValue' => '',
              'xmlChildren' => 
              array (
                0 => 
                array (
                  'xmlName' => 'include',
                  'xmlAttributes' => 
                  array (
                    'template' => 'lastRSS',
                  ),
                  'xmlValue' => '',
                  'xmlChildren' => 
                  array (
                  ),
                ),
              ),
            ),
            'access' => 'internal',
            'permissions' => '',
          ),
        ),
        'prefuseaction' => 
        array (
          'xml' => 
          array (
            'xmlName' => 'prefuseaction',
            'xmlAttributes' => 
            array (
            ),
            'xmlValue' => '',
            'xmlChildren' => 
            array (
            ),
          ),
          'callsuper' => false,
        ),
        'postfuseaction' => 
        array (
          'xml' => 
          array (
          ),
          'callsuper' => false,
        ),
      ),
      'home' => 
      array (
        'path' => '../apps/home/',
        'parent' => '',
        'rootpath' => '../../site/',
        'xml' => 
        array (
          'xmlChildren' => 
          array (
            0 => 
            array (
              'xmlName' => 'circuit',
              'xmlAttributes' => 
              array (
                'access' => 'public',
              ),
              'xmlValue' => '',
            ),
          ),
        ),
        'timestamp' => 1366518291,
        'circuitTrace' => 
        array (
          0 => 'home',
        ),
        'access' => 'public',
        'permissions' => '',
        'fuseactions' => 
        array (
          'airport' => 
          array (
            'xml' => 
            array (
              'xmlName' => 'fuseaction',
              'xmlAttributes' => 
              array (
                'name' => 'airport',
              ),
              'xmlValue' => '',
              'xmlChildren' => 
              array (
                0 => 
                array (
                  'xmlName' => 'include',
                  'xmlAttributes' => 
                  array (
                    'template' => 'actAirPort',
                  ),
                  'xmlValue' => '',
                  'xmlChildren' => 
                  array (
                  ),
                ),
              ),
            ),
            'access' => 'public',
            'permissions' => '',
          ),
          'gateway' => 
          array (
            'xml' => 
            array (
              'xmlName' => 'fuseaction',
              'xmlAttributes' => 
              array (
                'name' => 'gateway',
              ),
              'xmlValue' => '',
              'xmlChildren' => 
              array (
                0 => 
                array (
                  'xmlName' => 'include',
                  'xmlAttributes' => 
                  array (
                    'template' => 'actGateway',
                  ),
                  'xmlValue' => '',
                  'xmlChildren' => 
                  array (
                  ),
                ),
              ),
            ),
            'access' => 'public',
            'permissions' => '',
          ),
        ),
        'prefuseaction' => 
        array (
          'xml' => 
          array (
            'xmlName' => 'prefuseaction',
            'xmlAttributes' => 
            array (
            ),
            'xmlValue' => '',
            'xmlChildren' => 
            array (
              0 => 
              array (
                'xmlName' => 'do',
                'xmlAttributes' => 
                array (
                  'action' => 'lib.function',
                ),
                'xmlValue' => '',
                'xmlChildren' => 
                array (
                ),
              ),
              1 => 
              array (
                'xmlName' => 'do',
                'xmlAttributes' => 
                array (
                  'action' => 'lib.adodb',
                ),
                'xmlValue' => '',
                'xmlChildren' => 
                array (
                ),
              ),
            ),
          ),
          'callsuper' => false,
        ),
        'postfuseaction' => 
        array (
          'xml' => 
          array (
          ),
          'callsuper' => false,
        ),
      ),
      'lay' => 
      array (
        'path' => '../apps/layout/',
        'parent' => '',
        'rootpath' => '../../site/',
        'xml' => 
        array (
          'xmlChildren' => 
          array (
            0 => 
            array (
              'xmlName' => 'circuit',
              'xmlAttributes' => 
              array (
                'access' => 'public',
              ),
              'xmlValue' => '',
            ),
          ),
        ),
        'timestamp' => 1366518291,
        'circuitTrace' => 
        array (
          0 => 'lay',
        ),
        'access' => 'public',
        'permissions' => '',
        'fuseactions' => 
        array (
          'main' => 
          array (
            'xml' => 
            array (
              'xmlName' => 'fuseaction',
              'xmlAttributes' => 
              array (
                'name' => 'main',
              ),
              'xmlValue' => '',
              'xmlChildren' => 
              array (
                0 => 
                array (
                  'xmlName' => 'include',
                  'xmlAttributes' => 
                  array (
                    'template' => 'dspMain',
                  ),
                  'xmlValue' => '',
                  'xmlChildren' => 
                  array (
                  ),
                ),
              ),
            ),
            'access' => 'public',
            'permissions' => '',
          ),
        ),
        'prefuseaction' => 
        array (
          'xml' => 
          array (
          ),
          'callsuper' => false,
        ),
        'postfuseaction' => 
        array (
          'xml' => 
          array (
          ),
          'callsuper' => false,
        ),
      ),
    ),
    'classes' => 
    array (
    ),
    'lexicons' => 
    array (
    ),
    'plugins' => 
    array (
    ),
    'pluginphases' => 
    array (
      'preProcess' => 
      array (
      ),
      'preFuseaction' => 
      array (
      ),
      'fuseactionException' => 
      array (
      ),
      'processError' => 
      array (
      ),
      'postFuseaction' => 
      array (
      ),
      'postProcess' => 
      array (
      ),
    ),
    'webrootdirectory' => '/Applications/XAMPP/xamppfiles/htdocs/pr/skyradar/v0.2/api/site/',
    'approotdirectory' => '/Applications/XAMPP/xamppfiles/htdocs/pr/skyradar/v0.2/api/core/',
    'rootdirectory' => '/Applications/XAMPP/xamppfiles/htdocs/pr/skyradar/v0.2/api/core/',
    'osdelimiter' => '/',
    'CoreToAppRootPath' => '../',
    'AppRootToCorePath' => '../',
    'CoreToWebRootPath' => '../../site/',
    'WebRootToCorePath' => '../../core/',
    'WebRootToAppRootPath' => '../core/',
    'parsePath' => 'parsed/',
    'pluginsPath' => 'plugins/',
    'lexiconPath' => 'lexicon/',
    'errortemplatesPath' => 'errortemplates/',
    'timestamp' => 1366518291,
    'fuseactionVariable' => 'fa',
    'defaultFuseaction' => 'home.main',
    'defaultFilename' => 'index',
    'precedenceFormOrUrl' => 'form',
    'mode' => 'development',
    'password' => 'secret',
    'parseWithComments' => false,
    'scriptLanguage' => 'php4',
    'scriptFileDelimiter' => 'php',
    'maskedFileDelimiters' => 'htm,cfm,cfml,php,php4,asp,aspx',
    'characterEncoding' => 'utf-8',
    'parseWithIndentation' => true,
    'conditionalParse' => true,
    'allowLexicon' => true,
    'ignoreBadGrammar' => true,
    'useAssertions' => false,
    'globalfuseactions' => 
    array (
      'preprocess' => 
      array (
        'xml' => 
        array (
          'xmlName' => 'preprocess',
          'xmlAttributes' => 
          array (
          ),
          'xmlValue' => '',
          'xmlChildren' => 
          array (
          ),
        ),
      ),
      'postprocess' => 
      array (
        'xml' => 
        array (
          'xmlName' => 'postprocess',
          'xmlAttributes' => 
          array (
          ),
          'xmlValue' => '',
          'xmlChildren' => 
          array (
          ),
        ),
      ),
    ),
    'parseRootPath' => '../',
    'dateLastLoaded' => 1366518291,
  ),
);
?>
