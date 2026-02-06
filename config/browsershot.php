<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Node Binary Path
    |--------------------------------------------------------------------------
    |
    | The path to the Node.js binary. If null, Browsershot will try to
    | find it automatically using 'which node' or 'where node'.
    |
    | Set this in your .env file as NODE_PATH
    |
    | On Windows: C:\Program Files\nodejs\node.exe
    |            or C:\Users\YourUser\.config\herd\bin\nvm\v24.0.1\node.exe
    | On Linux/Mac: /usr/bin/node or leave null to auto-detect
    | On VPS: Usually /usr/bin/node or /usr/local/bin/node
    |
    | Example .env:
    | NODE_PATH=C:\Program Files\nodejs\node.exe
    |
    */

    'node_path' => env('NODE_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | NPM Binary Path
    |--------------------------------------------------------------------------
    |
    | The path to the NPM binary. Usually not needed, but can be set if
    | Browsershot needs to install Puppeteer.
    |
    */

    'npm_path' => env('NPM_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | Chrome/Chromium Binary Path
    |--------------------------------------------------------------------------
    |
    | The path to the Chrome/Chromium binary. If null, Browsershot will try to
    | find it automatically.
    |
    | Set this in your .env file as BROWSERSHOT_CHROME_PATH
    |
    | On Windows: C:\Program Files\Google\Chrome\Application\chrome.exe
    |            or C:\Program Files (x86)\Google\Chrome\Application\chrome.exe
    | On Linux/Mac: /usr/bin/google-chrome or /usr/bin/chromium-browser
    | On VPS: Usually /usr/bin/google-chrome-stable or /usr/bin/chromium
    |
    | Example .env:
    | BROWSERSHOT_CHROME_PATH=C:\Program Files\Google\Chrome\Application\chrome.exe
    |
    */

    'chrome_path' => env('BROWSERSHOT_CHROME_PATH', null),

    /*
    |--------------------------------------------------------------------------
    | Chrome/Chromium Arguments
    |--------------------------------------------------------------------------
    |
    | Additional arguments to pass to Chrome/Chromium when generating PDFs.
    | --no-sandbox and --disable-setuid-sandbox are required on most Linux servers.
    |
    */

    'chrome_args' => env('CHROME_ARGS', '--no-sandbox --disable-setuid-sandbox'),

    /*
    |--------------------------------------------------------------------------
    | Chrome Config Home
    |--------------------------------------------------------------------------
    |
    | The path to Chrome's config directory. Only needed on VPS servers where
    | Chrome needs a specific config directory.
    |
    | Set this in your .env file as CHROME_CONFIG_HOME
    |
    | Example .env:
    | CHROME_CONFIG_HOME=/path/to/chrome/.config
    |
    */

    'chrome_config_home' => env('CHROME_CONFIG_HOME', null),

];
