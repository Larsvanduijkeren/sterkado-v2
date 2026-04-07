server "164.90.164.231", roles: %w{app}, name: "production", user: fetch(:deploy_user), port: 222

# login to remote server is done via ssh-key authentication
# never, ever with password
set :ssh_options, {
    keys: %w(~/.ssh/id_rsa),
    auth_methods: %w(publickey)
}
