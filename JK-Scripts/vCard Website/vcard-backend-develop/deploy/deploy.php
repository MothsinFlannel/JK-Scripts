<?php

require __DIR__ . '/vendor/voodoo-rocks/deployer/recipe/yii2-app-vm.php';

set('shared_dirs', [
    'runtime',
    'flavors',
    'web/assets',
    'web/uploads',
]);

set('keep_releases', 1);

env('project', 'vcard-backend');
env('composer_options', 'install --prefer-dist --optimize-autoloader --no-progress --no-interaction');
set('repository', 'git@gitlab.com:voodoo-rocks/{{project}}.git');

task('rbac:reload', function () {
    run('php {{release_path}}/yii migrate --migrationPath=@yii/rbac/migrations/ --interactive=0');
    run('php {{release_path}}/yii rbac/reload');
});

task('cache:clear', function () {
    run('php {{release_path}}/yii cache/flush-all --interactive=0');
    run('php {{release_path}}/yii cache/flush-schema --interactive=0');
});

task('queue:consume', function () {
    run('screen -S vcard.service -dm php {{release_path}}/yii queue/consume');
    writeln("<info>Job queue started</info>");
});

before('rbac:reload', 'cache:clear');

after('deploy:run_migrations', 'rbac:reload');
//after('success', 'queue:consume');

server('vcard.voodoo.work', 'vcard.voodoo.work', 22)
    ->user('ubuntu')
    ->identityFile()
    ->stage('develop')
    ->env('deploy_path', '/home/ubuntu/{{project}}');

server('vcard.voodoo.services', 'vcard.voodoo.services', 22)
    ->user('ubuntu')
    ->identityFile()
    ->stage('staging')
    ->env('deploy_path', '/home/ubuntu/{{project}}');

server('app.vcardsys.com', 'app.vcardsys.com', 22)
    ->user('ubuntu')
    ->identityFile()
    ->stage('production')
    ->env('deploy_path', '/home/ubuntu/{{project}}');