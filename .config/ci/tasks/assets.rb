namespace :assets do
  task :install do
    run_locally do
      execute "cd #{fetch(:theme_folder)} && yarn install --frozen-lockfile"
    end
  end

  task :compile do
    run_locally do
      execute "cd #{fetch(:theme_folder)} && yarn build"
    end
  end

  task :sync do
    run_locally do
      host = primary(:app)
      execute :rsync,
        "-a --delete -e 'ssh -p #{host.port}'",
        "#{fetch(:theme_folder)}/public/build/ #{host.username}@#{host.hostname}:#{release_path}/#{fetch(:theme_folder)}/public/build/"
    end
  end
end
