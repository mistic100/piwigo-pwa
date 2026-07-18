# Piwigo PWA

A standalone PWA frontend for your Piwigo gallery.

WIP


## Installation

### Patch web-services

The PWA requires additional data from Piwigo web-services that currently cannot be obtained due to a lack of necessary triggers.

The `include/ws_core.inc.php` file must be manually edited to add a line:

```diff
      $result = call_user_func_array($method['callback'], array($params, &$this) );
+     $result = trigger_change('ws_result', $result, $methodName, $params);
```

> [!WARNING]
> This modification will be lost on every Piwigo update.

If you use `linuxserver/piwigo` Docker image you can automate it by using a [Custom Script](https://docs.linuxserver.io/general/container-customization/#custom-scripts).

```sh
#!/bin/bash

TARGET_FILE="/app/www/public/include/ws_core.inc.php"
SEARCH_STRING="call_user_func_array(\$method"
INSERT_STRING="\$result = trigger_change('ws_result', \$result, \$methodName, \$params);"

echo "**** Patching ws_core file ****"

if [ ! -f "$TARGET_FILE" ]; then
    echo "Error: Target file '$TARGET_FILE' not found."
    exit 1
fi

if grep -qF "$INSERT_STRING" "$TARGET_FILE"; then
    echo "File is already patched. Skipping."
    exit 0
fi

sed -i "/$(echo "$SEARCH_STRING" | sed 's/[.[\*^$]/\\&/g')/a \\  $INSERT_STRING" "$TARGET_FILE"

echo "File patched successfully."
