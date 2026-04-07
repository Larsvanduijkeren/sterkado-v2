# config valid for current version and patch releases of Capistrano
lock "~> 3.17.0"

set :application, "sterkado"
set :repo_url, "git@github.com:Larsvanduijkeren/sterkado-v2.git"
set :branch, "master"
set :deploy_user, "deploy"
set :deploy_to, "/var/www/html/sterkado"

set :theme_folder, "wp-content/themes/websheriff-sage"
set :local_root, File.expand_path('../../../', __dir__)

append :linked_files, ".env"
append :linked_dirs, "wp-content/uploads"

before "deploy:updated", "dependencies:install_root"
before "deploy:updated", "dependencies:install_theme"
before "deploy:updated", "assets:install"
before "deploy:updated", "assets:compile"

before "dependencies:sync_theme_vendor", "dependencies:ensure_theme_release_dirs"
before "assets:sync", "dependencies:ensure_theme_release_dirs"

after "deploy:updated", "dependencies:sync_root_vendor"
after "deploy:updated", "dependencies:sync_theme_vendor"
after "deploy:updated", "assets:sync"

after "deploy:updated", "verify:sage_autoload"

after "deploy:published", "permissions:fix"
