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

# Customizing Theme
This website uses Bootstrap Barrio as a base theme and the wid theme is a bootstrap_sass extension of it.
If any theme changes is needed to be done, follow steps below
`cd web/themes/custom/wid`
- Edit the gulpfile.js and change the proxy to your server URL followed by installing dependencies and creating js,css.
`````
browserSync.init({
    proxy: 'http://yourdomain.com',
})
yarn
yarn gulp
`````
