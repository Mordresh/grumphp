# PHPLint

The PHPLint task will check your source files for syntax errors.

```yaml
# grumphp.yml
parameters:
    tasks:
        phplint:
            exclude: []
            jobs: ~
            triggered_by: ['php', 'phtml', 'php3', 'php4', 'php5']
```
**exclude**

*Default: []*

Any directories to be excluded from linting. You can specify which
directories you wish to exclude, such as the vendor directory.

**jobs**

*Default: null*

The number of jobs you wish to use for parallel processing. If no number
is given, it is left up to parallel-lint itself, which currently
defaults to 10.

**trigered_by**

*Default: ['php', 'phtml', 'php3', 'php4', 'php5']*

Any file extensions that you wish to be passed to the linter.
