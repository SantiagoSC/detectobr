# DETECTOBR (Detect Optional Before Required utility)

In php 8 declaring a function with an optional parameter before a required one results in a "Deprecated" warning which can break some views or API responses.

Its easy to resolve this issue when located, but in big old projects can be a nightmare to search this type of declarations in thousands of scripts.

This script helps to locate automatically all these declarations in a whole project.

## How to use

Simply place [detectobr.php](detectobr.php) in the root folder (or subfolder) of the project in which you want to scan files and then execute it with:

```
php -f detectobr.php
```

The script will scan all php files in the same folder and all subfolders and will notice you with all declarations that have optional parameters before required parameters, showing you the file and line where it occurs.

Then, you only need to resolve the deprecated code and scan again until nothing is found.

## Considerations

### Nullable parameters

This script respects nullable parameters as the interpreter does not show deprecated warning when nullable parameter with declared type preceeds a required parameter. So this declaration will be accepted as valid:
```
function (string $nullableParameter = null, $required)
```

### Test declarations

This script comes with three declarations to test the script, because it scans itself too.

You can play with this script by adding more test declarations inside it or creating any other script with your tests.
