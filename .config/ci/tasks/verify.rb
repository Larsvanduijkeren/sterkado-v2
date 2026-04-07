namespace :verify do
  task :sage_autoload do
    on roles(:app) do
      execute "test -f #{release_path}/#{fetch(:theme_folder)}/vendor/autoload.php"
    end
  end
end
