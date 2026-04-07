namespace :permissions do
  desc "Ensure writable permissions without sudo (assumes server already has correct ownership)"
  task :fix do
    on roles(:app) do
      # Make uploads group-writable; don't try to chown
      execute "chmod -R g+rwX #{shared_path}/wp-content/uploads"
      execute "find #{shared_path}/wp-content/uploads -type d -exec chmod 2775 {} \\;"
    end
  end
end

