# Installation
*  Clone or download the git repo.
```
cd project_dir
composer install
```

## Drush
- Runserver (PHP’s built-in http server for development): `drush runserver`
- Clear cache: `drush cc`
- Clear all cache: `drush cache-rebuid`
- Shows list of available modules & themes `drush pml`
- Run any pending database updates `drush updb`
- Enable a module: `drush pm:enable {name_of_module}`
- Disable a module: `drush pm:uninstall {name_of_module}`
- Check Drupal Composer packages for security updates: `drush pm:security`
- Check watchdog (logged events): `drush ws`
