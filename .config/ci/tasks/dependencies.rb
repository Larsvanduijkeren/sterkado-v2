namespace :dependencies do
  desc "Install root composer deps locally (dotenv etc.)"
  task :install_root do
    run_locally do
      within fetch(:local_root) do
        execute "COMPOSER=.config/composer.json composer install --no-dev --prefer-dist --optimize-autoloader"
      end
    end
  end
  desc "Sync root vendor to server release"
  task :sync_root_vendor do
    run_locally do
      host = primary(:app)
      execute :rsync,
        "-a --delete -e 'ssh -p #{host.port}'",
        "vendor/ #{host.username}@#{host.hostname}:#{release_path}/vendor/"
    end
  end

  desc "Install Sage theme composer deps locally"
  task :install_theme do
    run_locally do
      execute "cd #{fetch(:theme_folder)} && composer install --no-dev --prefer-dist --optimize-autoloader"
    end
  end

  desc "Create theme paths on release (rsync needs parents; git archive omits ignored/empty dirs)"
  task :ensure_theme_release_dirs do
    on roles(:app) do
      theme = "#{release_path}/#{fetch(:theme_folder)}"
      execute :mkdir, "-p", "#{theme}/vendor", "#{theme}/public/build"
    end
  end

  desc "Sync Sage theme vendor to server release"
  task :sync_theme_vendor do
    run_locally do
      host = primary(:app)
      execute :rsync,
        "-a --delete -e 'ssh -p #{host.port}'",
        "#{fetch(:theme_folder)}/vendor/ #{host.username}@#{host.hostname}:#{release_path}/#{fetch(:theme_folder)}/vendor/"
    end
  end
end
