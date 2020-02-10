<?php
namespace Deployer;

require 'recipe/laravel.php';

// Project name
set('application', 'bawantracking');

// Project repository
set('repository', 'git@github.com:mrofi/tracking.git');

// [Optional] Allocate tty for git clone. Default value is false.
set('git_tty', true);

// Shared files/dirs between deploys
add('shared_files', []);
add('shared_dirs', []);

// Writable dirs by web server
add('writable_dirs', []);
set('allow_anonymous_stats', false);

// Hosts

host('mager')
    ->set('deploy_path', '/var/www/html/{{application}}');

// Tasks

task('build', function () {
    run('cd {{release_path}} && build');
});

// [Optional] if deploy fails automatically unlock.
after('deploy:failed', 'deploy:unlock');

// Migrate database before symlink new release.

before('deploy:symlink', 'artisan:migrate');

